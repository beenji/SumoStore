<?php
namespace Sumo;
class ControllerAccountAddress extends Controller
{
    private $error = array();

    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE'));
        $this->load->model('account/address');

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home')
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL')
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE'),
            'href'      => $this->url->link('account/address', '', 'SSL')
        );

        $this->data['error_warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        }

        $this->data['success'] = '';
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['addresses'] = array();
        $results = $this->model_account_address->getAddresses();
        foreach ($results as $result) {
            if ($result['address_format']) {
                $format = $result['address_format'];
            }
            else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1} {number} {addon}' . "\n" . '{address_2}' . "\n" . '{postcode} {city}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{number}',
                '{addon}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname'     => $result['firstname'],
                'lastname'      => (!empty($result['middlename']) ? $result['middlename'] . ' ' : '') . $result['lastname'],
                'company'       => $result['company'],
                'address_1'     => $result['address_1'],
                'number'        => $result['number'],
                'addon'         => $result['addon'],
                'address_2'     => $result['address_2'],
                'city'          => $result['city'],
                'postcode'      => $result['postcode'],
                'zone'          => $result['zone'],
                'zone_code'     => isset($result['zone_code']) ? $result['zone_code'] : '',
                'country'       => $result['country']
            );

            $this->data['addresses'][] = array(
                'default'       => $result['address_id'] == $this->customer->getAddressId() ? true : false,
                'address_id'    => $result['address_id'],
                'address'       => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
                'update'        => $this->url->link('account/address/update', 'address_id=' . $result['address_id'], 'SSL'),
                'delete'        => $this->url->link('account/address/delete', 'address_id=' . $result['address_id'], 'SSL')
            );
        }


        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/address/list.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function insert()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/address/insert', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE_ADD'));
        $this->load->model('account/address');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_account_address->addAddress($this->request->post);
            $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_ADDRESS_ADDED');
            $this->redirect($this->url->link('account/address', '', 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE_EDIT'));

        $this->load->model('account/address');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_account_address->editAddress($this->request->get['address_id'], $this->request->post);

            // Default Shipping Address
            if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
                $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
                $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
                $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
            }

            // Default Payment Address
            if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
                $this->session->data['payment_country_id'] = $this->request->post['country_id'];
                $this->session->data['payment_zone_id'] = $this->request->post['zone_id'];

                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
            }

            $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_ADDRESS_UPDATED');

            $this->redirect($this->url->link('account/address', '', 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE'));

        $this->load->model('account/address');

        if (isset($this->request->get['address_id']) && $this->validateDelete()) {
            $this->model_account_address->deleteAddress($this->request->get['address_id']);

            // Default Shipping Address
            if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
                unset($this->session->data['shipping_address_id']);
                unset($this->session->data['shipping_country_id']);
                unset($this->session->data['shipping_zone_id']);
                unset($this->session->data['shipping_postcode']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
            }

            // Default Payment Address
            if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
                unset($this->session->data['payment_address_id']);
                unset($this->session->data['payment_country_id']);
                unset($this->session->data['payment_zone_id']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
            }

            $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_ADDRESS_DELETED');
        }
        $this->redirect($this->url->link('account/address', '', 'SSL'));
    }

    protected function getForm()
    {
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE'),
            'href'      => $this->url->link('account/address', '', 'SSL'),
        );
        if (!isset($this->request->get['address_id'])) {
            $this->data['breadcrumbs'][] = array(
                'text'  => Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE_ADD'),
                'href'  => $this->url->link('account/address/new', '', 'SSL'),
            );
        }
        else {
            $this->data['breadcrumbs'][] = array(
                'text'  => Language::getVar('SUMO_ACCOUNT_ADDRESS_TITLE_EDIT'),
                'href'  => $this->url->link('account/address/update', 'address_id=' . $this->request->get['address_id'], 'SSL'),
            );
        }

        if ($this->config->get('pc_api_key')) {
            $this->session->data['pc'] = md5(md5(microtime(true)) . $_SERVER['REMOTE_ADDR']);
        }

        $this->load->model('account/customer_group');
        $this->load->model('localisation/country');
        $this->data['countries'] = $this->model_localisation_country->getCountries();
        $this->data['customer_groups'] = $this->model_account_customer_group->getCustomerGroups();

        $address = array();
        if (isset($this->request->get['address_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $address = $this->model_account_address->getAddress($this->request->get['address_id']);
        }
        $fields = array('firstname', 'middlename', 'lastname', 'company', 'company_id', 'tax_id', 'address_1', 'number', 'addon', 'address_2', 'postcode', 'city', 'country_id', 'zone_id', 'default');
        foreach ($fields as $field) {
            $this->data['error_' . $field] = '';
            $this->data[$field] = '';
            if (isset($this->error[$field])) {
                $this->data['error_' . $field] = $this->error[$field];
            }
            if (isset($this->request->post[$field])) {
                $this->data[$field] = $this->request->post[$field];
            }
            else if (!empty($address[$field])) {
                $this->data[$field] = $address[$field];
            }
        }


        if (!isset($this->request->get['address_id'])) {
            $this->data['action'] = $this->url->link('account/address/insert', '', 'SSL');
        }
        else {
            $this->data['action'] = $this->url->link('account/address/update', 'address_id=' . $this->request->get['address_id'], 'SSL');
        }


        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/address/form.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error['firstname'] = Language::getVar('SUMO_ERROR_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error['lastname'] = Language::getVar('SUMO_ERROR_LASTNAME');
        }

        if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
            $this->error['address_1'] = Language::getVar('SUMO_NOUN_ERROR_ADDRESS');
        }

        if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
            $this->error['city'] = Language::getVar('SUMO_NOUN_ERROR_CITY');
        }

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if ($country_info) {
            if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
                $this->error['postcode'] = Language::getVar('SUMO_NOUN_ERROR_POSTAL_CODE');
            }
        }

        if ($this->request->post['country_id'] == '') {
            $this->error['country_id'] = Language::getVar('SUMO_NOUN_ERROR_COUNTRY');
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
            $zones = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
            if (count($zones)) {
                $this->error['zone_id'] = Language::getVar('SUMO_NOUN_ERROR_ZONE');
            }
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateDelete()
    {
        if ($this->model_account_address->getTotalAddresses() == 1) {
            $this->error['warning'] = Language::getVar('SUMO_ACCOUNT_ADDRESS_ERROR_DELETE_ONLY_ONE_ADDRESS');
        }

        if ($this->customer->getAddressId() == $this->request->get['address_id']) {
            $this->error['warning'] = Language::getVar('SUMO_ACCOUNT_ADDRESS_ERROR_DELETE_PRIMARY');
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
