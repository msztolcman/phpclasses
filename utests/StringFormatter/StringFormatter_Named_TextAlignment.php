<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../StringFormatter.php';

class StringFormatter_Named_TextAlignment extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: named, case: text alignment');
    }

    public function testAlignLeftFillDefault () {
        $sf = new StringFormatter ('test: "{val1:<30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align left')), 'test: "align left                    "');
    }

    public function testAlignRightFillDefault () {
        $sf = new StringFormatter ('test: "{val1:>30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align right')), 'test: "                   align right"');
    }

    public function testAlignCenterFillDefault () {
        $sf = new StringFormatter ('test: "{val1:^30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align center')), 'test: "         align center         "');
    }

    public function testAlignLeftFillStar () {
        $sf = new StringFormatter ('test: "{val1:*<30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align left')), 'test: "align left********************"');
    }

    public function testAlignRightFillStar () {
        $sf = new StringFormatter ('test: "{val1:*>30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align right')), 'test: "*******************align right"');
    }

    public function testAlignCenterFillStar () {
        $sf = new StringFormatter ('test: "{val1:*^30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align center')), 'test: "*********align center*********"');
    }

    public function testAlignLeftFillMultiChar () {
        $sf = new StringFormatter ('test: "{val1:*|<30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align left')), 'test: "{val1:*|<30}"');
    }

    public function testAlignRightFillMultiChar () {
        $sf = new StringFormatter ('test: "{val1:*|>30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align right')), 'test: "{val1:*|>30}"');
    }

    public function testAlignCenterFillMultiChar () {
        $sf = new StringFormatter ('test: "{val1:*|^30}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align center')), 'test: "{val1:*|^30}"');
    }

    public function testAlignLeftTokenTooLong () {
        $sf = new StringFormatter ('test: "{val1:<5}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align left')), 'test: "align left"');
    }

    public function testAlignRightTokenTooLong () {
        $sf = new StringFormatter ('test: "{val1:>5}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align right')), 'test: "align right"');
    }

    public function testAlignCenterTokenTooLong () {
        $sf = new StringFormatter ('test: "{val1:^5}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'align center')), 'test: "align center"');
    }

}

