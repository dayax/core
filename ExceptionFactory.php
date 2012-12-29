<?php

/*
 * This file is part of the dayax project.
 *
 * (c) Anthonius Munthi <toni.dayax@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dayax\core;

/**
 * Exception Class.
 *
 * @author Anthonius Munthi <toni.dayax@gmail.com>
 */
class ExceptionFactory extends \Exception
{
    private static $splClasses = null;
    private static $packages = array(
        'dayax',
    );

    private static $template = null;

    /**
     * @codeCoverageIgnore
     */
    public static function register()
    {
        $autoloads = spl_autoload_functions();
        $callback = array('dayax\core\ExceptionFactory', 'loadClass');
        if (!in_array($callback, $autoloads)) {
            spl_autoload_register($callback);
        }

        if (is_null(self::$splClasses)) {
            self::$splClasses = array();
            foreach (spl_classes() as $splClass) {
                if (false !== strpos($splClass, 'Exception')) {
                    if (!in_array($splClass, self::$splClasses)) {
                        self::$splClasses[$splClass] = $splClass;
                    }
                }
            }
        }

        if (is_null(self::$template)) {
            self::$template = file_get_contents(__DIR__.'/resources/exception.tpl',LOCK_EX);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public static function addPackage($name)
    {
        if (!in_array($name, self::$packages)) {
            self::$packages[] = $name;
        }
    }

    public static function loadClass($class)
    {
        if (false === strpos($class, "\\") || false === strpos($class, 'Exception')) {
            return;//@codeCoverageIgnore
        }

        $exp = explode("\\",$class);
        if (!in_array($exp[0], self::$packages)) {
            return;//@codeCoverageIgnore
        }
        $namespace = substr($class, 0, strrpos($class, '\\'));
        $eclass    = $exp[count($exp) - 1];

        $extend = "\Exception";
        if (isset(self::$splClasses[$eclass])) {
            $extend = '\\'.self::$splClasses[$eclass];
        }

        $replacement = array(
            '%%namespace%%'=>$namespace,
            '%%class%%'=>$eclass,
            '%%extends%%'=>$extend,
        );

        $definition = strtr(self::$template,$replacement);

        eval($definition);
    }
}
