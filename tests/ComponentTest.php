<?php

namespace dayax\core\tests;

use dayax\core\test\TestCase;

class ComponentTest extends TestCase
{
    public function testCanSetAndGetProperty()
    {
        $c = new TestComponent();
        $c->Prop1 = 'hello';
        
        $this->assertEquals('hello',$c->Prop1);
        $this->assertEquals('hello',$c->getProp1());

        $c->setProp1('foo');
        $this->assertEquals('foo',$c->Prop1);
        $this->assertEquals('foo',$c->getProp1());
    }
    
    /**
     * @expectedException dayax\core\InvalidOperationException
     */
    public function testCannotSetPropertyWhenReadOnly()
    {
        $c = new TestComponent();
        $c->ReadOnlyProp = 21;
    }

    public function testCanSetAndGetEventHandler()
    {
        $c = new TestComponent();
        $c->OnFoo = array($c,'OnFoo');
        $this->assertTrue($c->getEventHandler()->hasEvent('onfoo'));
        
        $h = $c->OnFoo;        
        $this->assertEquals(array($c,'OnFoo'),$h[0]);
        $this->assertEquals(array(),$c->OnBar);
    }
    
    /**
     * @expectedException dayax\core\InvalidOperationException
     */
    public function testShouldThrowOnUndefinedEvent()
    {
        $c = new TestComponent();
        $c->OnUndefinedEvent;
    }
    
    /**
     * @expectedException dayax\core\InvalidOperationException
     */
    public function testShouldThrowOnGetUndefinedProperty()
    {
        $c = new TestComponent();
        $bar = $c->UndefinedProperty;
    }
    
    /**
     * @expectedException dayax\core\InvalidOperationException
     */
    public function testShouldThrowOnSetUndefinedProperty()
    {
        $c = new TestComponent();
        $c->UndefinedProperty = "bar";
    }
}