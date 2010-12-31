<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/simpletest/autorun.php';
require_once dirname (dirname (__FILE__)) .'/StringFormatter.php';

class StringFormatter_Named_Sprintf extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: named, case: sprintf');
    }

    public function testSprintfFloat () {
        $sf = new StringFormatter ('test: "{val1%%0.2f}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => pi ())), 'test: "3.14"');
    }

    public function testSprintfSignedInt () {
        $sf = new StringFormatter ('test: "{val1%+%d}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => pi ())), 'test: "+3"');
    }

    public function testSprintfWithArgumentSwapping () {
        $sf = new StringFormatter ('test: "{val1%%2$s %1$s}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => array ('world', 'Hello'))), 'test: "Hello world"');
    }

}

