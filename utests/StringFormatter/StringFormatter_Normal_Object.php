<?php

error_reporting (E_ALL | E_STRICT);
ini_set ('display_errors', true);

require_once dirname (__FILE__) . '/simpletest/autorun.php';
require_once dirname (dirname (__FILE__)) .'/StringFormatter.php';

class StringFormatterTestObject {
    public $test_value = 'Hello world';
    public $test_value2 = null;
    public $test_value3 = null;

    public function __construct () {
        $this->test_value2 = new stdClass ();
        $this->test_value3 = new StringFormatterTestObject2 ();
    }

    public function test_method () {
        return $this->test_value;
    }

    public function test_method2 () {
        return $this->test_value2;
    }

    public function test_method3 () {
        return $this->test_value3;
    }

}

class StringFormatterTestObject2 {
    public function __toString () {
        return '[StringFormatterTestObject2]';
    }

    public function __call ($method, $args) {
        if ($method == 'test_method_magic') {
            return $method;
        }
        else {
            return 'unknown method: '. $method;
        }
    }

}

class StringFormatterTestObject3 {
    public function __get ($property) {
        if ($property == 'test_property_magic') {
            return $property;
        }
        else {
            return 'unknown property: '. $property;
        }
    }

}

class StringFormatter_Normal_Object extends UnitTestCase {
    public function __construct () {
        parent::__construct ('Mode: normal, case: object');
        $this->obj = new StringFormatterTestObject ();
        $this->obj2 = new StringFormatterTestObject2 ();
        $this->obj3 = new StringFormatterTestObject3 ();
    }

    public function testCallNonExistentMember () {
        $sf = new StringFormatter ('test: "{0->test}"');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "{0->test}"');
    }

    public function testCallProperty () {
        $sf = new StringFormatter ('test: "{0->test_value}"');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "'.$this->obj->test_value.'"');
    }

    public function testCallMethod () {
        $sf = new StringFormatter ('test: "{0->test_method}"');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "'.$this->obj->test_value.'"');
    }

    public function testCallPropertyStdClass () {
        $this->expectError ('Object of class stdClass could not be converted to string');
        $sf = new StringFormatter ('test: "{0->test_value2}"');
        $this->expectError ('Object of class stdClass to string conversion');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "Object"');
    }

    public function testCallPropertyClassWithToString () {
        $sf = new StringFormatter ('test: "{0->test_value3}"');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "[StringFormatterTestObject2]"');
    }

    public function testCallMethodStdClass () {
        $this->expectError ('Object of class stdClass could not be converted to string');
        $sf = new StringFormatter ('test: "{0->test_method2}"');
        $this->expectError ('Object of class stdClass to string conversion');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "Object"');
    }

    public function testCallMethodClassWithToString () {
        $sf = new StringFormatter ('test: "{0->test_method3}"');
        $this->assertEqual ($sf->parse ($this->obj), 'test: "[StringFormatterTestObject2]"');
    }

    public function testCallMethodMagicDoesNotExists () {
        $sf = new StringFormatter ('test: "{0->test_method4}"');
        $this->assertEqual ($sf->parse ($this->obj2), 'test: "unknown method: test_method4"');
    }

    public function testCallMethodMagicExists () {
        $sf = new StringFormatter ('test: "{0->test_method_magic}"');
        $this->assertEqual ($sf->parse ($this->obj2), 'test: "test_method_magic"');
    }

    public function testCallPropertyMagicDoesNotExists () {
        $sf = new StringFormatter ('test: "{0->test_property}"');
        $this->assertEqual ($sf->parse ($this->obj3), 'test: "unknown property: test_property"');
    }

    public function testCallPropertyMagicExists () {
        $sf = new StringFormatter ('test: "{0->test_property_magic}"');
        $this->assertEqual ($sf->parse ($this->obj3), 'test: "test_property_magic"');
    }

}

