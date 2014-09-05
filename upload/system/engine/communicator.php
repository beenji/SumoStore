<?php
namespace Sumo;
class Communicator extends Singleton
{
    public static $instance;

    public static function send($userAgent, $postData, $return = false)
    {
        return;
    }

    public static function getVersion()
    {
        return '1.10';
    }
}
