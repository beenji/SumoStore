<?php
namespace Sumo;
class Database extends Singleton
{
    public static $instance, $prefix, $connection, $queryCount, $tmpQueries, $lastSQL;

    public static function setup($settings = array())
    {
        self::getInstance();
        self::$queryCount = 0;

        if (!isset($settings['password'])) {
            $settings['password'] = '';
        }

        if (!isset($settings['prefix'])) {
            $settings['prefix'] = 'sumo_';
        }

        if (!isset($settings['port'])) {
            $settings['port'] = 3306;
        }

        try {
            self::$connection = new \PDO(
                'mysql:dbname=' . $settings['database'] .
                    ';host=' . $settings['hostname'] .
                    ';port=' . $settings['port'] .
                    ';charset=utf8',
                $settings['username'],
                $settings['password']
            );
        }
        catch(\PDOException $e) {
            throw new \Exception ('Could not connect to database. Error: ' . $e->getMessage());
            //trigger_error('Could not connect to database. ' . $e->getMessage());
            //exit('There was a problem connecting to the database, which is required to run SumoStore. Please contact the webmaster.');
        }

        self::$prefix = rtrim($settings['prefix'], '_') . '_';

        self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        //self::$connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    public static function query($sql, $vars = array())
    {
        $profiler = microtime(true);
        self::$queryCount++;
        if (count(self::$tmpQueries) >= 500) {
            self::$tmpQueries = array();
        }
        self::$tmpQueries[] = $sql;

        if (!self::$connection) {
            if (class_exists('Sumo\\Logger')) {
                Logger::warning('[DB] No connection; aborting');
            }
            return false;
        }

        $sql = str_replace('PREFIX_', self::$prefix, $sql);
        if (!is_array($vars) || !count($vars)) {
            try {
                if (class_exists('Sumo\\Logger')) {
                    if (isset(self::$prefix)) {
                        $sql = str_replace('PREFIX_', self::$prefix, $sql);
                    }
                    Logger::info('[DB] Query to perform: ' . $sql);
                }
                $stmt = self::$connection->query($sql);
            }
            catch (\PDOException $e) {
                $file = $func = $line = 'unknown';
                $trace = debug_backtrace();
                if (isset($trace[1])) {
                    $file = $trace[1]['file'] ? $trace[1]['file'] : $file;
                    $line = $trace[1]['line'] ? $trace[1]['line'] : $line;
                }
                if (isset($trace[2])) {
                    $func = $trace[2]['function'] ? $trace[2]['function'] : $func;
                }
                $debugPHP = 'File: ' . $file . '<br />Function: ' . $func . '<br />Line: ' . $line . '<br />Error: ' . $e->getMessage() . '<br />SQL: ' . $sql;
                //trigger_error('SQL query failed! ' . $e->getMessage() . '<br />' . $sql . '<br />' . $debugPHP);
                throw new \Exception($debugPHP);
                $stmt = false;
            }
            if (class_exists('Sumo\\Logger')) {
                Logger::info('[DB] Returning result, took ' . round(microtime(true) - $profiler, 8) . ' seconds');
            }
            return $stmt;
        }
        else {
            $debugSQL = $sql;
            try {
                $stmt = self::$connection->prepare($sql);
            }
            catch (\PDOException $e) {
                $file = $func = $line = 'unknown';
                $trace = debug_backtrace();
                if (isset($trace[1])) {
                    $file = $trace[1]['file'] ? $trace[1]['file'] : $file;
                    $line = $trace[1]['line'] ? $trace[1]['line'] : $line;
                }
                if (isset($trace[2])) {
                    $func = $trace[2]['function'] ? $trace[2]['function'] : $func;
                }
                $debugPHP = 'File: ' . $file . '<br />Function: ' . $func . '<br />Line: ' . $line . '<br />Error: ' . $e->getMessage() . '<br />SQL:' . $debugSQL;
                //trigger_error('SQL query failed! ' . $e->getMessage() . '<br />' . $debugSQL . '<br />' . $debugPHP);
                throw new \Exception($debugPHP);
                $stmt = false;
            }

            if ($stmt) {
                foreach ($vars as $key => $value) {
                    if (stristr($sql, ':' . $key)) {
                        $type = \PDO::PARAM_STR;

                        if (is_int($value)) {
                            $type = \PDO::PARAM_INT;
                        }
                        else if(is_bool($value)) {
                            $type = \PDO::PARAM_BOOL;
                        }
                        else if (is_null($value) && !empty($value)) {
                            //$type = \PDO::PARAM_NULL;
                        }
                        $debugSQL = str_replace(':' . $key, $value, $debugSQL);
                        $stmt->bindValue(':' . $key, $value, $type);
                    }
                }
                if (class_exists('Sumo\\Logger')) {
                    if (isset(self::$prefix)) {
                        $sql = str_replace('PREFIX_', self::$prefix, $sql);
                    }
                    Logger::info('[DB] Query to perform: ' . $debugSQL);
                }
                self::$lastSQL = $debugSQL;
                $stmt->setFetchMode(\PDO::FETCH_ASSOC);
                try {
                    $stmt->execute();
                }
                catch (\PDOException $e) {
                    $file = $func = $line = 'unknown';
                    $trace = debug_backtrace();
                    if (isset($trace[1])) {
                        $file = $trace[1]['file'] ? $trace[1]['file'] : $file;
                        $line = $trace[1]['line'] ? $trace[1]['line'] : $line;
                    }
                    if (isset($trace[2])) {
                        $func = $trace[2]['function'] ? $trace[2]['function'] : $func;
                    }
                    $debugPHP = 'File: ' . $file . '<br />Function: ' . $func . '<br />Line: ' . $line . '<br />Error: ' . $e->getMessage() . '<br />SQL:' . $debugSQL;
                    throw new \Exception($debugPHP);
                }
            }

            if (class_exists('Sumo\\Logger')) {
                Logger::info('[DB] Returning result, took ' . round(microtime(true) - $profiler, 8) . ' seconds');
            }
            return $stmt;
        }
    }

    public static function fetchAll($sql, $vars = array())
    {
        $stmt = self::query($sql, $vars);
        $return = array();
        $nr = 0;
        while ($list = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $key = $nr++;
            $return[$key] = $list;
        }

        return $return;
    }

    public static function delete($table, $where)
    {
        if (empty($table) || empty($where)) {
            return false;
        }
        self::query("DELETE FROM " . $table . " WHERE " . $where);
    }

    public static function insert($table, $fieldsArray)
    {
        $fields = $keys = '';
        foreach ($fieldsArray as $key => $value) {
            $fields .= $key . ',';
            $keys .= ':' . $key . ',';
        }
        $fields = rtrim($fields, ',');
        $keys = rtrim($keys, ',');

        self::query("INSERT INTO " . $table . "(" . $fields . ") VALUES (" . $keys . ")", $fieldsArray);

        return self::$connection->lastInsertId();
    }

    public static function lastInsertId()
    {
        return self::$connection->lastInsertId();
    }
}
