<?php

namespace dayax\core\tests;

class TestComponent
{
    public $foo = null;
    public $bar = null;
    
    public $param1 = null;
    public $param2 = null;
    
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