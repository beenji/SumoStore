<?php
namespace Sumo;
class Logger extends Singleton
{
    const LOG_INFO      = 'info';
    const LOG_WARNING   = 'warning';
    const LOG_ERROR     = 'error';

    public      static $instance;
    private     static $messages, $startTime, $startRam, $level;
    protected   static $trace;

    public function __construct()
    {
        self::$level        = 1;
        self::$startTime    = microtime(true);
        self::$startRam     = memory_get_usage(true);
    }

    public static function setLevel($level)
    {
        self::$level = $level;
    }

    public static function add($message, $type = Logger::LOG_INFO, $extratrace = array())
    {
        $trace      = debug_backtrace(false);
        $backtrace  = array();

        if (count($extratrace)) {
            $i = 1;
            foreach ($extratrace as $single) {
                $backtrace['manually added ' . $i++] = $single;
            }
        }

        for ($i = 1; $i <= self::$level; $i++) {
            if (!isset($trace[$i])) {
                continue;
            }

            $tmp = $trace[$i];
            $called = '';

            if (isset($tmp['class'])) {
                $called .= $tmp['class'] . $tmp['type'];
            }

            $called .= $tmp['function'];

            if (isset($tmp['args'])) {
                $called .= '(';
                foreach ($tmp['args'] as $argk => $argv) {
                    if (is_array($argv)) {
                        foreach ($argv as $k => $v) {
                            if (is_object($v)) {
                                $called .= '[' . get_class($v) . ']';
                            }
                            else if (is_string($v)) {
                                $called .= '[' . $v . ']';
                            }
                            else {
                                $called .= '[array]';
                            }
                            $called .= ', ';
                        }

                    }
                    else {
                        if (is_object($argv)) {
                            $called .= get_class($argv);
                        }
                        else if (is_string($argv)) {
                            $called .= $argv;
                        }
                        else {
                            $called .= 'array';
                        }
                        $called .= ', ';
                    }
                }
                $called = rtrim($called, ', ');
                $called .= ')';
            }
            $backtrace[$i]['called'] = $called;
            $backtrace[$i]['file'] = isset($tmp['file']) ? $tmp['file'] . ':' . $tmp['line'] : 'Unknown';
        }

        self::$messages[$type][] = array(
            'message'   => $message,
            'backtrace' => $backtrace,
            'runtime'   => self::get('runtime'),
            'memory'    => self::get('curmemory')
        );
    }

    public static function clear()
    {
        self::$messages['info'] = array();
    }

    public static function info($message)
    {

        self::add($message, self::LOG_INFO);
    }

    public static function warning($message)
    {
        self::add($message, self::LOG_WARNING);
    }

    public static function error($message)
    {
        self::add($message, self::LOG_ERROR);
    }

    public static function get($which)
    {
        switch ($which) {
            case 'runtime':
                return round(microtime(true) - self::$startTime, 8);
                break;

            case 'memory':
            case 'ram':
                $units      = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
                $memory     = memory_get_usage(true) - self::$startRam;
                $power      = $memory > 0 ? floor(log($memory, 1024)) : 0;
                $current    = number_format($memory / pow(1024, $power), 2, '.', ',') . $units[$power];
                $toppower   = floor(log(memory_get_peak_usage(true), 1024));
                $topmemory  = number_format(memory_get_peak_usage(true) / pow(1024, $toppower), 2, '.', ',') . $units[$toppower];
                return $current . '/' . $topmemory;
                break;

            case 'curmemory':
                $units  = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
                $memory = memory_get_usage(true) - self::$startRam;
                $power  = $memory > 0 ? floor(log($memory, 1024)) : 0;
                return number_format($memory / pow(1024, $power), 2, '.', ',') . $units[$power];
                break;

            case 'total':
                return self::$messages;
                break;

            case 'info':
                return self::$messages[self::LOG_INFO];
                break;

            case 'warning':
            case 'warn':
                return self::$messages[self::LOG_WARNING];
                break;

            case 'error':
                return self::$messages[self::LOG_ERROR];
                break;
        }
    }
}
