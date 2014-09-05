<?php
namespace Widgetsimplefooter;
use App;
use Sumo;
class ControllerWidgetsimplefooter extends App\Controller
{
    public function index($var)
    {
        $settings = $this->config->get('footer_' . $this->config->get('template'));
        $this->data['settings'] = $settings;
        $this->template = 'footer.tpl';
        $this->output = $this->render();
    }

    public function get($type)
    {
        $this->template = $type . '.tpl';
        return $this->render();
    }
}
