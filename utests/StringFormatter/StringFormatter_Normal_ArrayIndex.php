<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../StringFormatter.php';

class StringFormatter_Normal_ArrayIndex extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: normal, case: array index');
    }

    public function testIndexNumericDoesNotExists () {
        $sf = new StringFormatter ('test: "{0[1]}"');
        $this->assertEqual ($sf->parse (array ('Hello')), 'test: "{0[1]}"');
    }

    public function testIndexNumericExists () {
        $sf = new StringFormatter ('test: "{0[0]}"');
        $this->assertEqual ($sf->parse (array ('Hello')), 'test: "Hello"');
    }

    public function testKeyDoesNotExists () {
        $sf = new StringFormatter ('test: "{0[world]}"');
        $this->assertEqual ($sf->parse (array ('hello' => 'Hello')), 'test: "{0[world]}"');
    }

    public function testKeyExists () {
        $sf = new StringFormatter ('test: "{0[hello]}"');
        $this->assertEqual ($sf->parse (array ('hello' => 'Hello')), 'test: "Hello"');
    }

}

