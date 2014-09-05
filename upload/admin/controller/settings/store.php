<?php
namespace Sumo;

class ControllerSettingsStore extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SHOP_SETTINGS'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SHOP_SETTINGS')));
        $this->setParent('settings/store/index');
        $results = $this->model_settings_stores->getStores();
        foreach ($results as $list) {
            $list['selected']   = isset($this->request->post['selected']) && in_array($list['store_id'], $this->request->post['selected']);
            $list['edit']       = $this->url->link('settings/store/update', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL');
            $this->data['stores'][] = $list;
        }

        $this->template = 'settings/store/list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function general()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_GENERAL_SETTINGS'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_GENERAL_SETTINGS')));
        $this->document->addScript('view/js/pages/settings_form.js');

        $this->load->model('catalog/information');
        $this->load->model('localisation/country');
        $this->load->model('localisation/currency');
        $this->load->model('localisation/language');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/stock_status');
        $this->load->model('localisation/return_status');
        $this->load->model('sale/customer');
        $this->load->model('sale/customer_group');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            unset($this->request->post['group']);
            $fp = @fopen(DIR_HOME . 'license.php', 'w');
            if (!$fp) {
                Logger::error(Language::getVar('SUMO_ADMIN_WARNING_LICENSE_FILE_UNWRITEABLE'));
                $this->data['warning'] = Language::getVar('SUMO_ADMIN_WARNING_LICENSE_FILE_UNWRITEABLE');
            }
            else {
                $this->request->post['license_key'] = preg_replace('/[^A-Za-z0-9-]/', '', $this->request->post['license_key']);
                fwrite($fp, "<?php define('LICENSE_KEY', '" . $this->request->post['license_key'] . "');");
                fclose($fp);
            }
            unset($this->request->post['license_key']);
            $this->model_settings_general->setSettings($this->request->post);
        }
        $this->data['form']         = 'general';
        $this->data['token']        = $this->session->data['token'];
        $this->data['countries']    = $this->model_localisation_country->getCountries();
        $this->data['currencies']   = $this->model_localisation_currency->getCurrencies();
        $this->data['languages']    = $this->model_localisation_language->getLanguages();
        $this->data['settings']     = $this->model_settings_general->getSettings();
        $this->data['settings']['license_key'] = LICENSE_KEY;
        $this->data['informations'] = $this->model_catalog_information->getInformations();
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
        $this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();

        foreach ($this->model_sale_customer_group->getCustomerGroups() as $list) {
            $list['amount'] = $this->model_sale_customer->getTotalCustomersByCustomerGroupId($list['customer_group_id']);
            $list['url']    = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&customer_group_id=' . $list['customer_group_id'], 'SSL');
            $this->data['customer_groups'][$list['customer_group_id']] = $list;
        }

        $this->template = 'settings/store/general.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function remove()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('settings/stores');
            foreach ($this->request->post['selected'] as $id) {
                if (!empty($id)) {
                    $this->model_settings_stores->removeStore($id);
                }
            }
        }
        $this->redirect($this->url->link('settings/store'));
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SHOP_SETTINGS_ADD'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SHOP_SETTINGS'), 'href' => $this->url->link('settings/store', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SHOP_SETTINGS_ADD')));
        $this->document->addScript('view/js/pages/settings_form.js');
        $this->document->addStyle('view/css/pages/settings.css');
        $this->setParent('settings/store/index');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            unset($this->request->post['group']);
            $this->model_settings_stores->setSettings('new', $this->request->post);
            Cache::removeAll();
        }

        $this->renderAndValidateForm();
        $this->data['action'] = $this->url->link('settings/store/insert', 'token=' . $this->session->data['token'], 'SSL');


        $this->template = 'settings/store/general.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SHOP_SETTINGS_UPDATE'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SHOP_SETTINGS'), 'href' => $this->url->link('settings/store', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SHOP_SETTINGS_UPDATE')));
        $this->document->addScript('view/js/pages/settings_form.js');
        $this->document->addStyle('view/css/pages/settings.css');
        $this->setParent('settings/store/index');

        $store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        if (!is_numeric($store_id)) {
            $this->redirect($this->url->link('settings/store', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->data['current_store'] = $store_id;

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            unset($this->request->post['group']);
            $this->model_settings_stores->setSettings($store_id, $this->request->post);
            Cache::removeAll();
        }

        $this->renderAndValidateForm($store_id, true);
        $this->data['action'] = $this->url->link('settings/store/update', 'token=' . $this->session->data['token'] . '&store_id=' . $store_id, 'SSL');

        $this->template = 'settings/store/general.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function renderAndValidateForm($store_id = null, $edit = false)
    {
        $this->load->model('catalog/information');
        $this->load->model('localisation/country');
        $this->load->model('localisation/currency');
        $this->load->model('localisation/language');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/stock_status');
        $this->load->model('localisation/return_status');
        $this->load->model('sale/customer');
        $this->load->model('sale/customer_group');
        $this->data['token']            = $this->session->data['token'];
        $this->data['form']             = 'store';
        $this->data['countries']        = $this->model_localisation_country->getCountries();
        $this->data['currencies']       = $this->model_localisation_currency->getCurrencies();
        $this->data['languages']        = $this->model_localisation_language->getLanguages();
        $this->data['default']          = $this->model_settings_general->getSettings();
        $this->data['informations']     = $this->model_catalog_information->getInformations();
        $this->data['order_statuses']   = $this->model_localisation_order_status->getOrderStatuses();
        $this->data['stock_statuses']   = $this->model_localisation_stock_status->getStockStatuses();
        $this->data['return_statuses']  = $this->model_localisation_return_status->getReturnStatuses();
        foreach ($this->model_sale_customer_group->getCustomerGroups() as $list) {
            $list['amount'] = $this->model_sale_customer->getTotalCustomersByCustomerGroupId($list['customer_group_id']);
            $list['url']    = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&customer_group_id=' . $list['customer_group_id'], 'SSL');
            $this->data['customer_groups'][$list['customer_group_id']] = $list;
        }
        $this->data['templates']        = array();
        foreach (glob(DIR_HOME . 'catalog/view/theme/*') as $dir) {
            $file = $dir . '/information.php';
            $name = str_replace(DIR_HOME . 'catalog/view/theme/', '', $dir);
            if (file_exists($file)) {
                include_once($file);
                if (!isset($template[$name])) {
                    Logger::warning('Template "' . $name . '" does have information.php, but its information could not be loaded');
                    $template[$name] = array('directory' => $name, 'name' => array($this->config->get('language_id') => $name . ': ' . Language::getVar('SUMO_NOUN_TEMPLATE_ERROR')));
                }
                else {
                    $template[$name]['directory'] = $name;
                }
                $this->data['templates'][] = $template[$name];
            }
            else {
                Logger::warning('Template "' . $name . '" does not have information.php');
                $this->data['templates'][] = array('directory' => $name, 'name' => array($this->config->get('language_id') => $name . ': ' . Language::getVar('SUMO_NOUN_TEMPLATE_ERROR')));
            }
        }

        if (count($this->data['templates']) == 0) {
            Logger::error('There wasnt a single theme that could be loaded, what is going on?');
        }

        $default = $this->data['default'];
        if ($store_id == null || !$edit) {
            $settings = array();
            if ($store_id == null) {
                Logger::info('store_id seems null');
            }
            else {
                Logger::info('!edit');
            }
        }
        else {
            $settings = $this->model_settings_stores->getSettings($store_id);
            if (count($settings) == 0) {
                $store = $this->model_settings_stores->getStore($store_id);
                if (is_array($store) && !empty($store['name'])) {
                    Logger::info('There are currently no settings for this store, but the store does exist');
                    foreach ($store as $key => $value) {
                        $settings[$key] = $value;
                    }
                }
                else {
                    Logger::error('This store does not exist!');
                }
            }
            else {
                Logger::info('Settings for store ' . $store_id . ' loaded');
            }
        }

        foreach ($default as $key => $value) {
            if (!isset($settings[$key])) {
                //$settings[$key] = $value;
            }
        }
        $this->data['settings'] = $settings;

        $this->document->addStyle('view/css/pages/uploader.css');
        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');
    }

    public function ajax()
    {
        $action     = !empty($this->request->post['action']) ? $this->request->post['action'] : 'none';
        $response   = array();
        switch ($action) {
            case 'group_info':
                $this->load->model('sale/customer_group');
                if (!empty($this->request->post['customer_group_id'])) {
                    $response['data'] = $this->model_sale_customer_group->getCustomerGroup($this->request->post['customer_group_id']);
                    $response['data']['info'] = $this->model_sale_customer_group->getCustomerGroupDescriptions($this->request->post['customer_group_id']);
                }
                break;

            case 'group_info_save':
                $this->load->model('sale/customer_group');
                parse_str(str_replace('&amp;', '&', $this->request->post['data']), $data);
                foreach ($data['group']['name'] as $lang_id => $value) {
                    $data['group']['customer_group_description'][$lang_id]['name'] = $value;
                }
                foreach ($data['group']['description'] as $lang_id => $value) {
                    $data['group']['customer_group_description'][$lang_id]['description'] = $value;
                }
                $data = $data['group'];
                if (!empty($this->request->post['customer_group_id'])) {
                    $return = $this->model_sale_customer_group->editCustomerGroup($this->request->post['customer_group_id'], $data);
                    if ($return) {
                        $response['title'] = Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_SAVED_TITLE');
                        $response['text'] = Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_SAVED_CONTENT');
                        $response['ok'] = true;
                    }
                    else {
                        $response['text'] = Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_SAVE_ERROR_CONTENT');
                        $response['title'] = Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_SAVE_ERROR_TITLE');
                        $response['ok'] = false;
                    }
                }
                else {
                    $return = $this->model_sale_customer_group->addCustomerGroup($data);
                    $response['title'] = Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_SAVED_TITLE');
                    $response['text'] = Language::getVar('SUMO_NOUN_CUSTOMER_GROUP_SAVED_CONTENT');
                    $response['customer_group_id'] = $return;
                    $response['ok'] = true;
                }
                break;

            case 'group_remove':
                if (!empty($this->request->post['customer_group_id']) && $this->request->post['customer_group_id'] > 1) {
                    $this->load->model('sale/customer_group');
                    $this->model_sale_customer_group->deleteCustomerGroup($this->request->post['customer_group_id']);
                    $response['ok'] = true;
                }
                else {
                    $response['title'] = Language::getVar('SUMO_NOUN_CUSTOMER_REMOVE_ERROR_TITLE');
                    $response['text'] = Language::getVar('SUMO_NOUN_CUSTOMER_REMOVE_ERROR_CONTENT');
                }
                break;

            case 'get_zone':
                $this->load->model('localisation/zone');
                if (!empty($this->request->post['country_id'])) {
                    $response['zone'] = $this->model_localisation_zone->getZonesByCountryId($this->request->post['country_id']);
                    $zone_id = $this->model_settings_general->getSetting('zone_id');
                    if (isset($this->request->post['store'])) {
                        $check = $this->model_settings_stores->getSetting($this->request->post['store'], 'zone_id');
                        if (!empty($check)) {
                            $zone_id = $check;
                        }
                    }
                    foreach ($response['zone'] as $key => $list) {
                        if ($list['zone_id'] == $zone_id) {
                            $response['zone'][$key]['selected'] = true;
                        }
                    }
                    $response['default'] = $zone_id;
                }
                break;

            case 'preview':
                if ($this->request->post['type'] != 'timezone') {
                    if ($this->request->post['type'] == 'time') {
                        if ($this->request->post['value'] != '%X') {
                            setlocale(LC_TIME, '');
                        }
                    }
                    $response['value'] = strftime($this->request->post['value']);
                }
                else {
                    $default = date_default_timezone_get();
                    date_default_timezone_set($this->request->post['value']);
                    $response['value'] = strftime('%x %X');
                    date_default_timezone_set($default);
                }
                break;
        }

        $this->response->setOutput(json_encode($response));
    }
}
