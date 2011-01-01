<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../DateCalc.php';

class DateCalc_Simple extends UnitTestCase {
    const TS = 346299333; ## 1980-12-22 03:15:33
    const DATE1 = '1980-12-22 03:15:33';
    const DATE2 = '22/12/80 03/15/33';
    const FORMAT2 = '%d/%m/%y %H/%M/%S';

    public function __construct () {
        parent::__construct ('simple date parsing and creating object');
    }

    public function testCreateInvalidFormat () {
        $this->expectException ('InvalidArgumentException');
        $dc = new DateCalc (self::DATE1, '%dupa');
    }

    public function testCreateEmpty () {
        $dc = new DateCalc ();
        $t = time ();
        $this->assertIsA ($dc, 'DateCalc');

        $this->assertWithinMargin ($t, $dc->getDateTime (), 2);
    }

    public function testCreateDefault () {
        $dc = new DateCalc (DateCalc::DATE_NOW);
        $t = time ();
        $this->assertIsA ($dc, 'DateCalc');

        $this->assertWithinMargin ($t, $dc->getDateTime (), 2);
    }

    public function testCreateSpecifiedTimestamp () {
        $dc = new DateCalc (self::TS);
        $this->assertIsA ($dc, 'DateCalc');

        $this->assertEqual (self::TS, $dc->getDateTime ());
        $this->assertEqual (self::TS, $dc->getTimestamp ());
        $this->assertIdentical (33, $dc->getSecond ());
        $this->assertIdentical (15, $dc->getMinute ());
        $this->assertIdentical (3, $dc->getHour ());
        $this->assertIdentical (22, $dc->getDay ());
        $this->assertIdentical (12, $dc->getMonth ());
        $this->assertIdentical (1980, $dc->getYear ());
        $this->assertIdentical (1, $dc->getWeekday ());
        $this->assertIdentical (356, $dc->getYearday ());
    }

    public function testCreateSpecifieDateDefaultFormat () {
        $dc = new DateCalc (self::DATE1);
        $this->assertIsA ($dc, 'DateCalc');

        $this->assertEqual (self::DATE1, $dc->getDateTime ());
        $this->assertEqual (self::TS, $dc->getTimestamp ());
        $this->assertIdentical (33, $dc->getSecond ());
        $this->assertIdentical (15, $dc->getMinute ());
        $this->assertIdentical (3, $dc->getHour ());
        $this->assertIdentical (22, $dc->getDay ());
        $this->assertIdentical (12, $dc->getMonth ());
        $this->assertIdentical (1980, $dc->getYear ());
        $this->assertIdentical (1, $dc->getWeekday ());
        $this->assertIdentical (356, $dc->getYearday ());
    }

    public function testCreateSpecifieDateSpecifiedFormat () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);
        $this->assertIsA ($dc, 'DateCalc');

        $this->assertEqual (self::DATE2, $dc->getDateTime ());
        $this->assertEqual (self::TS, $dc->getTimestamp ());
        $this->assertIdentical (33, $dc->getSecond ());
        $this->assertIdentical (15, $dc->getMinute ());
        $this->assertIdentical (3, $dc->getHour ());
        $this->assertIdentical (22, $dc->getDay ());
        $this->assertIdentical (12, $dc->getMonth ());
        $this->assertIdentical (1980, $dc->getYear ());
        $this->assertIdentical (1, $dc->getWeekday ());
        $this->assertIdentical (356, $dc->getYearday ());

        $this->assertIdentical (self::DATE2, $dc->getOriginalDate ());
        $this->assertIdentical (self::FORMAT2, $dc->getOriginalFormat ());
    }

}

