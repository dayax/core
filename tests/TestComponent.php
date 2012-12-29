<?php

namespace dayax\core\tests;

use dayax\core\Component;

class TestComponent extends Component
{
    public $foo = null;
    public $bar = null;
    
    public $param1 = null;
    public $param2 = null;
    
    private $prop1 = null;
    private $readOnlyProp = null;
    
    public function setProp1($value)
    {
        $this->prop1 = $value;
    }
    
    public function getProp1()
    {
        return $this->prop1;
    }
    
    public function getReadOnlyProp()
    {
        return $this->readOnlyProp;
    }
    
    public function onEventA()
    {

    }

    public function onEventB()
    {

    }

    public function onPriority1()
    {

    }

    public function onPriority2()
    {

    }
    
    public function onFoo()
    {
        $this->foo = "Foo";
    }
    
    public function onBar()
    {
        $this->bar = "Bar";
    }
    
    public function onEventWithParameter($param1,$param2)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }
}