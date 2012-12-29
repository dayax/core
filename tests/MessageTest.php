<?php

namespace dayax\core\tests;

use dayax\core\Message;
use dayax\core\test\TestCase;
use thrower\first\Test as Thrower;

class MessageTest extends TestCase
{
    private $lang;
        
    
    public function testCanSetAndGetLanguage()
    {       
        $m = new Message();
        $m->setLanguage('id');
        $this->assertEquals('id',$m->Language);
    }
    
    public function testCanSetAndGetCacheDir()
    {
        $dir = __DIR__.'/resources/cache';
        $m = new Message();
        $m->CacheDir = $dir;
        $this->assertEquals($dir,$m->CacheDir);
    }
    
    public function testCanLoadCatalogue()
    {
        $m = new Message();
        $m->addCatalogue('foo',__DIR__.'/resources/messages');
        $this->assertTrue($m->hasCatalogue('foo'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowOnInvalidCatalogueDir()
    {
        $m = new Message();
        $m->addCatalogue('foo','foo');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowOnInvalidCacheDir()
    {
        $m = new Message();
        $m->CacheDir = 'foo';
    }
    
    /**
     * @dataProvider getTestTranslateMessage
     */
    public function testTranslateMessage($exception,$method,$message,$lang="en",$parameter=array())
    {
        Message::getInstance()->setLanguage($lang);
        Message::getInstance()->addCatalogue('thrower', __dir__.'/resources/messages');
        $this->setExpectedException('thrower\first\\'.$exception,$message);       
        call_user_func_array(array('thrower\first\Test',$method),$parameter);       
    }
    
    public function getTestTranslateMessage()
    {
        return array(
            array('InvalidArgumentException','throwInvalidArgument','throw new invalid argument exception'),
            array('CustomException','throwCustom','throw new custom exception'),
            array('StringException','throwString','throw new string exception'),
            array('WithArgumentException','throwWithArgument','throw new exception with argument "foo" and "bar"','en',array('foo','bar')),
            
            array('InvalidArgumentException','throwInvalidArgument','id - throw new invalid argument exception','id'),
            array('CustomException','throwCustom','id - throw new custom exception','id'),
            array('StringException','throwString','id - throw new string exception','id'),
            array('WithArgumentException','throwWithArgument','id - throw new exception with argument "foo" and "bar"','id',array('foo','bar')),
            
            array('UntranslatedException','throwUntranslated','untranslated message','id'),
        );
    }
}