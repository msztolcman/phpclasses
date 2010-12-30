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
 *      echo $f->parse ('world', 'Hello'); # Hello world!
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
 *      $f = new StringFormatter ('Test: 10: {#10}, 16: {0#16}, 2: {0#2}, 7: {0#7}');
 *      echo $f->parse (11); # Test: 10: 11, 16: b, 2: 1011, 7: 14
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
    const MODE_NORMAL   = 1;
    const MODE_NAMED    = 2;

    /**
     * Matrix for mapping string suffixes to values provided for base_convert function
     *
     * @var array
     */
    protected static $matrix__base_convert = array (
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
    protected static $matrix__str_pad = array (
        '<' => STR_PAD_RIGHT,
        '>' => STR_PAD_LEFT,
        '^' => STR_PAD_BOTH,
    );

    /**
     * Regular expressions for key used in template.
     *
     * Key must be one of StringFormatter::MODE_* constant, and value is regular expression used to find key in tokens
     *
     * @var array
     */
    protected static $rxp_keys = array (
        self::MODE_NORMAL   => '\d*',
        self::MODE_NAMED    => '\w+',
    );

    /**
     * Regular expression for finding tokens in format
     *
     * @var string
     */
    protected static $rxp_token = '
        /
        \{              # opening brace
            (
                [^}]*   # all but closing brace
            )
        \}              # closing brace
    /x';

    /**
     * Store provided by user format string
     *
     * @var string
     */
    protected $format = null;

    /**
     * Mode we are run
     *
     * @var int one of: StringFormatter::MODE_NORMAL, StringFormatter::MODE_NAMED
     */
    protected $mode = self::MODE_NORMAL;

    /**
     * Given for StringFormatter::parse parameters
     *
     * @var array
     */
    protected $params = array ();

    /**
     * Pointer for accessing given elements when no placeholder in format is given
     *
     * @var int
     */
    protected $pointer = 0;

    /**
     * Constructor
     *
     * @param string $format format to parse
     */
    public function __construct ($format) {
        $this->format = $format;
    }

    /**
     * Helper function - test for existence of key in given parameters
     *
     * @param string|int key
     * @return bool
     */
    protected function has_key ($key) {
        return ($this->mode == self::MODE_NORMAL && $key == '') || isset ($this->params[$key]);
    }

    /**
     * Helper function for find current param
     *
     * @param int parameter index (optional)
     * @return mixed
     */
    protected function get_param ($key = '') {
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
    protected function format_callback ($data) {
        if (count ($data) < 2) {
            return $data[0];
        }

        ## simple auto or explicit placeholder
        if ($this->mode == self::MODE_NORMAL && $this->has_key ($data[1])) {
            return $this->get_param ($data[1]);
        }

        ## simple named, explicit placeholder
        else if ($this->mode == self::MODE_NAMED && strlen ($data[1]) > 0 && $this->has_key ($data[1])) {
            return $this->get_param ($data[1]);
        }

        ## text alignment
        else if (preg_match ('
            /
            ^
                ('. self::$rxp_keys[$this->mode] .')    # placeholder
                :                                       # explicit colon
                (.)?                                    # pad character
                ([<>^])                                 # alignment
                (\d+)                                   # pad length
            $
            /x', $data[1], $match) &&
            $this->has_key ($match[1])
        ) {
            return str_pad (
                $this->get_param ($match[1]),
                $match[4],
                (strlen ($match[2]) > 0 ? $match[2] : ' '),
                self::$matrix__str_pad[$match[3]]
            );
        }

        ## sprintf pattern
        else if (preg_match ('
            /
            ^
                ('. self::$rxp_keys[$this->mode] .')    # placeholder
                %                                       # explicit percent
                (.*)                                    # sprintf pattern
            $
            /x', $data[1], $match) &&
            $this->has_key ($match[1])
        ) {
            return vsprintf ($match[2], $this->get_param ($match[1]));
        }

        ## call object method or get object property
        else if (preg_match ('
            /
            ^
                ('. self::$rxp_keys[$this->mode] .')    # placeholder
                ->                                      # explicit arrow
                (\w+)                                   # keyword (field or method name)
            $
            /x', $data[1], $match) &&
            $this->has_key ($match[1])
        ) {
            $param = $this->get_param ($match[1]);
            if (method_exists ($param, $match[2])) {
                return call_user_func (array ($param, $match[2]));
            }
            else if (property_exists ($param, $match[2])) {
                return $param->$match[2];
            }
            else if (in_array ('__call', get_class_methods ($param))) {
                return call_user_func (array ($param, $match[2]));
            }
            else if (in_array ('__get', get_class_methods ($param))) {
                return $param->$match[2];
            }
            else {
                return $data[0];
            }
        }

        ## converting int to other base
        else if (preg_match ('
            /
            ^
            ('. self::$rxp_keys[$this->mode] .')    # placeholder
            [#]                                     # explicit hash
            ([dxXob]|\d\d?)                         # base shortcut
            $
            /x', $data[1], $match) &&
            $this->has_key ($match[1])
        ) {
            $ret = base_convert (
                (int) $this->get_param ($match[1]),
                10,
                (
                    is_numeric ($match[2])
                        ? $match[2]
                        : self::$matrix__base_convert[$match[2]]
                )
            );
            if ($match[2] == 'X') {
                $ret = strtoupper ($ret);
            }
            return $ret;
        }

        ## array index
        else if (preg_match ('
            /
            ^
                ('. self::$rxp_keys[$this->mode] .')    # placeholder
                \[                                      # opening square bracket
                    (\w+)                               # key
                \]                                      # closing square bracket
            $
            /x', $data[1], $match) &&
            $this->has_key ($match[1])
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
        $this->mode = self::MODE_NORMAL;

        $this->params = func_get_args ();
        return preg_replace_callback (self::$rxp_token, array ($this, 'format_callback'), $this->format);
    }

    /**
     * Main StringFormatter method - call it with one array argument, when want to use named parameters in your template.
     *
     * Keys in given array must correspond with parameters in template.
     *
     * @param array arg parameters used to format given string
     * @return string
     */
    public function parseNamed (array $args) {
        $this->mode = self::MODE_NAMED;

        $this->params = $args;
        return preg_replace_callback (self::$rxp_token, array ($this, 'format_callback'), $this->format);
    }

}

