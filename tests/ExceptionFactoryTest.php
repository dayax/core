<?php

namespace dayax\core\tests;

use dayax\core\test\TestCase;
use dayax\core\ExceptionFactory;

class ExceptionFactoryTest extends TestCase
{
    public static function setUpBeforeCLass()
    {
        require_once __DIR__.'/resources/thrower/Test.php';
        ExceptionFactory::addPackage('thrower');
    }

    public function testShouldExtendsPhpDefaultClass()
    {
        $this->assertTrue(new \thrower\InvalidArgumentException() instanceof \InvalidArgumentException);
        $this->assertTrue(new \thrower\first\InvalidArgumentException() instanceof \InvalidArgumentException);
        $this->assertTrue(new \thrower\first\second\Exception() instanceof \Exception);
    }

    /**
     * @dataProvider getTestClass
     */
    public function testShouldCreatedFromClass($callback,$expected)
    {
        $this->setExpectedException($expected);
        call_user_func($callback);
    }

    public function getTestClass()
    {
        return array(
            array('thrower\\Test::throwString','\Exception'),
            array('thrower\\first\\Test::throwException','thrower\\first\\Exception'),
            array('thrower\\first\\Test::throwCustom','thrower\\first\\CustomException'),
            array('thrower\\first\\second\\Test::throwException','thrower\\first\\second\\Exception'),
        );
    }
}
