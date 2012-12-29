<?php

/*
 * This file is part of the dayax package.
*
* (c) Anthonius Munthi <me@itstoni.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dayax\core\tests;

use dayax\core\EventHandler;
use dayax\core\test\TestCase;
use dayax\core\tests\TestComponent;

class EventHandlerTest extends TestCase
{
    public function getTestComponent()
    {
        return new TestComponent();
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddEventShouldThrowOnInvalidHandler()
    {
        $e = new EventHandler();
        $e->add('test',array('foo','bar'));
    }
    
    public function testShouldAddHandler()
    {        
        $e = new EventHandler();
        $this->assertFalse($e->hasEvent('test'));
        $e->add('test', array($this->getTestComponent(),'onEventA'));
        $this->assertTrue($e->hasEvent('test'));        
    }
    
    public function testShouldSortHandler()
    {
        $e = new EventHandler();
        $ob = $this->getTestComponent();
        $e->add('test',array($ob,'onEventA'));
        $e->add('test',array($ob,'onEventB'));
        $e->add('test',array($ob,'onPriority1'),1);
        $e->add('test',array($ob,'onPriority2'),2);
        $e->add('test',array($ob,'onFoo'),2);
        
        $h = $e->getHandlers('test');        
        $this->assertEquals(array($ob,'onPriority1'),$h[0]);
        $this->assertEquals(array($ob,'onPriority2'),$h[1]);
        $this->assertEquals(array($ob,'onFoo'),$h[2]);
        $this->assertEquals(array($ob,'onEventA'),$h[3]);
        $this->assertEquals(array($ob,'onEventB'),$h[4]);
    }
    
    public function testShouldReturnFalseWhenNoHandlerRegistered()
    {
        $e = new EventHandler();
        $this->assertFalse($e->raiseEvent('test'));
    }
    
    public function testShouldRaiseEvent()
    {
        $e = new EventHandler();
        $c = $this->getTestComponent();
        $e->add('test', array($c,'onFoo'));
        $e->add('test', array($c,'onBar'));
        
        $e->raiseEvent('test');
        $this->assertEquals('Foo',$c->foo);
        $this->assertEquals('Bar',$c->bar);
    }
    
    public function testShouldRaiseEventWithParameter()
    {
        $e = new EventHandler();
        $c = $this->getTestComponent();
        $e->add('test', array($c,'onEventWithParameter'));
        
        $e->raiseEvent('test',array('param1','param2'));
        $this->assertEquals('param1',$c->param1);
        $this->assertEquals('param2',$c->param2);
        
        $e->raiseEvent('test',array('foo','bar'));
        $this->assertEquals('foo',$c->param1);
        $this->assertEquals('bar',$c->param2);
    }
}