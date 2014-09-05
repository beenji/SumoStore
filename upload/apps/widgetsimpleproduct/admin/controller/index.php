<?php
namespace Widgetsimpleproduct;
use App;
use Sumo;
class ControllerWidgetsimpleproduct extends App\Controller
{
    public function index()
    {
        $this->redirect($this->url->link('settings/themes', '', 'SSL'));
    }
}
