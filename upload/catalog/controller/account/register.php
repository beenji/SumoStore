<?php
namespace Sumo;
class ControllerAccountRegister extends Controller
{
    private $error = array();

    public function index()
    {
        if ($this->customer->isLogged()) {
            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_REGISTER_TITLE'));

        $this->load->model('account/customer');
        $this->load->model('account/customer_group');
        $this->load->model('localisation/country');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_account_customer->addCustomer($this->request->post);

            $this->customer->login($this->request->post['email'], $this->request->post['password']);

            unset($this->session->data['guest']);

            // Default Shipping Address
            if ($this->config->get('tax_customer') == 'shipping') {
                $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
                $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
                $this->session->data['shipping_postcode'] = $this->request->post['postcode'];
            }

            // Default Payment Address
            if ($this->config->get('tax_customer') == 'payment') {
                $this->session->data['payment_country_id'] = $this->request->post['country_id'];
                $this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
            }

            $this->redirect($this->url->link('account/success'));
        }

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),

        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_REGISTER_TITLE'),
            'href'      => $this->url->link('account/register', '', 'SSL'),
        );

        $this->data['customer_groups'] = array();
        if (is_array($this->config->get('customer_group_display'))) {
            $customer_groups = $this->model_account_customer_group->getCustomerGroups();
            foreach ($customer_groups as $customer_group) {
                if (in_array($customer_group['customer_group_id'], $this->config->get('customer_group_display'))) {
                    $this->data['customer_groups'][] = $customer_group;
                }
            }
        }

        $this->data['countries'] = $this->model_localisation_country->getCountries();

        $this->data['text_agree'] = '';
        if ($this->config->get('customer_policy_id')) {
            $this->load->model('catalog/information');
            $information_info = $this->model_catalog_information->getInformation($this->config->get('customer_policy_id'));
            if ($information_info) {
                $this->data['text_agree'] = Language::getVar('SUMO_NOUN_AGREE', array($this->url->link('information/information', 'information_id=' . $this->config->get('customer_policy_id'), 'SSL'), $information_info['title']));
            }
        }

        $this->data['warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['warning'] = $this->error['warning'];
        }
        $errors = array(
            'firstname', 'lastname', 'birthdate', 'email', 'telephone', 'mobile', 'gender', 'password', 'confirm',
            'company', 'company_id', 'tax_id', 'address_1', 'postcode', 'city', 'country', 'zone',
            'password', 'confirm'
        );

        foreach ($errors as $error) {
            $this->data['error_' . $error] = '';
            if (isset($this->error[$error])) {
                $this->data['error_' . $error] = $this->error[$error];
            }
        }

        $fields = array(
            'firstname', 'middlename', 'lastname', 'birthdate', 'email', 'telephone', 'mobile', 'gender', 'fax',
            'customer_group_id', 'company', 'company_id', 'tax_id', 'address_1', 'address_2', 'postcode', 'city', 'country_id', 'zone_id',
            'password', 'confirm', 'number',
            'newsletter'
        );

        foreach ($fields as $field) {
            $this->data[$field] = '';
            if (isset($this->request->post[$field])) {
                $this->data[$field] = $this->request->post[$field];
            }
        }

        $this->data['gender'] = 'm';
        if (isset($this->request->post['gender'])) {
            $this->data['gender'] = $this->request->post['gender'];
        }

        $this->data['agree'] = false;
        if (isset($this->request->post['agree'])) {
            $this->data['agree'] = $this->request->post['agree'];
        }

        if (empty($this->data['country_id'])) {
            $this->data['country_id'] = $this->config->get('country_id');
        }

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }

        $token = md5(microtime(true) . $_SERVER['REMOTE_ADDR']);
        $this->data['token_pc'] = $token;
        $this->session->data['pc'] = $token;

        $this->template = 'account/register.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        $this->load->model('account/customer_group');
        $this->load->model('catalog/information');
        $this->load->model('localisation/country');

        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error['firstname'] = Language::getVar('SUMO_NOUN_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error['lastname'] = Language::getVar('SUMO_NOUN_ERROR_LASTNAME');
        }

        if (empty($this->request->post['gender'])) {
            $this->error['gender'] = Language::getVar('SUMO_NOUN_ERROR_GENDER');
        }

        if (empty($this->request->post['birthdate']) || (utf8_strlen($this->request->post['birthdate']) != 10)) {
            $this->error['birthdate'] = Language::getVar('SUMO_NOUN_ERROR_BIRTHDATE');
        }

        if (!filter_var($this->request->post['email'], \FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = Language::getVar('SUMO_NOUN_ERROR_EMAIL');
        }

        if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = Language::getVar('SUMO_NOUN_ERROR_EMAIL_IN_USE', $this->url->link('account/login', '', 'SSL'));
        }

        $this->request->post['telephone'] = preg_replace('/([^\d]+)/', '', str_replace('+', '00', $this->request->post['telephone']));
        $this->request->post['mobile'] = preg_replace('/([^\d]+)/', '', str_replace('+', '00', $this->request->post['mobile']));

        if ((utf8_strlen($this->request->post['telephone']) < 8) || (utf8_strlen($this->request->post['telephone']) > 15)) {
            $this->error['telephone'] = Language::getVar('SUMO_NOUN_ERROR_TELEPHONE');
        }

        if (!empty($this->request->post['mobile'])) {
            if ((utf8_strlen($this->request->post['mobile']) < 8) || (utf8_strlen($this->request->post['mobile']) > 15)) {
                $this->error['mobile'] = Language::getVar('SUMO_NOUN_ERROR_TELEPHONE');
            }
        }

        // Customer Group
        if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('customer_group_display'))) {
            $customer_group_id = $this->request->post['customer_group_id'];
        }
        else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
        if ($customer_group) {
            // Company ID
            if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($this->request->post['company_id'])) {
                $this->error['company_id'] = Language::getVar('SUMO_NOUN_ERROR_COMPANY');
            }

            // Tax ID
            if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($this->request->post['tax_id'])) {
                $this->error['tax_id'] = Language::getVar('SUMO_NOUN_ERROR_TAX');
            }
        }

        if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
              $this->error['address_1'] = Language::getVar('SUMO_NOUN_ERROR_ADDRESS');
        }

        if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
              $this->error['city'] = Language::getVar('SUMO_NOUN_ERROR_CITY');
        }

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
        if ($country_info) {
            if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 6)) {
                $this->error['postcode'] = Language::getVar('SUMO_NOUN_ERROR_POSTAL_CODE');
            }

            // VAT Validation
            $this->load->helper('vat');

            if ($this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
                $this->error['tax_id'] = Language::getVar('SUMO_NOUN_ERROR_VAT');
            }
        }

        if ($this->request->post['country_id'] == '') {
            $this->error['country'] = Language::getVar('SUMO_NOUN_ERROR_COUNTRY');
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
            $this->error['zone'] = Language::getVar('SUMO_NOUN_ERROR_ZONE');
        }

        $password = $this->request->post['password'];
        if (empty($password) || (utf8_strlen($this->request->post['password']) < 4)) {
            $this->error['password'] = Language::getVar('SUMO_NOUN_ERROR_PASSWORD_UNSAFE');
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = Language::getVar('SUMO_NOUN_ERROR_PASSWORD_CONFIRM');
        }

        if ($this->config->get('customer_policy_id')) {
            $information_info = $this->model_catalog_information->getInformation($this->config->get('customer_policy_id'));
            if ($information_info && !isset($this->request->post['agree'])) {
                $this->error['warning'] = Language::getVar('SUMO_NOUN_ACCOUNT_AGREE_PAGE', array($this->url->link('information/information/info', 'information_id=' . $information_info['information_id']), $information_info['title']));
            }
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function country()
    {
        $json = array();
        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
        if ($country_info) {
            $this->load->model('localisation/zone');
            $json = array(
                'country_id'        => $country_info['country_id'],
                'name'              => $country_info['name'],
                'iso_code_2'        => $country_info['iso_code_2'],
                'iso_code_3'        => $country_info['iso_code_3'],
                'address_format'    => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status'            => $country_info['status']
            );
        }

        $this->response->setOutput(json_encode($json));
    }
}
