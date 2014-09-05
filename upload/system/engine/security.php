<?php
/**
    SumoGuard V2.0.0
**/
namespace SumoGuard;
use Sumo;

class Settings extends Sumo\Singleton
{
    private static $settings;

    public static function set($key, $value = null)
    {
        if(is_array($key)) {
            foreach ($key as $k => $v) {
                return self::$settings[$k] = $v;
            }
        }
        else {
            return self::$settings[$key] = $value;
        }
    }

    public static function get($key)
    {
        return isset(self::$settings[$key]) ? self::$settings[$key] : null;
    }

    public static function isEnabled()
    {
        return false;
    }

    public static function v()
    {
        if (empty(self::$settings['license_key'])) {
            echo 'empty_license_key';
            return false;
        }

        if (!@file_exists(DIR_CACHE . 'sumo.guard.data')) {
            self::iv();
            return self::v();
        }

        $c = @file_get_contents(DIR_CACHE . 'sumo.guard.data');
        $c = @json_decode($c, true);
        if (!is_array($c) || empty($c['t']) || empty($c['h']) || self::ih($c['t']) != $c['h'] || !self::it(self::ih($c['t']))) {
            self::iv();
            return self::v();
        }

        return $c;
    }

    // Internal validation function to validate license key and fetch data from API while sending feedback
    private static function iv()
    {
        //touch(DIR_CACHE . 'sumo.guard.disabled');
        $data = array();
        $data['t'] = time();
        $data['h'] = self::ih($data['t']);

        $fp = fopen(DIR_CACHE . 'sumo.guard.data', 'w+');
        fwrite($fp, json_encode($data));
        fclose($fp);
    }

    // Internal hashing function
    private static function ih($t)
    {
        return strtoupper(sha1(strrev($t)));
    }

    // Internal time function to check if the given time is bigger/lower than the current
    private static function it($t)
    {
        $t = (int)$t;
        if (empty($t) || ($t + (rand(1, 24) * 3600) <= time())) {
            return false;
        }
        return true;
    }
}

class Reporter extends Sumo\Singleton
{

}

abstract class Listener extends Sumo\Singleton
{

}

if (defined('LICENSE_KEY')) {
    Settings::set('license_key', LICENSE_KEY);
}
