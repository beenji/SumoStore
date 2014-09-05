<?php
namespace Sumo;
class ControllerReportDashboard extends Controller
{
    public function index()
    {
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD')));
        $this->template = 'dashboard/index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}
