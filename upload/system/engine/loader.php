<?php
final class Loader
{
    protected $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function library($library)
    {
        $file = DIR_SYSTEM . 'library/' . $library . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load library ' . $library . '!', E_USER_ERROR);
        }
    }

    public function helper($helper)
    {
        $file = DIR_SYSTEM . 'helper/' . $helper . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load helper ' . $helper . '!', E_USER_ERROR);
        }
    }

    public function model($model)
    {
        $file   = DIR_APPLICATION . 'model/' . $model . '.php';
        $class  = 'Sumo\Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
        $call   = 'model_' . str_replace('/', '_', $model);
        if ($this->registry->has($call)) {
            return;
        }
        if (file_exists($file)) {
            include_once($file);

            $this->registry->set($call, new $class($this->registry));
        } else {
            trigger_error('Error: Could not load model ' . $model . '!', E_USER_ERROR);
        }
    }

    public function appModel($model)
    {
        $file = DIR_HOME . 'apps/' . strtolower($this->registry->get('currentApp')) . '/model/' . strtolower($model) . '.php';
        $class = $this->registry->get('currentApp') . '\Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
        $call = strtolower($this->registry->get('currentApp')) . '_model_' . str_replace('/', '_', strtolower($model));
        if ($this->registry->has($call)) {
            return;
        }
        if (file_exists($file)) {
            include_once($file);
            $this->registry->set($call, new $class($this->registry));
        }
        else {
            trigger_error('Error: Could not load model ' . $model . '!' . PHP_EOL . 'File: ' . $file, E_USER_ERROR);
        }
    }

    public function database($driver, $hostname, $username, $password, $database)
    {
        $file  = DIR_SYSTEM . 'database/' . $driver . '.php';
        $class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);

        if (file_exists($file)) {
            include_once($file);

            $this->registry->set(str_replace('/', '_', $driver), new $class($hostname, $username, $password, $database));
        } else {
            trigger_error('Error: Could not load database ' . $driver . '!', E_USER_ERROR);
        }
    }

    public function config($config)
    {
        $this->config->load($config);
    }

    public function language($language)
    {
        return $this->language->load($language);
    }
}
