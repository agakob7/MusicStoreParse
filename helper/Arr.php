<?php


class Arr
{

    public static function get($array, $key, $default = false)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }


}

