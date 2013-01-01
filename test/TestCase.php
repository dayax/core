<?php

/*
 * This file is part of the dayax package.
 *
 * (c) Anthonius Munthi <toni.dayax@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core\test;
require_once __DIR__.'/ExceptionCode.php';
/**
 * TestCase Class.
 *
 * @author Anthonius Munthi <toni.dayax@gmail.com>
 * @codeCoverageIgnore
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $resourceDir;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $r = new \ReflectionClass($this);
        $this->resourceDir = dirname($r->getFileName()).DIRECTORY_SEPARATOR.'resources';
    }

    /**
     * Removes files or directories.
     *
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to remove
     */
    public static function remove($files)
    {
        //$files = iterator_to_array($this->toIterator($files));
        if (!$files instanceof \Traversable) {
            $files = new \ArrayObject(is_array($files) ? $files : array($files));
        }
        $files = iterator_to_array($files);
        $files = array_reverse($files);
        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }
            if (is_dir($file) && !is_link($file)) {
                self::remove(new \FilesystemIterator($file));
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }

}
