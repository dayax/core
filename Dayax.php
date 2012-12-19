<?php

/*
 * This file is part of the dayax project.
 *
 * (c) Anthonius Munthi <toni.munthi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core;

use dayax\core\ExceptionFactory;

class Dayax
{

    private static $cacheDir;
    private static $loader;

    private static $initialized = false;
    private static $rootDir = false;

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
     * @param   string $namespace
     * @return  string A path of namespace
     */
    public static function getPathOfNamespace($namespace)
    {
        $pos = strrpos($namespace, "\\");
        $ns = substr($namespace, 0, $pos);
        $class = substr($namespace, $pos + 1);

        $prefixes = self::getLoader()->getPrefixes();
        foreach ($prefixes as $package => $paths) {
            if (false !== strpos($ns, $package)) {
                foreach ($paths as $path) {
                    $dir = $path.DIRECTORY_SEPARATOR . $ns . DIRECTORY_SEPARATOR . $class;
                    $dir = str_replace("\\", DIRECTORY_SEPARATOR, $dir);
                    if (is_file($file = $dir . '.php')) {
                        return $file;
                    } elseif (is_dir($dir)) {
                        return $dir;
                    }
                }// end for paths loop
            } //@codeCoverageIgnore
        }// end for namespaces loop
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

        if(is_file($file = $rootDir.'/vendor/autoload_classmap.php')){
            $map = require_once $file;
            if(is_array($map)){
                $loader->addClassMap($map);
            }
        }

        ExceptionFactory::register();
        if(is_callable($closure)){
            call_user_func($closure);
        }
        self::$initialized = true;
    }
}
