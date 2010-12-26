<?php

/**
 * StringFormatter - simple, but powerful string formatting
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.1
 * @copyright Copyright (c) 2010, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 */

/**
 * StringFormatter - simple, but powerful string formatting
 *
 * Recognized patterns:
 *  * simple replacement:
 *      $f = new StringFormatter ('{} {}!');
 *      echo $f->parse ('Hello', 'world'); # Hello world!
 *
 *  * simple replacement with strict sequence:
 *      $f = new StringFormatter ('{1} {0}!');
 *      echo $f->parse ('world', Hello'); # Hello world!
 *
 *  * text alignment:
 *      * left:
 *          $f = new StringFormatter ('{} "{1:<20}"');
 *          echo $f->parse ('Hello', 'world'); # Hello "world               "
 *      * right:
 *          $f = new StringFormatter ('{} "{1:>20}"');
 *          echo $f->parse ('Hello', 'world'); # Hello "               world"
 *      * center:
 *          $f = new StringFormatter ('{} "{1:^20}"');
 *          echo $f->parse ('Hello', 'world'); # Hello "       world        "
 *  * text alignment with specified character:
 *      $f = new StringFormatter ('{} "{1:*^20}"');
 *      echo $f->parse ('Hello', 'world'); # Hello "*******world********"
 *  * sprintf-like formatting:
 *      $f = new StringFormatter ('Test: {:%.3f}');
 *      echo $f->parse (2.1234567); # Test: 2.123
 *      $f = new StringFormatter ('Test 2: {:%c}');
 *      echo $f->parse (97); # Test2: a
 *  * call object method or get object property:
 *      $f = new StringFormatter ('Test: {0->method} {->property}');
 *      class TestStringFormatter {
 *          public $property = 'test property';
 *          public function method () {
 *              return 'test method';
 *          }
 *      }
 *      echo $f->parse (new TestStringFormatter (), new TestStringFormatter ()); # Test: test method test property
 *  * convert int to other base:
 *      $f = new StringFormatter ('Test: 10: {#d}, 16: {0#x}, 2: {0#b}');
 *      echo $f->parse (11); # Test: 10: 11, 16: b, 2: 1011
 *
 *      Available bases:
 *          * b - binary
 *          * o - octal
 *          * d - decimal
 *          * x - hex (small letters)
 *          * X - hex (big letters)
 *  * array indexes:
 *      $f = new StringFormatter ('Test: test1: {[test1]}, test2: {0[test2]}');
 *      echo $f->parse (array ('test1' => 'Hello', 'test2' => 'world')); # Test: test1: Hello, test2: world
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.1
 * @copyright Copyright (c) 2010, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 */
class StringFormatter {
    /**
     * Matrix for mapping string suffixes to values provided for base_convert function
     *
     * @var array
     */
    private $matrix__base_convert = array (
        'b' =>  2,
        'o' =>  8,
        'd' => 10,
        'x' => 16,
        'X' => 16
    );

    /**
     * Matrix for mapping string suffixes to values provided for str_pad function
     *
     * @var array
     */
    private $matrix__str_pad = array (
        '<' => STR_PAD_LEFT,
        '>' => STR_PAD_RIGHT,
        '^' => STR_PAD_BOTH,
    );
    /**
     * Store provided by user format string
     *
     * @var string
     */
    private $format = null;

    /**
     * Given for StringFormatter::parse parameters
     *
     * @var array
     */
    private $params = array ();

    /**
     * Pointer for accessing given elements when no placeholder in format is given
     *
     * @var int
     */
    private $pointer = 0;

    /**
     * Regular expression for finding tokens in format
     *
     * @var string
     */
    private $rxp_token = '
        /
        \{              # opening brace
            (
                [^}]*   # all but closing brace
            )
        \}              # closing brace
    /x';

    /**
     * Constructor
     *
     * @param string $format format to parse
     */
    public function __construct ($format) {
        $this->format = $format;
    }

    /**
     * Helper function for find current param
     *
     * @param int parameter index (optional)
     * @return mixed
     */
    private function get_param ($key = '') {
        if ($key == '') {
            $key = $this->pointer++;
        }

        return $this->params[$key];
    }

    /**
     * Callback for preg_replace_callback - here is doing all magic with replacing format token with
     * proper values from given arguments in StringFormatter::parse method.
     *
     * @param string matched token data
     * @return string
     */
    private function format_callback ($data) {
        if (count ($data) < 2) {
            return $data[0];
        }

        ## simple auto or exact placeholder
        if ($data[1] == '' || is_numeric ($data[1])) {
            return $this->get_param ($data[1]);
        }

        ## text alignment
        else if (preg_match ('
            /
            ^
                (\d*)       # placeholder
                :           # explicit colon
                (.)?        # pad character
                ([<>^])     # alignment
                (\d+)       # pad length
            $
            /x', $data[1], $match)
        ) {
            return str_pad (
                $this->get_param ($match[1]),
                $match[4],
                ($match[2] ? $match[2] : ' '),
                $this->matrix__str_pad[$match[3]]
            );
        }

        ## sprintf pattern
        else if (preg_match ('
            /
            ^
                (\d*)   # placeholder
                :       # explicit colon
                (.*)    # sprintf pattern
            $
            /x', $data[1], $match)
        ) {
            return sprintf ($match[2], $this->get_param ($match[1]));
        }

        ## call object method or get object property
        else if (preg_match ('
            /
            ^
                (\d*)   # placeholder
                ->      # explicit arrow
                (\w+)   # keyword (field or method name)
            $
            /x', $data[1], $match)
        ) {
            $param = $this->get_param ($match[1]);
            if (method_exists ($param, $match[2])) {
                return call_user_func (array ($param, $match[2]));
            }
            else {
                return $param->$match[2];
            }
        }

        ## converting to other base
        else if (preg_match ('/
            ^
            (\d*)       # placeholder
            [#]         # explicit hash
            ([dxXob])   # base shortcut
            $
            /x', $data[1], $match)
        ) {
            $ret = base_convert (
                (int) $this->get_param ($match[1]),
                10,
                $this->matrix__base_convert[$match[2]]
            );
            if ($match[2] == 'X') {
                $ret = strtoupper ($ret);
            }
            return $ret;
        }

        ## array index
        else if (preg_match ('/
            ^
                (\d*)       # placeholder
                \[          # opening square bracket
                    (\w+)   # key
                \]          # closing square bracket
            $/x', $data[1], $match)
        ) {
            $ret = $this->get_param ($match[1]);
            return $ret[$match[2]];
        }

        ## unknown token type
        else {
            return $data[0];
        }
    }

    /**
     * Main StringFormatter method - call it with series of argument to create formatted string.
     *
     * @param mixed arg,... parameters used to format given string
     * @return string
     */
    public function parse () {
        $this->params = func_get_args ();
        return preg_replace_callback ($this->rxp_token, array ($this, 'format_callback'), $this->format);
    }

}

