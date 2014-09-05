<?php
Namespace Sumo;
class ControllerSettingsMenu extends Controller
{
    public function index()
    {
        $this->load->model('settings/menu');
        $this->data['items'] = $this->model_settings_menu->generateMenu('', true);
        $this->data['parents'] = $this->model_settings_menu->getParentItems();

        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SETTINGS_MENU'));
        $this->document->addScript('view/js/jquery/jquery.nestable.js');
        $this->document->addScript('view/js/jquery/jquery.modalEffects.js');
        $this->document->addStyle('view/css/jquery/jquery.niftymodals.css');

        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_MENU')));

        $this->template = 'settings/menu/overview.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        Logger::info('AJAX request initiated');
        $this->load->model('settings/menu');
        $return = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            Logger::info('Request method validated');
            if (isset($this->request->post['order'])) {
                $data = $this->request->post['data'];
                $return['order'] = $this->model_settings_menu->saveMenuOrder($data) ? Language::getVar('SUMO_ADMIN_SETTINGS_MENU_ORDER_SAVED') : Language::getVar('SUMO_ADMIN_SETTINGS_MENU_ORDER_NOT_SAVED');
            }

            if (isset($this->request->post['save'])) {
                $data = $this->request->post['data'];
                $type = $this->request->post['type'];

                $save = array();
                foreach ($data as $list) {
                    $save[$list['name']] = $list['value'];
                }

                if ($type == 'add') {
                    $this->model_settings_menu->addMenuItem($save);
                }
                else {
                    $this->model_settings_menu->editMenuItem($save, $this->request->post['id']);
                }
                $return['save'] = true;
            }

            if (isset($this->request->post['get'])) {
                Logger::info('Requested information for ID: ' . $this->request->post['id']);
                $return = $this->model_settings_menu->getMenuItem($this->request->post['id']);
                Logger::info('Information found: ' . print_r($return,true));
            }

            if (isset($this->request->post['delete'])) {
                $return = $this->model_settings_menu->removeMenuItem($this->request->post['id']);
            }
        }
        $return['debug'] = true;
        Logger::info('Setting outpout to be ' . json_encode($return));
        $this->response->setOutput(json_encode($return));
    }
}
