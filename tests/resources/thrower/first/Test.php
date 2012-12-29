<?php

namespace thrower\first;

class Test
{
    public static function throwCustom()
    {
        throw new CustomException('custom');
    }

    public static function throwInvalidArgument()
    {
        throw new InvalidArgumentException('invalid.argument');
    }

    public static function throwString()
    {
        throw new StringException('string');
    }

    public static function throwException()
    {
        throw new Exception('root');
    }
    
    public static function throwWithArgument($foo,$bar)
    {
        throw new WithArgumentException('with.arg',$foo,$bar);
    }
    
    public static function throwUntranslated()
    {
        throw new UntranslatedException('untranslated');
    }
}
