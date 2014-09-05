<?php
namespace Sumo;
abstract class Controller
{
    protected $registry;
    protected $id;
    protected $layout;
    protected $template;
    protected $children = array();
    protected $data = array();
    protected $output;

    public function __construct($registry)
    {
        $this->registry = $registry;
        if (defined('DIR_CATALOG')) {
            $this->load->model('settings/general');
            $this->load->model('settings/stores');
            $this->load->model('settings/menu');
        }

        Logger::info('called: ' . get_class($this));
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function setParent($active)
    {
        $this->model_settings_menu->setActive($active);
    }

    protected function forward($route, $args = array())
    {
        return new Action($route, $args);
    }

    protected function redirect($url, $status = 302)
    {
        header('Status: ' . $status);
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
        exit();
    }

    protected function getChild($child, $args = array())
    {
        Logger::info('child: ' . $child);
        $action = new Action($child, $args);

        if (file_exists($action->getFile())) {
            if (!class_exists($action->getClass())) {
                require_once($action->getFile());
            }

            $class = $action->getClass();
            $controller = new $class($this->registry);
            if (method_exists($class, $action->getMethod())) {
                $controller->{$action->getMethod()}($action->getArgs());
                return $controller->output;
            }
            else {
                trigger_error('Warning: Method ' . $class . '->' . $action->getMethod() . '(' . $action->getArgs() . ') does not exist', E_USER_WARNING);
                return $this->output;
            }

        }
        else {

            if (isset($this->data['header'])) {
                $this->output .= $this->data['header'];
            }
            if (isset($this->data['footer'])) {
                $this->output .= $this->data['footer'];
            }
            trigger_error('Error: Could not load controller ' . $child . '! (File: ' . $action->getFile() . ')', E_USER_ERROR);
            return $this->output;
        }
    }

    protected function render()
    {
        Logger::info('rendering now');
        foreach ($this->children as $child) {
            $test = explode('/', $child);
            if (count($test) == 3) {
                $this->data[$test[0]][$test[1]][$test[2]] = $this->getChild($child);
            }
            else {
                $this->data[basename($child)] = $this->getChild($child);
            }
        }

        $original = $this->template;

        // First try if the full/exact path is given
        if (!file_exists(DIR_TEMPLATE . $this->template)) {
            $this->template = $this->config->get('template') . '/template/' . $this->template;
        }

        // Secondly try if the base template file exists
        if (!file_exists(DIR_TEMPLATE . $this->template)) {
            $this->template = str_replace($this->config->get('template') . '/template/', 'base/template/', $this->template);
        }

        if (file_exists(DIR_TEMPLATE . $this->template)) {
            extract($this->data);
            ob_start();
            require(DIR_TEMPLATE . $this->template);
            $this->output = ob_get_contents();
            ob_end_clean();
            Logger::info('rendering completed');

            return str_replace('</head>', '<meta name="generator" content="SumoStore" /></head>', $this->output);
        }
        else {
            trigger_error('Error: Could not load template ' . DIR_TEMPLATE . $original . '!', E_USER_ERROR);
            if (isset($this->data['header'])) {
                $this->output .= $this->data['header'];
            }
            if (isset($this->data['footer'])) {
                $this->output .= $this->data['footer'];
            }

            return $this->output;
        }
    }
}

namespace App;
use Sumo;
class Controller extends Sumo\Controller
{
    public function __construct($registry)
    {
        $this->registry = $registry;
        $app = $this->getCurrentApp();
        $this->registry->set('currentApp', $app);
    }

    final private function getCurrentApp()
    {
        $parts = explode('\\', get_called_class());
        return $parts[0];
    }

    final protected function render()
    {
        Sumo\Logger::info('rendering app now');
        foreach ($this->children as $child) {
            $this->data[basename($child)] = $this->getChild($child);
        }

        $this->registry->set('currentApp', $this->getCurrentApp());

        $dir = 'apps/' . strtolower($this->registry->get('currentApp'));
        $template = $this->template;

        $this->template = DIR_TEMPLATE . $this->config->get('template') . '/template/' . $dir . '/' . $this->template;

        // First try if the full/exact path is given
        if (!file_exists($this->template)) {
            //trigger_error('Error 1: Could not load template ' . $this->template . '!', E_USER_ERROR);
            $this->template = str_replace($this->config->get('template') . '/template/', 'base/template/', $this->template);
        }

        if (!file_exists($this->template)) {
            //trigger_error('Error 2: Could not load template ' . $this->template . '!', E_USER_ERROR);
            $dir = DIR_HOME . $dir;
            $test = explode('/', trim(DIR_APPLICATION, '/'));
            $test = end($test);
            if ($test == 'admin') {
                $dir .= '/admin/';
            }
            else {
                $dir .= '/catalog/';
            }
            $dir .= 'view/template/';
            $this->template = $dir . $template;
        }

        if (file_exists($this->template)) {
            extract($this->data);
            ob_start();
            require($this->template);
            $this->output = ob_get_contents();
            ob_end_clean();
            Sumo\Logger::info('Rendering for ' . strtolower($this->registry->get('currentApp')) . ' finished; loaded template ' . $this->template);
            return str_replace('</head>', '<meta name="generator" content="SumoStore" /></head>', $this->output);
        }
        else {
            trigger_error('Error: Could not load template ' . $this->template . ' for ' . strtolower($this->registry->get('currentApp')) . '!', E_USER_ERROR);

            if (isset($this->data['header'])) {
                $this->output .= $this->data['header'];
            }
            if (isset($this->data['footer'])) {
                $this->output .= $this->data['footer'];
            }
            return $this->output;
        }
    }
}
