<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/simpletest/autorun.php';
require_once dirname (dirname (__FILE__)) .'/StringFormatter.php';

class StringFormatter_Normal_Simple extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: normal, case: simple replacement');
    }

    public function testEmptyTemplateNoArgs () {
        $sf = new StringFormatter ('');
        $this->assertEqual ($sf->parse (), '');
    }

    public function testEmptyTemplateWithArgs () {
        $sf = new StringFormatter ('');
        $this->assertEqual ($sf->parse ('a', 'b'), '');
    }

    public function testUnorderedTokens () {
        $sf = new StringFormatter ('test: {}, test2: {}');
        $this->assertEqual ($sf->parse ('hello', 'world'), 'test: hello, test2: world');
    }

    public function testOrderedTokens () {
        $sf = new StringFormatter ('test: {0}, test2: {1}');
        $this->assertEqual ($sf->parse ('hello', 'world'), 'test: hello, test2: world');
    }

    public function testOrderedTokens2 () {
        $sf = new StringFormatter ('test: {1}, test2: {0}');
        $this->assertEqual ($sf->parse ('hello', 'world'), 'test: world, test2: hello');
    }

    public function testTokenDoesntExists () {
        $sf = new StringFormatter ('test: {0}, test2: {1}');
        $this->assertEqual ($sf->parse ('hello'), 'test: hello, test2: {1}');
    }

    public function testTokenMoreThenOnce () {
        $sf = new StringFormatter ('test: {}, test2: {1}, test3: {0}');
        $this->assertEqual ($sf->parse ('hello', 'world'), 'test: hello, test2: world, test3: hello');
    }

}

