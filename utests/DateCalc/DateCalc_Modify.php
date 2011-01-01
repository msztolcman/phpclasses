<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../DateCalc.php';

class DateCalc_Modify extends UnitTestCase {
    const TS = 346299333; ## 1980-12-22 03:15:33
    const DATE2 = '22/12/80 03/15/33';
    const DATE2_ADD1WEEK = '29/12/80 03/15/33';
    const DATE2_SUB1WEEK = '15/12/80 03/15/33';
    const DATE2_ADD2DAYS7HOURS = '24/12/80 10/15/33';
    const DATE2_SUB2DAYS7HOURS = '19/12/80 20/15/33';
    const DATE2_200 = '22/12/80 03/18/53';
    const DATE2_1DAY310 = '23/12/80 03/20/43';
    const DATE2_SUB1DAY310 = '21/12/80 03/10/23';
    const DATE2_ADD2WEEKS = '05/01/81 03/15/33';
    const DATE2_SUB2WEEKS = '08/12/80 03/15/33';
    const FORMAT2 = '%d/%m/%y %H/%M/%S';

    public function __construct () {
        parent::__construct ('modify dates');
    }

    public function testAdd1Week () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('+1 week');

        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts + 86400 * 7);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_ADD1WEEK);
    }

    public function testSub1Week () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('-1 week');

        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts - 86400 * 7);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_SUB1WEEK);
    }

    public function testAdd2Days7Hours () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('+2 days 7 hours');

        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts + 86400 * 2 + 3600 * 7);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_ADD2DAYS7HOURS);
    }

    public function testSub2Days7Hours () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('-2 days 7 hours');

        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts - 86400 * 2 - 3600 * 7);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_SUB2DAYS7HOURS);
    }

    public function test200 () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('200');
        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts + 200);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_200);
    }

    public function test1Day310 () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('1 day 310');
        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts + 86400 + 310);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_1DAY310);
    }

    public function testSub1Day310 () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('-1 day 310');
        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts - 86400 - 310);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_SUB1DAY310);
    }

    public function testAdd2Weeks () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('+2 weeks');
        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts + 86400 * 14);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_ADD2WEEKS);
    }

    public function testSub2Weeks () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $dc->modify ('-2weeks');
        $this->assertNotIdentical ($dc->getTimestamp (), self::TS);
        $this->assertNotIdentical ($dc->getTimestamp (), $ts);
        $this->assertNotIdentical ($dc->getDateTime (), $dt);

        $this->assertIdentical ($dc->getTimestamp (), $ts - 86400 * 14);
        $this->assertIdentical ($dc->getFormatted (self::FORMAT2), self::DATE2_SUB2WEEKS);
    }

    public function testAddInvalidValue () {
        $dc = new DateCalc (self::DATE2, self::FORMAT2);

        $ts = $dc->getTimestamp ();
        $dt = $dc->getDateTime ();

        $this->expectException ('InvalidArgumentException');
        $dc->modify ('-2 dupy 7 hhurs');
    }

}

