<?php
class LanguageOld
{
    /* OLD, LEGACY ONLY */
    private $default = 'dutch';
    public $directory, $keys, $count;
    private $data = array();

    public function __construct($directory)
    {
        $this->count = 0;
        $this->keys = array();
        $this->directory = $directory;
    }

    public function get($key)
    {
        $this->count++;
        $value = (isset($this->data[$key]) ? $this->data[$key] : $key);
        $this->keys[] = array($key => $value);
        Sumo\Logger::warning('[OLD LANGUAGE] ' . $key . ' requested');
        return $value;
    }

    public function load($filename)
    {
        $file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();
            require($file);
            $this->data = array_merge($this->data, $_);
            return $this->data;
        }

        $file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();
            require($file);
            $this->data = array_merge($this->data, $_);
            return $this->data;
        }
        else {
            #trigger_error('Error: Could not load language ' . $filename . '!');
        }
    }
}
