<?php
namespace Multisafepay;
use App;
use Sumo;

class ControllerMultisafepay extends App\Controller
{
    public function index()
    {
        $this->document->setTitle(Sumo\Language::getVar('APP_MULTISAFEPAY_TITLE'));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('common/apps', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_MULTISAFEPAY_TITLE')
        ));

        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['current_store'] = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;

        $this->load->model('localisation/language');
        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('localisation/order_status');
        $this->data['statusses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->data['link_msp'] = 'http://www.multisafepay.com/en/';

        $this->load->appModel('Settings');
        $this->load->appModel('Api');
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->multisafepay_model_settings->setSettings($this->data['current_store'], $this->request->post['settings']);
            //$this->redirect($this->url->link('app/multisafepay', '', 'SSL'));
        }
        $this->data['settings'] = $this->multisafepay_model_settings->getSettings($this->data['current_store']);


        $this->load->model('localisation/geo_zone');
        $this->data['zones'] = $this->model_localisation_geo_zone->getGeoZones();
        $this->data['geo_zone_id'] = isset($this->request->get['geo_zone_id']) ? $this->request->get['geo_zone_id'] : $this->config->get('zone_id');

        $this->data['tax'] = $this->model_settings_stores->getSettings($this->data['current_store'], 'tax_percentage');
        if (!is_array($this->data['tax']) || !count($this->data['tax']) || !isset($this->data['tax']['default'])) {
            $this->data['tax'] = $this->model_settings_general->getSetting('tax_percentage');
        }

        $this->document->addScript('../apps/multisafepay/admin/view/js/multisafepay.js');

        $this->template = 'index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        $this->load->appModel('Api');
        $this->load->appModel('Settings');

        $response = array();
        //$response['debug'] = 'starting..';
        $settings = $this->multisafepay_model_settings->getSettings($this->request->post['store_id']);

        // Combine existing settings with posted settings
        if (isset($this->request->post['settings'])) {
            $settings = array_merge($settings, $this->request->post['settings']);
        }

        $this->multisafepay_model_api->setSettings($settings);
        $mspResponse = $this->multisafepay_model_api->getGateways();

        if (is_array($mspResponse['gateways']['gateway']) && !empty($mspResponse['gateways']['gateway'])) {
            foreach ($mspResponse['gateways']['gateway'] as $gateway) {
                $this->data['gateways'][] = array(
                    'code'      => mb_strtolower($gateway['id']),
                    'lang_code' => mb_strtoupper($gateway['id']),
                    'label'     => $gateway['description']
                );
            }

            $this->load->model('localisation/geo_zone');
            $this->data['zones'] = $this->model_localisation_geo_zone->getGeoZones();
            $this->data['geo_zone_id'] = isset($this->request->get['geo_zone_id']) ? $this->request->get['geo_zone_id'] : $this->config->get('zone_id');
            $this->data['settings'] = $settings;

            $this->template = 'gateways.tpl';
            $this->response->setOutput($this->render());
        }
    }
}
