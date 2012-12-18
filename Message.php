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
 * Message Class.
 *
 * @author Anthonius Munthi <toni.dayax@gmail.com>
 */
class Message
{
    private static $_cacheDir = null;

    private static $_cached = array();

    private static $_lang =  'en';

    public static function setLanguage($lang)
    {
        self::$_lang = $lang;
    }

    public static function getLanguage()
    {
        return self::$_lang;
    }

    public static function getCacheDir()
    {
        if (is_null(self::$_cacheDir)) {
            self::$_cacheDir = Dayax::getCacheDir().DIRECTORY_SEPARATOR. 'messages';
            if (!is_dir(self::$_cacheDir)) {
                if (!@mkdir(self::$_cacheDir, 0777, true)) {
                    throw new Exception(sprintf("Can't create message cache directory. Please ensure that directory '%s' is writable.", dirname(self::$_cacheDir)));
                }
            }
        }

        return self::$_cacheDir;
    }

    public static function getCacheFileName($sourceFile)
    {
        $file = self::getCacheDir().DIRECTORY_SEPARATOR.hash('crc32',dirname($sourceFile)).DIRECTORY_SEPARATOR.basename($sourceFile,'.txt').'.php';
        if (!is_dir($dir = dirname($file))) {
            mkdir($dir,0777,true);
        }
        return $file;
    }

    public static function translateMessage()
    {
        $args = debug_backtrace();
        $sourceDir = self::getMessageDir($args[1]);
        $file = self::getMessageFile($sourceDir);

        if (!isset(self::$_cached[$file])) {
            self::loadCache($file);
        }

        $params = func_get_args();
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $key = array_shift($params);
        $message = isset(self::$_cached[$file][$key]) ? self::$_cached[$file][$key] : $key;
        $tokens = array();
        for ($i=0;$i<count($params);$i++) {
            $tokens["{".$i."}"] = $params[$i];
        }

        return strtr($message,$tokens);
    }

    private static function writeCache($cacheFile,$sourceFile)
    {
        $contents = file($sourceFile);
        $cached = array();
        foreach ($contents as $content) {
            if(trim($content)==='') continue;
            $exp = explode("=", $content);
            array_walk($exp, create_function('&$item', '$item=trim($item);'));
            list($key, $msg) = $exp;
            $cached[$key] = $msg;
        }
        $data = var_export($cached,true);
        $tpl = <<<EOC
<?php

/*
 * Messages cache for file  : %s
 * generated at             : %s
 */

self::\$_cached["%s"] = %s;
EOC;
        $contents = sprintf($tpl,$sourceFile,date('Y-m-d h:m:s'),$sourceFile,$data);
        file_put_contents($cacheFile, $contents,LOCK_EX);
    }

    private static function getMessageDir($caller)
    {
        $default = __DIR__ . '/resources/messages';
        // try to found foo\bar\resource or foo\resource first
        if (isset($caller['object']) || is_object($caller['class'])) {
            $r = isset($caller['object']) ? new \ReflectionClass($caller['object']):new \ReflectionClass($caller['class']);

            //try namespace\class first
            $exp = explode("\\",$r->getNamespaceName());
            $c = count($exp);
            for ($i=0;$i<$c;$i++) {
                //echo implode("\\",$exp).'\\resources\\messages'."\n";
                $path = Dayax::getPathOfNamespace(implode("\\",$exp).'\\resources\\messages');

                if (is_dir($path)) {
                    return $path;
                }
                array_pop($exp);
            }

            $baseDir = dirname($r->getFileName());
            if (is_dir($path = $baseDir . '/resources/messages')) {
                return realpath($path);
            }

            // not found? then we should use foo\namespace\resource
            $exp = explode("\\",$r->getNamespaceName());
            $cDir = realpath($baseDir);

            for ($i=0;$i<count($exp);$i++) {
                $dir = $cDir.'/resources/messages';
                if (is_dir($path=realpath($dir))) {
                    return $path;
                }
                $cDir = $cDir.'/..';
            }
            // not found? we should try the root package of namespace foo;
            $root = $exp[0];
            $paths = Dayax::getLoader()->getPrefixes();
            $paths = isset($paths[$root]) ? $paths[$root]:array();
            foreach ($paths as $dir) {
                if (is_dir($path=$dir.DIRECTORY_SEPARATOR.$root.'/resources/messages')) {
                    return $path;
                }
            }
        }

        return $default;
    }

    private static function getMessageFile($dir)
    {
        if (is_file($file = $dir . DIRECTORY_SEPARATOR . 'messages.' . self::$_lang . '.txt')) {
            echo $file;
            return $file;
        } elseif (is_file($file = $dir . DIRECTORY_SEPARATOR . 'messages.txt')) {
            return $file;
        } else {
            throw new Exception("messages file not exists ".$file);
        }
    }

    private static function loadCache($sourceFile)
    {
        $cacheFile = self::getCacheFileName($sourceFile);
        if (!is_file($cacheFile) || (filemtime($sourceFile) > filemtime($cacheFile))) {
            self::writeCache($cacheFile,$sourceFile);
        }
        include($cacheFile);
    }

}
