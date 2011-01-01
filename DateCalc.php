<?php

/**
 * DateCalc - calculations on date and time
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.1
 * @copyright Copyright (c) 2010, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 */

/**
 * DateCalc - calculations on date and time
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.1
 * @copyright Copyright (c) 2010, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 */
class DateCalc {
    const DATE_NOW = -1;

    /**
     * Array with recognized units in {@link DateCalc::calculate()} or in {@link DateCalc::modify()}
     *
     * @var array
     */
    private static $calculate_units = array (
        'sec', 'secs', 'second', 'seconds',
        'min', 'mins', 'minute', 'minutes',
        'hour', 'hours',
        'day', 'days',
        'month', 'months',
        'year', 'years',
        'weeks',
    );

    /**
     * Current values of DateCalc object
     *
     * @var array
     */
    protected $datetime             = array ();

    /**
     * Original datetime given by user
     *
     * $date argument in constructor
     *
     * @var string|int
     */
    protected $datetime_original    = null;

    /**
     * Original format given by user
     *
     * $format argument to constructor
     *
     * @var string|int
     */
    protected $datetime_format      = null;

    /**
     * Dumper.
     *
     * @internal
     * @ignore
     * @param mixed arg,...
     */
    private static function D () {
        $args = func_get_args ();
        echo '<pre>';
        foreach ($args as $k=>$arg) {
            echo ($k+1).'. '.print_r ($arg, 1) . "\n";
        }
        echo '</pre>';
    }

    /**
     * Convert datetime array to timestamp
     *
     * @param array datetime
     * @return int
     */
    private static function tsFromDatetime ($datetime) {
        return mktime (
            $datetime['hour'],
            $datetime['minute'],
            $datetime['second'],
            $datetime['month'],
            $datetime['day'],
            $datetime['year']
        );
    }

    /**
     * Convert timestamp to datetime array
     *
     * @param int ts
     * @param array datetime if given, function will modify this array adding or replacing some keys
     * @return array datetime array
     */
    private static function datetimeFromTs ($ts, &$datetime = null) {
        $ret = array ();
        list (
            $ret['year'],
            $ret['month'],
            $ret['day'],
            $ret['hour'],
            $ret['minute'],
            $ret['second']
        ) = explode ('-', strftime ('%Y-%m-%d-%H-%M-%S', $datetime));

        if (!is_null ($datetime)) {
            $datetime['year']       = $ret['year'];
            $datetime['month']      = $ret['month'];
            $datetime['day']        = $ret['day'];
            $datetime['hour']       = $ret['hour'];
            $datetime['minute']     = $ret['minute'];
            $datetime['second']     = $ret['second'];
        }

        return $ret;
    }

    /**
     * Parse date to datetime array.
     * It use strptime () to parse date, so parseDate is used as this function (same arguments).
     *
     * @param string|int date
     * @param string format
     * @return array
     */
    public static function parseDate ($date, $format) {
        $datetime = strptime ($date, $format);
        if ($datetime === false) {
            return;
        }

        $datetime = array (
            'year'      => $datetime['tm_year'] + 1900,
            'month'     => $datetime['tm_mon'] + 1,
            'day'       => $datetime['tm_mday'],
            'hour'      => $datetime['tm_hour'],
            'minute'    => $datetime['tm_min'],
            'second'    => $datetime['tm_sec'],
            'weekday'   => $datetime['tm_wday'],
            'yearday'   => $datetime['tm_yday'],
        );

        $datetime['ts'] = self::tsFromDatetime ($datetime);

        ## dirty hack for strptime: on some systems there is no automatically calculated tm_yday and tm_wday. But when we parse from
        ## timestamp, or few other tokens, it it calculated... so we slow down a little, but have always this data on this systems too...
        ## source: http://www.php.net/manual/en/function.strptime.php#89239
        if ($datetime['yearday'] == 0 && $datetime['weekday'] == 0) {
            $tmp = strptime ($datetime['ts'], '%s');
            $datetime['yearday'] = $tmp['tm_yday'];
            $datetime['weekday'] = $tmp['tm_wday'];
        }

        return $datetime;
    }

    /**
     * Constructor
     * Parse given date and set datetime array
     *
     * @param string|int date DateCalc::DATE_NOW, timestamp or date
     * @param string format ignored if date is int or DateCalc::DATE_NOW, in any other case must be a format given to strptime
     */
    public function __construct ($date = self::DATE_NOW, $format='%Y-%m-%d %H:%M:%S') {
        if ($date == self::DATE_NOW) {
            $date = time ();
            $format = '%s';
        }
        else if (is_int ($date)) {
            $format = '%s';
        }

        $this->datetime_original    = $date;
        $this->datetime_format      = $format;

        $this->datetime = self::parseDate ($date, $format);
        if (is_null ($this->datetime)) {
            throw new InvalidArgumentException ("Unrecognized format: $format");
        }
    }

    /**
     * Calculate current state with new value, and return new DateCalc object with new values.
     *
     * @param string value how to recalculate date
     * @return DateCalc new object with modified value
     * @see DateCalc::modify()
     */
    public function calculate ($value) {
        $value = trim ($value);

        if (!preg_match_all ('
            /
                ([+-])?             # sign
                \s*
                (\d+)               # quant
                \s*
                (\w*)               # unit
            /x', $value, $match, PREG_SET_ORDER
        )) {
            throw new InvalidArgumentException ("Incorrect pattern to calculate: $value");
        }

        $ret        = '';
        $prev_sign  = '';
        foreach ($match as &$data) {
            if (!$data[1]) {
                $data[1] = $prev_sign ? $prev_sign : '+';
            }
            $prev_sign = $data[1];

            if (!$data[3]) {
                $data[3] = ' seconds';
            }
            else if (!in_array ($data[3], self::$calculate_units)) {
                throw new InvalidArgumentException ("Incorrect pattern to calculate: $value");
            }

            $ret .= "$data[1]$data[2] $data[3]";
        }

        $ret = strtotime ($ret, $this->datetime['ts']);
        return new DateCalc ($ret);
    }

    /**
     * Change current state to the new value, and return itself
     *
     * @param string value how to recalculate date
     * @return DateCalc
     */
    public function modify ($value) {
        $dc = $this->calculate ($value);
        $this->datetime = $dc->datetime;
        return $this;
    }

    /**
     * Return current datetime in same format used when create object
     *
     * @return string
     */
    public function getDateTime () {
        return strftime ($this->datetime_format, $this->datetime['ts']);
    }

    /**
     * Return current datetime in given format
     *
     * @return string
     */
    public function getFormatted ($format) {
        return strftime ($format, $this->datetime['ts']);
    }

    /**
     * Return original datetime (same as given in constructor)
     *
     * @return string|int
     */
    public function getOriginalDate () {
        return $this->datetime_original;
    }

    /**
     * Return original format (same as given in constructor)
     *
     * @return string
     */
    public function getOriginalFormat () {
        return $this->datetime_format;
    }

    /**
     * Return current value as timestamp
     *
     * @return int
     */
    public function getTimestamp () {
        return $this->datetime['ts'];
    }

    /**
     * Return current seconds
     *
     * @return int
     */
    public function getSecond () {
        return $this->datetime['second'];
    }

    /**
     * Return current minutes
     *
     * @return int
     */
    public function getMinute () {
        return $this->datetime['minute'];
    }

    /**
     * Return current hours
     *
     * @return int
     */
    public function getHour () {
        return $this->datetime['hour'];
    }

    /**
     * Return current days
     *
     * @return int
     */
    public function getDay () {
        return $this->datetime['day'];
    }

    /**
     * Return current months
     *
     * @return int
     */
    public function getMonth () {
        return $this->datetime['month'];
    }

    /**
     * Return current years
     *
     * @return int
     */
    public function getYear () {
        return $this->datetime['year'];
    }

    /**
     * Return current weekday number (0 - Sunday, 6 - Saturday)
     *
     * @return int
     */
    public function getWeekday () {
        return $this->datetime['weekday'];
    }

    /**
     * Return current yearday number (0-366)
     *
     * @return int
     */
    public function getYearday () {
        return $this->datetime['yearday'];
    }

}

