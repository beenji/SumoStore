<?php
namespace Sumo;
abstract class Singleton
{
    public static function getInstance()
    {
        return isset(static::$instance) ?: static::$instance = new static;
    }
}
