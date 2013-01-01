<?php

namespace dayax\core\tests;

use dayax\core\Translator;
use dayax\core\test\TestCase;

class TranslatorTest extends TestCase
{
    private $lang;

    public function testCanSetAndGetLanguage()
    {
        $m = new Translator();
        $m->setLanguage('id');
        $this->assertEquals('id',$m->Language);
    }

    public function testCanSetAndGetCacheDir()
    {
        $dir = __DIR__.'/resources/cache';
        if(!is_dir($dir)){
            mkdir($dir,true);
        }
        $m = new Translator();
        $m->CacheDir = $dir;
        $this->assertEquals($dir,$m->CacheDir);
    }

    public function testCanLoadcatalog()
    {
        $m = new Translator();
        $m->addCatalog('foo',__DIR__.'/resources/messages');
        $this->assertTrue($m->hasCatalog('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowOnInvalidCatalogDir()
    {
        $m = new Translator();
        $m->addcatalog('foo','foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowOnInvalidCacheDir()
    {
        $m = new Translator();
        $m->CacheDir = 'foo';
    }

    /**
     * @dataProvider getTestTranslate
     * @covers dayax\core\Translator::translate
     */
    public function testTranslate($exception,$method,$message,$lang="en",$parameter=array())
    {
        Translator::getInstance()->setLanguage($lang);
        Translator::getInstance()->addCatalog('thrower', __dir__.'/resources/messages');
        $this->setExpectedException('thrower\first\\'.$exception,$message);
        call_user_func_array(array('thrower\first\Test',$method),$parameter);
    }

    public function getTestTranslate()
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