<?php
final class Front
{
    protected $registry;
    protected $pre_action = array();
    protected $error;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function addPreAction($pre_action)
    {
        $this->pre_action[] = $pre_action;
    }

    public function dispatch($action, $error)
    {
        $this->error = $error;

        foreach ($this->pre_action as $pre_action) {
            $result = $this->execute($pre_action);
            Sumo\Logger::info('Front dispatching ' . get_class($pre_action) . ' as pre-action');

            if ($result) {
                $action = $result;
                //break;
            }
        }

        while ($action) {
            $action = $this->execute($action);
        }
    }

    private function execute($action)
    {
        if (!method_exists($action, 'getFile')) {
            return '[could not execute this action]';
        }
        if (file_exists($action->getFile())) {
            Sumo\Logger::info('Executing ' . $action->getClass() . '->' . $action->getMethod());
            require_once($action->getFile());

            $class = $action->getClass();
            try {
                if (!class_exists($class)) {
                    throw new \Exception('File not found for class ' . $class);
                }
                $controller = new $class($this->registry);
            }
            catch (\Exception $e) {
                trigger_error('Could not execute ' . $class . ', something is wrong.', E_USER_ERROR);
            }

            if (is_callable(array($controller, $action->getMethod()))) {
                $return = call_user_func_array(array($controller, $action->getMethod()), $action->getArgs());
            }
            else {
                trigger_error('Could not execute ' . $action->getClass() . '->' . $action->getMethod(), E_USER_ERROR);
                $return = $this->error;

                $this->error = '';
            }
        }
        else {
            $file = $action->getFile();
            if (empty($file)) {
                Sumo\Logger::warning('$action does not have method $action->getFile(): ' . print_r($action,true));
            }
            Sumo\Logger::warning('Could not load file ' . $action->getFile() . ' to call ' . $action->getClass() . '->' . $action->getMethod());
            $return = $this->error;

            $this->error = '';
        }

        return $return;
    }
}
