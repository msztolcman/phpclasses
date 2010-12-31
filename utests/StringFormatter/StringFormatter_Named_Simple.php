<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/../simpletest/autorun.php';
require_once dirname (__FILE__) . '/../../StringFormatter.php';

class StringFormatter_Named_Simple extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: named, case: simple replacement');
    }

    public function testEmptyTemplateNoArgs () {
        $sf = new StringFormatter ('');
        $this->assertEqual ($sf->parseNamed (), '');
    }

    public function testEmptyTemplateWithArgs () {
        $sf = new StringFormatter ('');
        $this->assertEqual ($sf->parseNamed (array ('val1' => 'a', 'val2' => 'b')), '');
    }

    public function testNamedTokens () {
        $sf = new StringFormatter ('test: {hello}, test2: {world}');
        $this->assertEqual ($sf->parseNamed (array ('hello' => 'Hello', 'world' => 'world')), 'test: Hello, test2: world');
    }

    public function testUnnamedTokens () {
        $sf = new StringFormatter ('test: {}, test2: {}');
        $this->assertEqual ($sf->parseNamed (array ('hello' => 'Hello', 'world' => 'world')), 'test: {}, test2: {}');
    }

    public function testTokenDoesntExists () {
        $sf = new StringFormatter ('test: {hello}, test2: {mars}');
        $this->assertEqual ($sf->parseNamed (array ('hello' => 'Hello')), 'test: Hello, test2: {mars}');
    }

    public function testTokenMoreThenOnce () {
        $sf = new StringFormatter ('test: {hello}, test2: {world}, test3: {hello}');
        $this->assertEqual ($sf->parseNamed (array ('hello' => 'Hello', 'world' => 'world')), 'test: Hello, test2: world, test3: Hello');
    }

}

