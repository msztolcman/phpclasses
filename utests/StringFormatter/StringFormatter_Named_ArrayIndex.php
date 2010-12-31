<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../StringFormatter.php';

class StringFormatter_Named_ArrayIndex extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: named, case: array index');
    }

    public function testIndexNumericDoesNotExists () {
        $sf = new StringFormatter ('test: "{val1[1]}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => array ('Hello'))), 'test: "{val1[1]}"');
    }

    public function testIndexNumericExists () {
        $sf = new StringFormatter ('test: "{val1[0]}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => array ('Hello'))), 'test: "Hello"');
    }

    public function testKeyDoesNotExists () {
        $sf = new StringFormatter ('test: "{val1[world]}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => array ('hello' => 'Hello'))), 'test: "{val1[world]}"');
    }

    public function testKeyExists () {
        $sf = new StringFormatter ('test: "{val1[hello]}"');
        $this->assertEqual ($sf->parseNamed (array ('val1' => array ('hello' => 'Hello'))), 'test: "Hello"');
    }

}

