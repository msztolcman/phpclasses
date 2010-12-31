<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../StringFormatter.php';

class StringFormatter_Normal_Sprintf extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: normal, case: sprintf');
    }

    public function testSprintfFloat () {
        $sf = new StringFormatter ('test: "{0%%0.2f}"');
        $this->assertEqual ($sf->parse (pi ()), 'test: "3.14"');
    }

    public function testSprintfSignedInt () {
        $sf = new StringFormatter ('test: "{0%+%d}"');
        $this->assertEqual ($sf->parse (pi ()), 'test: "+3"');
    }

    public function testSprintfWithArgumentSwapping () {
        $sf = new StringFormatter ('test: "{0%%2$s %1$s}"');
        $this->assertEqual ($sf->parse (array ('world', 'Hello')), 'test: "Hello world"');
    }

}

