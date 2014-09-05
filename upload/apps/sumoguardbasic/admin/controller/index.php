<?php
namespace Sumoguardbasic;
use Sumo;
use App;
class ControllerSumoguardbasic extends App\Controller
{
    public function index()
    {
        $this->load->appModel('Setup');
        $this->load->appModel('Settings');

        $this->sumoguardbasic_model_setup->activate(isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0);

        $this->document->setTitle(Sumo\Language::getVar('APP_SUMOGUARDBASIC_TITLE'));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('settings/dashboard', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_SUMOGUARDBASIC_TITLE')
        ));

        $this->data['active'] = $this->sumoguardbasic_model_settings->enabled();

        $this->template = 'index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function check()
    {
        $json = array();
        $this->load->appModel('Settings');

        if (isset($this->request->post['status'])) {
            $this->sumoguardbasic_model_settings->setStatus($this->request->post['status']);
        }

        $check = $this->sumoguardbasic_model_settings->enabled();

        switch ($check) {
            case 0:
                $json['text']   = Sumo\Language::getVar('APP_SUMOGUARDBASIC_STATUS_INACTIVE');
                $json['icon']   = 'fa-frown-o';
                $json['class']  = 'has-warning';
                break;

            case 1:
                $json['text']   = Sumo\Language::getVar('APP_SUMOGUARDBASIC_STATUS_ACTIVE');
                $json['icon']   = 'fa-check';
                $json['class']  = 'has-success';
                break;

            case 2:
                $json['text']   = Sumo\Language::getVar('APP_SUMOGUARDBASIC_STATUS_NO_LICENSE_KEY');
                $json['icon']   = 'fa-exclamation-circle';
                $json['class']  = 'has-warning';
                break;

            case 3:
                $json['text']   = Sumo\Language::getVar('APP_SUMOGUARDBASIC_STATUS_INVALID_LICENSE');
                $json['icon']   = 'fa-exclamation-triangle';
                $json['class']  = 'has-error';
                break;
        }

        $this->response->setOutput(json_encode($json));
    }
}
