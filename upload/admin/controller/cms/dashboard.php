<?php
namespace Sumo;
class ControllerCMSDashboard extends Controller
{
    public function index()
    {
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_CMS_DASHBOARD')));
        $this->template = 'dashboard/index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}
