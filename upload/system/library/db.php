<?php
class DB
{
    private $driver;
    public $count, $tmpQueries;

    public function __construct($driver, $hostname, $username, $password, $database)
    {
        if (file_exists(DIR_DATABASE . $driver . '.php')) {
            require_once(DIR_DATABASE . $driver . '.php');
        } else {
            exit('Error: Could not load database file ' . $driver . '!');
        }
        $this->count = 0;
        $this->driver = new $driver($hostname, $username, $password, $database);
    }

    public function query($sql)
    {
        $this->count++;
        $this->tmpQueries[] = $sql;
        Sumo\Logger::warning('[OLD DB] Warning, query through the old DB interface. Switch to the new Database class');
        return $this->driver->query($sql);
    }

    public function escape($value)
    {
        return $this->driver->escape($value);
    }

    public function countAffected()
    {
        return $this->driver->countAffected();
    }

    public function getLastId()
    {
        return $this->driver->getLastId();
    }
}
