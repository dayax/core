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
 * @author Anthonius Munthi <toni.munthi@gmail.com>
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

    public function testShouldReturnThePathOfNamespace()
    {
        $this->assertTrue(is_dir(Dayax::getPathOfNamespace('dayax\core\resources')));
        $this->assertTrue(is_file(Dayax::getPathOfNamespace('dayax\core\ExceptionFactory')));
    }
}

?>
