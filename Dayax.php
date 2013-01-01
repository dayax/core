<?php

/*
 * This file is part of the dayax package.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core;

use dayax\core\ExceptionFactory;

/**
 * Dayax Utility Class
 * @author Anthonius Munthi <me@itstoni.com>
 * @codeCoverageIgnore
 * @todo Remove this class
 */
class Dayax
{

    private static $cacheDir;
    private static $loader;

    private static $initialized = false;
    private static $rootDir = false;

    static public function getRootDir()
    {
        return self::$rootDir;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getVendorDir()
    {
        return self::$rootDir.'/vendor';
    }

    /**
     * @codeCoverageIgnore
     */
    public static function setCacheDir($dir)
    {
        self::$cacheDir = $dir;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getCacheDir()
    {
        if (is_null(self::$cacheDir)) {
            self::$cacheDir = __DIR__.'/../../../cache';
        }
        return self::$cacheDir;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function serialize($data,$file=null)
    {
        $serialized = serialize(array($data));
        if (!is_null($file)) {
            file_put_contents($file, $serialized,LOCK_EX);
        }

        return $serialized;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function unserialize($data)
    {
        if (is_file($data)) {
            $unserialized = file_get_contents($data,LOCK_EX);
            $unserialized = unserialize($unserialized);

            return $unserialized[0];
        }
        $data = unserialize($data);

        return $data[0];
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     * @codeCoverageIgnore
     */
    public static function getLoader()
    {
        if(!is_object(self::$loader)){
            foreach(get_declared_classes() as $class){
                if(false!==strpos($class,'ComposerAutoloaderInit')){
                    self::$loader = $class::getLoader();
                    break;
                }
            }
        }
        return self::$loader;
    }

    /**
     * @codeCoverageIgnore
     */
    static public function init($closure = null)
    {
        if(true===self::$initialized){
            return;
        }
        $loader = self::getLoader();
        $r = new \ReflectionClass($loader);

        $rootDir = realpath(dirname($r->getFileName()).'/../../');
        self::$rootDir = $rootDir;
        self::$cacheDir = $rootDir.'/cache';

        ExceptionFactory::register();
        if(is_callable($closure)){
            call_user_func($closure);
        }
        self::$initialized = true;
    }
}
