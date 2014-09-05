<?php
namespace Mollie;
use Sumo;
use App;
class ControllerMollie extends App\Controller
{
    public function index()
    {
        $this->document->setTitle(Sumo\Language::getVar('APP_MOLLIE_TITLE'));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('common/apps', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_MOLLIE_TITLE')
        ));

        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['current_store'] = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;

        $this->load->model('localisation/language');
        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('localisation/order_status');
        $this->data['statusses'] = $this->model_localisation_order_status->getOrderStatuses();


        $this->load->appModel('Settings');
        $this->load->appModel('Api');

        $settings = $this->mollie_model_settings->getSettings($this->data['current_store']);
        $settingsPosted = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $settingsPosted = isset($this->request->post['settings']) ? $this->request->post['settings'] : array();

            // Did the key change? If so, reset discount-setting
            if (isset($settingsPosted['general']['api_key']) && isset($settings['general']['api_key']) && $settingsPosted['general']['api_key'] != $settings['general']['api_key']) {
                foreach ($this->data['stores'] as $store) {
                    $this->mollie_model_settings->setSetting($store['store_id'], 'discount', 0);
                }
            }

            $this->mollie_model_settings->setSettings($this->data['current_store'], $settingsPosted);
            //$this->redirect($this->url->link('app/mollie', '', 'SSL'));
        }

        // Merge old with new settings
        // array_replace is not preferred because:
        // 1: The discount-setting may not be preset in the 'old' $settings
        // 2: We have no numeric keys, so the 2nd array will pretty much
        //    overwrite all the 'old' settings from $settings
        $this->data['settings'] = array_merge($settings, $settingsPosted);

        $this->load->model('localisation/geo_zone');
        $this->data['zones'] = $this->model_localisation_geo_zone->getGeoZones();
        $this->data['geo_zone_id'] = isset($this->request->get['geo_zone_id']) ? $this->request->get['geo_zone_id'] : $this->config->get('zone_id');

        $this->data['tax'] = $this->model_settings_stores->getSettings($this->data['current_store'], 'tax_percentage');
        if (!is_array($this->data['tax']) || !count($this->data['tax']) || !isset($this->data['tax']['default'])) {
            $this->data['tax'] = $this->model_settings_general->getSetting('tax_percentage');
        }

        $this->document->addStyle('../apps/mollie/admin/view/css/mollie.css');

        $this->template = 'index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        $return = array();
        $return['response'] = false;

        $check  = !empty($this->request->post['check']) ? $this->request->post['check'] : false;
        $key    = !empty($this->request->post['key']) ? $this->request->post['key'] : '';
        switch ($check) {
            case 'validate-api':
            case 'payment-options':
                if (!preg_match("!^(?:live|test)_\\w+\$!", $key)) {
                    $return['response'] = false;
                    $return['message']  = Sumo\Language::getVar('APP_MOLLIE_ERROR_INVALID_API_KEY');
                }
                else {
                    $this->load->appModel('Api');
                    $this->mollie_model_api->setKey($key);
                    try {
                        $response = $this->mollie_model_api->callMethods();
                        if (isset($response['error'])) {
                            $return['response'] = false;
                            $return['message']  = Sumo\Language::getVar('APP_MOLLIE_ERROR_' . strtoupper($response['error']['type']));
                        }
                        else {
                            $return['response'] = true;
                            if ($check == 'payment-options') {
                                $return['options']  = $response['data'];
                            }
                        }
                    }
                    catch (\Exception $e) {
                        $return['response'] = false;
                        $return['message']  = $e->getMessage();
                    }
                }
                break;

            case 'validate-discount':
                $username   = $this->request->post['username'];
                $password   = $this->request->post['password'];
                $errors     = array();
                if (empty($username)) {
                    // code 20
                    $errors[] = Sumo\Language::getVar('APP_MOLLIE_ERROR_USERNAME_EMPTY');
                }
                if (empty($password)) {
                    // code 21
                    $errors[] = Sumo\Language::getVar('APP_MOLLIE_ERROR_PASSWORD_EMPTY');
                }

                if (!count($errors)) {
                    $this->load->appModel('Api');
                    $result = $this->mollie_model_api->addDiscount($username, $password);
                    if ($result['resultcode'] == 10) {
                        $return['response'] = true;
                        $this->load->appModel('settings');
                        foreach ($this->model_settings_stores->getStores() as $list) {
                            $this->mollie_model_settings->setSetting($list['store_id'], 'discount', 1);
                        }
                    }
                    else {
                        $return['response'] = false;
                        $return['text']     = Sumo\Language::getVar('APP_MOLLIE_ERROR_RESELLER_CODE_' . $result['resultcode']);
                    }
                }
                else {
                    $return['response'] = false;
                    $return['text'] = implode('<br />', $errors);
                }
                break;

            case 'ignore-discount':
                $this->load->appModel('Settings');
                $this->load->appModel('settings');
                foreach ($this->model_settings_stores->getStores() as $list) {
                    $this->mollie_model_settings->setSetting($list['store_id'], 'discount', -1);
                }
                break;

            default:
                break;
        }

        $this->response->setOutput(json_encode($return));
    }
}
