<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/simpletest/autorun.php';
require_once dirname (dirname (__FILE__)) .'/StringFormatter.php';

class StringFormatter_Normal_ConvertInt extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: normal, case: convert int to other base');
    }

    public function testDecToNamedBin () {
        $sf = new StringFormatter ('test: "{0#b}"');
        $this->assertEqual ($sf->parse (123), 'test: "1111011"');
    }

    public function testDecToNamedOct () {
        $sf = new StringFormatter ('test: "{0#o}"');
        $this->assertEqual ($sf->parse (123), 'test: "173"');
    }

    public function testDecToNamedDec () {
        $sf = new StringFormatter ('test: "{0#d}"');
        $this->assertEqual ($sf->parse (123), 'test: "123"');
    }

    public function testDecToNamedHexSmall () {
        $sf = new StringFormatter ('test: "{0#x}"');
        $this->assertEqual ($sf->parse (123), 'test: "7b"');
    }

    public function testDecToNamedHexBig () {
        $sf = new StringFormatter ('test: "{0#X}"');
        $this->assertEqual ($sf->parse (123), 'test: "7B"');
    }

    public function testDecToUnnamedBin () {
        $sf = new StringFormatter ('test: "{0#2}"');
        $this->assertEqual ($sf->parse (123), 'test: "1111011"');
    }

    public function testDecToUnnamedOct () {
        $sf = new StringFormatter ('test: "{0#8}"');
        $this->assertEqual ($sf->parse (123), 'test: "173"');
    }

    public function testDecToUnnamedDec () {
        $sf = new StringFormatter ('test: "{0#10}"');
        $this->assertEqual ($sf->parse (123), 'test: "123"');
    }

    public function testDecToUnnamedHex () {
        $sf = new StringFormatter ('test: "{0#16}"');
        $this->assertEqual ($sf->parse (123), 'test: "7b"');
    }



    public function testBinToNamedDec () {
        $sf = new StringFormatter ('test: "{0#2#d}"');
        $this->assertEqual ($sf->parse ('1111011'), 'test: "123"');
    }

    public function testOctToNamedDec () {
        $sf = new StringFormatter ('test: "{0#8#d}"');
        $this->assertEqual ($sf->parse ('173'), 'test: "123"');
    }

    public function testDecToNamedDec2 () {
        $sf = new StringFormatter ('test: "{0#10#d}"');
        $this->assertEqual ($sf->parse ('123'), 'test: "123"');
    }

    public function testHexSmallToNamedDec () {
        $sf = new StringFormatter ('test: "{0#16#d}"');
        $this->assertEqual ($sf->parse ('7b'), 'test: "123"');
    }

    public function testHexBigToNamedDec () {
        $sf = new StringFormatter ('test: "{0#16#d}"');
        $this->assertEqual ($sf->parse ('7B'), 'test: "123"');
    }

    public function testBinToUnnamedDec () {
        $sf = new StringFormatter ('test: "{0#2#10}"');
        $this->assertEqual ($sf->parse ('1111011'), 'test: "123"');
    }

    public function testOctToUnnamedDec () {
        $sf = new StringFormatter ('test: "{0#8#10}"');
        $this->assertEqual ($sf->parse ('173'), 'test: "123"');
    }

    public function testDecToUnnamedDec2 () {
        $sf = new StringFormatter ('test: "{0#10#10}"');
        $this->assertEqual ($sf->parse ('123'), 'test: "123"');
    }

    public function testHexSmallToUnnamedDec () {
        $sf = new StringFormatter ('test: "{0#16#10}"');
        $this->assertEqual ($sf->parse ('7b'), 'test: "123"');
    }

    public function testHexBigToUnnamedDec () {
        $sf = new StringFormatter ('test: "{0#16#10}"');
        $this->assertEqual ($sf->parse ('7B'), 'test: "123"');
    }



    public function testDecToUnnamed7 () {
        $sf = new StringFormatter ('test: "{0#7}"');
        $this->assertEqual ($sf->parse (123), 'test: "234"');
    }

    public function testDecToUnnamed23 () {
        $sf = new StringFormatter ('test: "{0#23}"');
        $this->assertEqual ($sf->parse (123), 'test: "58"');
    }

}

