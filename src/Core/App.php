<?php

namespace App\Core;

class App
{
    private static $_storage = array();

    /**
     * Установка значения
     */
    public static function setService($key, $value)
    {
        return self::$_storage[$key] = $value;
    }

    /**
     * Получение значения
     */
    public static function getService($key, $default = null)
    {
        return (isset(self::$_storage[$key])) ? self::$_storage[$key] : $default;
    }

    /**
     * Удаление
     */
    public static function removeService($key)
    {
        unset(self::$_storage[$key]);
        return true;
    }

    /**
     * Очистка
     */
    public static function cleanService()
    {
        self::$_storage = array();
        return true;
    }
}
