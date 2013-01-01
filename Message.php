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

/**
 * Provide Message service
 *
 *
 * @method void addCatalog(string $namespace,string $directory) Add a new catalog to load
 *
 * @author Anthonius Munthi <me@itstoni.com>
 */
class Message
{
    /**
     * Translate a message
     * @param   string  $message
     * @param   array   $parameters
     * @return  string  Translated message
     */
    static public function translate($message,Array $parameters=array())
    {
        $trace = debug_backtrace();
        $trace = $trace[2];
        $class = $trace['class'];
        $function = $trace['function'];
        if ($class !== __CLASS__ && $function !== __METHOD__) {
            $exp = explode('\\', $class);
            $namespace = $exp[0];
            return Translator::getInstance()->translate($message, $parameters, $namespace);
        }
        return $message;
    }

    static public function __callStatic($name,$arguments)
    {
        $translator = Translator::getInstance();
        $callback = array();
        if($name==='addCatalog'){
            $callback = array($translator,$name);
        }

        if(!is_callable($callback)){
            throw new UnexistentMethodException('core.message_undefined_method',__CLASS__,$name);
        }

        return call_user_func_array($callback, $arguments);
    }
}