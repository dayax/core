<?php

namespace thrower\first;

class Test
{
    public static function throwCustom()
    {
        throw new CustomException();
    }

    public static function throwInvalidArgument()
    {
        throw new InvalidArgumentException();
    }

    public static function throwString()
    {
        throw new StringException();
    }

    public static function throwException()
    {
        throw new Exception();
    }
}
