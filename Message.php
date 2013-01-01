<?php

namespace dayax\core;

/**
 * Provide Message service
 *
 * @method void addCatalog(string $namespace,string $directory) Description
 */
class Message
{
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