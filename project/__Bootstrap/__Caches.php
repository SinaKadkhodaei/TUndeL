<?php

namespace __Bootstrap;

class __Caches
{
    private static
        $instance = null;

    public static function __callStatic($_name, $_arguments)
    {
        if (self::$instance === null)
            self::$instance = new __Caches();

        if (count($_arguments) > 0)
            self::$instance->{$_name} = $_arguments[0];
        else
            return self::$instance->{$_name};
    }

    public static function exists($name)
    {
        return isset(self::$instance->{$name});
    }
}
