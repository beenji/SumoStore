<?php
namespace Sumo;
class ControllerSettingsDashboard extends Controller
{
	public function index()
	{
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD')));
		$this->template = 'dashboard/index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
	}
}
