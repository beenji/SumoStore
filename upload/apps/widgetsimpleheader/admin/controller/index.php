<?php
namespace Widgetsimpleheader;
use App;
use Sumo;
class ControllerWidgetsimpleheader extends App\Controller
{
    public function index()
    {
        $this->redirect($this->url->link('settings/themes', '', 'SSL'));
    }
}
