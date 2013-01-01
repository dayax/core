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

use dayax\core\Dayax;
use dayax\core\test\TestCase;

/**
 * Description of DayaxTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
class DayaxTest extends TestCase
{
    /**
     * @covers \dayax\core\Dayax::init
     */
    public function testShouldInitTheLoader()
    {
        $this->assertTrue(is_object(Dayax::getLoader()));
    }
    
    public function testShouldReturnTheRootDir()
    {
        $rootDir = realpath(__DIR__.'/../../../../');        
        $this->assertEquals($rootDir,Dayax::getRootDir());
    }
}

?>
