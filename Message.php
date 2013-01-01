<?php

namespace dayax\core;

class Message
{
    static public function translate($message,$parameters)
    {        
        foreach(debug_backtrace() as $trace){            
            $class = $trace['class'];
            $function  = $trace['function'];            
            if($class!==__CLASS__ && $function !== __METHOD__){
                $exp = explode('\\',$class);
                $namespace = $exp[0];                
                return Translator::getInstance()->translate($message, $parameters,$namespace);
            }
        }
        return $message;
    }
}