<?php
namespace Sumo;
class Cache extends Singleton
{
    public static $cache, $count, $disabled, $storeId;
    const EXPIRE = 3600;

    public static function disableCache($status = false)
    {
        Logger::info('Setting cache status to be ' . (!$status ? 'on' : 'off'));
        self::$disabled = $status;
    }

    public static function setStore($id)
    {
        self::$storeId = $id;
    }

    // Static data, that does not need to be refreshed often/automatically
    public static function find($string, $key = '')
    {
        if (self::$disabled) {
            Logger::info('Requested cache to find ' . $string . ':' . $key . ', but cache is disabled');
            return null;
        }

        $file = self::getFile($string);
        if (!isset(self::$cache[$string])) {
            $data = @file_exists($file);
            if ($data !== false) {
                self::$cache[$string] = json_decode(base64_decode(file_get_contents($file)), true);
            }
        }

        if (empty($key)) {
            if (isset(self::$cache[$string])) {
                return self::$cache[$string];
            }
        }
        else {
            if (isset(self::$cache[$string][$key])) {
                return self::$cache[$string][$key];
            }
        }
        return null;
    }

    // Quick changing data, that needs to be refreshed often/automatically
    public static function tempFind($string, $key = '')
    {
        if (self::$disabled) {
            return null;
        }
        $file = self::getFile($string);
        if (!file_exists($file)) {
            return null;
        }
        $filedata = @filemtime($file);
        if (!isset(self::$cache[$string]) && $filedata && (time() - $filedata >= self::EXPIRE)) {
            self::$cache[$string] = json_decode(base64_decode(file_get_contents($file)), true);
        }

        if (empty($key)) {
            if (isset(self::$cache[$string])) {
                return self::$cache[$string];
            }
        }
        else {
            if (isset(self::$cache[$string][$key])) {
                return self::$cache[$string][$key];
            }
        }
        return null;
    }

    public static function getHTML($string)
    {
        if (self::$disabled) {
            return null;
        }

        $file = self::getFile($string);
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        return false;
    }

    public static function setHTML($string, $html)
    {
        if (self::$disabled) {
            return null;
        }

        $file = self::getFile($string);
        $fp = fopen($file, 'w+');
        fwrite($fp, $html);
        fclose($fp);
        return true;
    }

    public static function set($string, $key, $value = '')
    {
        if (self::$disabled) {
            return null;
        }

        $file   = self::getFile($string);
        $cache  = array();
        if (file_exists($file)) {
            $cache = json_decode(base64_decode(file_get_contents($file)), true);
        }
        if (!empty($value)) {
            if (!is_array($cache)) {
                $cache = array();
            }
            $cache[$key] = $value;
        }
        else {
            $cache = $key;
        }
        $fp = fopen($file, 'w+');
        if ($fp) {
            fwrite($fp, base64_encode(json_encode($cache)));
            fclose($fp);
            return true;
        }
        return false;
    }

    public static function remove($string, $search = false)
    {
        if (!$search) {
            $file = self::getFile($string);
            if (file_exists($file)) {
                unlink($file);
            }
            return true;
        }
        else {
            $files = glob( DIR_CACHE . 'cache.' . $string . '*');
            if (is_array($files)) {
                foreach ($files as $file) {
                    unlink($file);
                }
            }
            else {
                self::remove($string);
            }
            return true;
        }
    }

    public static function removeAll($languages = false)
    {
        $files = glob(DIR_CACHE . 'cache.*');
        if (is_array($files)) {
            foreach ($files as $file) {
                if ((stristr($file, 'language') && $languages) || !stristr($file, 'language')) {
                    @unlink($file);
                }
            }
        }
    }

    public static function getFile($string)
    {
        if (!is_numeric(self::$count)) {
            self::$count = 0;
        }
        self::$count++;
        return DIR_CACHE . 'cache.' . preg_replace('/[^a-z0-9A-Z]/', '-', $string) . '.' . md5($string) . '.' . self::$storeId;
    }
}
