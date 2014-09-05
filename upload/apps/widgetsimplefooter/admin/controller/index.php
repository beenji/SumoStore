<?php
namespace Widgetsimplefooter;
use App;
use Sumo;
class ControllerWidgetsimplefooter extends App\Controller
{
    public function index()
    {
        $this->redirect($this->url->link('settings/themes', '', 'SSL'));
    }
}
