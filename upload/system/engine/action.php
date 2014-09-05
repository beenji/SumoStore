<?php
namespace Sumo;
final class Action
{
    protected $file;
    protected $class;
    protected $method;
    protected $args = array();

    public function __construct($route, $args = array())
    {
        $path = '';

        $parts = explode('/', str_replace('../', '', (string)$route));

        // check if an app is requested
        if ($parts[0] == 'app') {
            $dir = DIR_HOME . 'apps/' . $parts[1];
            if (is_dir($dir)) {
                if (!isset($parts[2]) || empty($parts[2])) {
                    $parts[2] = 'index';
                }

                // Determine it's front/backend
                $test = explode('/', trim(DIR_APPLICATION, '/'));
                $test = end($test);

                if ($test == 'admin') {
                    $file = $dir . '/admin/controller/' . $parts[2] . '.php';
                }
                else {
                    $file = $dir . '/catalog/controller/' . $parts[2] . '.php';
                }

                if (is_file($file)) {
                    if ($parts[2] == 'index') {
                        $this->class = ucfirst($parts[1]) . '\Controller' . $parts[1];
                        array_shift($parts);
                        array_shift($parts);
                        array_shift($parts);
                    }
                    else {
                        $this->class = ucfirst($parts[1]) . '\Controller' . $parts[1] . $parts[2];
                        array_shift($parts);
                        array_shift($parts);
                    }
                    $this->file = $file;
                }
                else {
                    $file = $dir . '/admin/controller/index.php';
                    if (is_file($file)) {
                        $this->class = ucfirst($parts[1]) . '\Controller' . $parts[1];
                        $this->file = $file;
                        array_shift($parts);
                        array_shift($parts);
                    }
                    else {
                        $file = $dir . '/catalog/controller/index.php';
                        if (is_file($file)) {
                            $this->class = ucfirst($parts[1]) . '\Controller' . $parts[1];
                            $this->file = $file;
                            array_shift($parts);
                            array_shift($parts);
                        }
                    }
                }

                if (!isset($this->class) && !isset($this->file)) {
                    Logger::error('The requested class/file for the (guessed) app "' . $parts[1] . '" could not be found!');
                }
            }
        }
        else {
            foreach ($parts as $part) {
                $path .= $part;

                if (is_dir(DIR_APPLICATION . 'controller/' . $path)) {
                    $path .= '/';
                    array_shift($parts);
                    continue;
                }

                if (is_file(DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php')) {
                    $this->file = DIR_APPLICATION . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';
                    $this->class = 'Sumo\Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);
                    array_shift($parts);
                    break;
                }
            }
        }

        if ($args) {
            $this->args = $args;
        }

        $method = end($parts);

        if ($method) {
            $this->method = $method;
        }
        else {
            $this->method = 'index';
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getArgs()
    {
        return $this->args;
    }
}
