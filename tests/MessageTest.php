<?php

/*
 * This file is part of the dayax.core package.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core\tests;

use dayax\core\test\TestCase;
use dayax\core\Translator;
use dayax\core\Message;

/**
 * MessageTest Class
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
class MessageTest extends TestCase
{
    public function testCanSetCatalogue()
    {
        Message::addCatalog('thrower',__DIR__.'/resources/messages');
        $this->assertTrue(Translator::getInstance()->hasCatalog('thrower'));
    }
    
    public function testCanSetCacheDir()
    {
        Message::setCacheDir(__DIR__.'/resources/cache');
        $this->assertEquals(__DIR__.'/resources/cache',  Translator::getInstance()->getCacheDir());
    }

    public function testShouldReturnKeyWhenUntranslated()
    {
        $this->assertEquals('unknown.key',Message::translate('unknown.key'));
    }

    /**
     * @expectedException dayax\core\UnexistentMethodException
     */
    public function testShouldThrowWhenMethodNotExists()
    {
        Message::foo();
    }
}

?>
