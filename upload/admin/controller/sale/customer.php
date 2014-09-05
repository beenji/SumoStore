<?php
namespace Sumo;
class ControllerSaleCustomer extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CUSTOMER_DASHBOARD'));

        $this->load->model('sale/customer');

        $this->getList();
    }

    public function find_customer()
    {
        $response = array();

        if (!empty($this->request->get['q'])) {
            $this->load->model('sale/customer');
            $customers = $this->model_sale_customer->getCustomers(array('filter_name' => $this->request->get['q']));

            foreach ($customers as $customer)
            {
                if (isset($this->request->get['full'])) {
                    $response[$customer['name']] = $customer;
                }
                else {
                    $response[$customer['name']] = array(
                        'customer_no' => str_pad($customer['customer_id'], 5, 0, STR_PAD_LEFT),
                        'id'          => $customer['customer_id']
                    );
                }
            }
        }

        $this->response->setOutput(json_encode($response));
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_CUSTOMER_INFO'));

        $this->load->model('sale/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_customer->addCustomer($this->request->post);
            $this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_CUSTOMER_INFO'));

        $this->load->model('sale/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_customer->editCustomer($this->request->get['customer_id'], $this->request->post);
            $this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
      }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CUSTOMER_DASHBOARD'));

        $this->load->model('sale/customer');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $customer_id) {
                $this->model_sale_customer->deleteCustomer($customer_id);
            }

            $this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function approve()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CUSTOMER_DASHBOARD'));

        $this->load->model('sale/customer');

        if (!$this->user->hasPermission('modify', 'sale/customer')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        } elseif (isset($this->request->post['selected'])) {
            $approved = 0;

            foreach ($this->request->post['selected'] as $customer_id) {
                $customer_info = $this->model_sale_customer->getCustomer($customer_id);

                if ($customer_info && !$customer_info['approved']) {
                    $this->model_sale_customer->approve($customer_id);

                    $approved++;
                }
            }

            //$this->redirect($this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CUSTOMER_DASHBOARD'),
        ));

        $data = array(
            'filter_name'           => '',
            'filter_email'          => '',
            'filter_ip'             => '',
            'filter_date_added'     => '',
            'filter_newsletter'     => '',
            'filter_customer_group' => '',
            'filter_status'         => null,
            'filter_newsletter'     => null,
            'filter_approved'       => null
        );

        $this->data['advanced_search'] = false;

        foreach ($data as $filter => $default)
        {
            $data[$filter] = isset($this->request->get[$filter]) && $this->request->get[$filter] != '' ? $this->request->get[$filter] : $default;
            $this->data[$filter] = $data[$filter];

            if (!empty($data[$filter]))
            {
                $this->data['advanced_search'] = true;
            }
        }

        if (!$this->data['advanced_search']) {
            $search = !empty($this->request->get['search']) ? $this->request->get['search'] : false;
            if ($search) {
                $this->data['search'] = $search;
                $data['filter_name'] = $search;
            }
        }

        $this->load->model('sale/customer_group');

        $this->data = array_merge($this->data, array(
            'cancel'          => $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'),
            'current_url'     => $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'),
            'customer_groups' => $this->model_sale_customer_group->getCustomerGroups(),
            'approve'         => $this->url->link('sale/customer/approve', 'token=' . $this->session->data['token'], 'SSL'),
            'insert'          => $this->url->link('sale/customer/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'delete'          => $this->url->link('sale/customer/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'token'           => $this->session->data['token'],
            'stores'          => array(),
            'customers'       => array()
        ));

        // Initiate pagination
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data = array_merge($data, array(
            'start' => ($page - 1) * 25,
            'limit' => 25
        ));

        $customer_total = $this->model_sale_customer->getTotalCustomers($data);

        $results = $this->model_sale_customer->getCustomers($data);

        foreach ($results as $result) {
            $this->data['customers'][] = array_merge($result, array(
                'date_added'     => Formatter::date($result['date_added']),
                'edit'           => $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL'),
                'login'          => $this->url->link('sale/customer/login', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . '&store_id=', 'SSL')
            ));
        }

        $pagination = new Pagination();
        $pagination->total = $customer_total;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->load->model('settings/stores');

        foreach($this->model_settings_stores->getStores() as $list) {
            $this->data['stores'][] = $list;
        }

        $this->template = 'sale/customer_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CUSTOMER_DASHBOARD'),
            'href'      => $this->url->link('sale/customer'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_CUSTOMER_INFO'),
        ));

        if (isset($this->request->get['customer_id'])) {
            $customerID   = $this->request->get['customer_id'];
            $action       = $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $customerID, 'SSL');
            $customerInfo = $this->model_sale_customer->getCustomer($customerID);

            // Address info
            $addresses = $this->model_sale_customer->getAddresses($customerID);

            if (!empty($addresses)) {
                $customerInfo['address'] = $addresses;
            }

            // Load some extra views
            $this->history();
            $this->transaction();
            $this->reward();
            $ips = $this->model_sale_customer->getIpsByCustomerId($customerID);
            foreach ($ips as $id => $list) {
                $list['total'] = $this->model_sale_customer->getTotalCustomersByIp($list['ip']);
                $list['filter_ip'] = $this->url->link('sale/customer', 'filter_ip=' . $list['ip'], 'SSL');
                $list['ban_ip'] = $this->model_sale_customer->getTotalBanIpsByIp($list['ip']);
                $ips[$id] = $list;
            }
        }
        else {
            $customerID   = 0;
            $action       = $this->url->link('sale/customer/insert', 'token=' . $this->session->data['token'], 'SSL');
            $customerInfo = array();
            $ips            = array();
        }

        $fields = array(
            'firstname'         => '',
            'middlename'        => '',
            'lastname'          => '',
            'birthdate'         => '',
            'email'             => '',
            'telephone'         => '',
            'fax'               => '',
            'mobile'            => '',
            'gender'            => 'm',
            'newsletter'        => 0,
            'customer_group_id' => 0,
            'status'            => 1,
            'password'          => '',
            'confirm'           => '',
            'address_id'        => 0,
            'address'           => array(
                array(
                    'address_id'    => '',
                    'firstname'     => '',
                    'middlename'    => '',
                    'lastname'      => '',
                    'company'       => '',
                    'company_id'    => '',
                    'tax_id'        => '',
                    'address_1'     => '',
                    'number'        => '',
                    'addon'         => '',
                    'address_2'     => '',
                    'postcode'      => '',
                    'city'          => '',
                    'country_id'    => '',
                    'zone_id'       => ''
                )
            )
        );

        // Fill up form field list
        foreach ($fields as $key => $defaultValue) {
            if (isset($this->request->post[$key])) {
                if ($key == 'address' && !sizeof($this->request->post[$key])) {
                    // Empty address, use default
                    $fields[$key] = $defaultValue;
                }
                else {
                    $fields[$key] = $this->request->post[$key];
                }
            }
            elseif (isset($customerInfo[$key])) {
                if ($key == 'birthdate') {
                    $fields[$key] = Formatter::date($customerInfo[$key]);
                }
                else {
                    $fields[$key] = $customerInfo[$key];
                }
            } else {
                $fields[$key] = $defaultValue;
            }
        }

        // Load some extra required models
        $this->load->model('localisation/country');
        $this->load->model('sale/customer_group');

        $this->data = array_merge($this->data, $fields, array(
            'form_error'      => implode('<br />', $this->error),
            'customer_id'     => $customerID,
            'action'          => $action,
            'address'         => array_values($fields['address']), // Reset array keys
            'birthdate'       => $fields['birthdate'],
            'token'           => $this->session->data['token'],
            'countries'       => $this->model_localisation_country->getCountries(),
            'customer_groups' => $this->model_sale_customer_group->getCustomerGroups(),
            'cancel'          => $this->url->link('sale/customer', 'token=' . $this->session->data['token'], 'SSL'),
            'ips'             => $ips
        ));

        // Only upon user-edit
        if (!empty($customer_info)) {
            $results = $this->model_sale_customer->getIpsByCustomerId($customerID);

            foreach ($results as $result) {
                $banIPTotal = $this->model_sale_customer->getTotalBanIpsByIp($result['ip']);

                $this->data['ips'][] = array(
                    'ip'         => $result['ip'],
                    'total'      => $this->model_sale_customer->getTotalCustomersByIp($result['ip']),
                    'date_added' => Formatter::date($result['date_added']),
                    'filter_ip'  => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&filter_ip=' . $result['ip'], 'SSL'),
                    'ban_ip'     => $banIPTotal
                );
            }
        }

        // Set letters for tabs
        $letters = 'ABCDEFGIJKLMNOPQRSTUVWXYZ';

        foreach ($this->data['address'] as $k => $address) {
            $this->data['address'][$k]['letter'] = substr($letters, $k, 1);
        }

        $this->document->addScript('view/js/pages/customer_form.js');

        $this->template = 'sale/customer_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'sale/customer')) {
            $this->error[] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
            return;
        }

        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error[] = Language::getVar('SUMO_ERROR_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error[] = Language::getVar('SUMO_ERROR_LASTNAME');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error[] = Language::getVar('SUMO_ERROR_EMAIL');
        }

        $customer_info = $this->model_sale_customer->getCustomerByEmail($this->request->post['email']);

        if (!isset($this->request->get['customer_id'])) {
            if ($customer_info) {
                $this->error[] = Language::getVar('SUMO_ERROR_EMAIL_EXISTS');
            }
        }
        else {
            if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
                $this->error[] = Language::getVar('SUMO_ERROR_EMAIL_EXISTS');
            }
        }

        if (empty($this->request->post['gender'])) {
            $this->error[] = Language::getVar('SUMO_ERROR_GENDER');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error[] = Language::getVar('SUMO_ERROR_PHONE');
        }

        if (!empty($this->request->post['mobile'])) {
            if ((utf8_strlen($this->request->post['mobile']) < 3) || (utf8_strlen($this->request->post['mobile']) > 32)) {
                $this->error[] = Language::getVar('SUMO_ERROR_MOBILE');
            }
        }

        if ($this->request->post['password'] || (!isset($this->request->get['customer_id']))) {
            if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
                $this->error[] = Language::getVar('SUMO_ERROR_PASSWORD');
            }

            if ($this->request->post['password'] != $this->request->post['confirm']) {
                $this->error[] = Language::getVar('SUMO_ERROR_PASSWORD_CONFIRM');
            }
        }

        if (isset($this->request->post['address'])) {
            foreach ($this->request->post['address'] as $key => $value) {
                // All fields empty? Continue with next item, we're not using this address
                if (implode('', $value) == '') {
                    unset($this->request->post['address'][$key]);
                    continue;
                }

                if ((utf8_strlen($value['firstname']) < 1) || (utf8_strlen($value['firstname']) > 32)) {
                    $this->error['address_firstname'] = Language::getVar('SUMO_ERROR_FIRSTNAME');
                }

                if ((utf8_strlen($value['lastname']) < 1) || (utf8_strlen($value['lastname']) > 32)) {
                    $this->error['address_lastname'] = Language::getVar('SUMO_ERROR_LASTNAME');
                }

                if ((utf8_strlen($value['address_1']) < 3) || (utf8_strlen($value['address_1']) > 128)) {
                    $this->error['address_address_1'] = Language::getVar('SUMO_ERROR_ADDRESS_1');
                }

                if (strlen($value['number']) < 1 || strlen($value['number']) > 9) {
                    $this->error['address_number'] = Language::getVar('SUMO_ERROR_ADDRESS_NUMBER');
                }

                if ((utf8_strlen($value['city']) < 2) || (utf8_strlen($value['city']) > 128)) {
                    $this->error['address_city'] = Language::getVar('SUMO_ERROR_CITY');
                }

                $this->load->model('localisation/country');

                $country_info = $this->model_localisation_country->getCountry($value['country_id']);

                if ($country_info) {
                    if ($country_info['postcode_required'] && (utf8_strlen($value['postcode']) < 2) || (utf8_strlen($value['postcode']) > 10)) {
                        $this->error['address_postcode'] = Language::getVar('SUMO_ERROR_POSTCODE');
                    }

                    // VAT Validation
                    $this->load->helper('vat');

                    if ($this->config->get('vat') && $value['tax_id'] && (vat_validation($country_info['iso_code_2'], $value['tax_id']) == 'invalid')) {
                        $this->error['address_tax_id'] = Language::getVar('SUMO_ERROR_TAX');
                    }
                }

                if ($value['country_id'] < 1) {
                    $this->error['address_country'] = Language::getVar('SUMO_ERROR_COUNTRY');
                }

                if (!isset($value['zone_id']) || $value['zone_id'] == '') {
                    $this->error['address_zone'] = Language::getVar('SUMO_ERROR_ZONE');
                }
            }
        }

        // Do we at least have on address?
        if (!sizeof($this->request->post['address'])) {
            $this->error['address'] = Language::getVar('SUMO_ERROR_NO_ADDRESS');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'sale/customer')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function login()
    {
        $json = array();

        if (isset($this->request->get['customer_id'])) {
            $customer_id = $this->request->get['customer_id'];
        }
        else {
            $customer_id = 0;
        }

        $this->load->model('sale/customer');
        $customer_info = $this->model_sale_customer->getCustomer($customer_id);

        if ($customer_info) {
            $token = md5(mt_rand());

            $this->model_sale_customer->editToken($customer_id, $token);

            if (isset($this->request->get['store_id'])) {
                $store_id = $this->request->get['store_id'];
            }
            else {
                $store_id = 0;
            }

            $this->load->model('setting/store');

            $store_info = $this->model_settings_stores->getStore($store_id);

            if ($store_info) {
                $this->redirect($store_info['base_default'] . '://' . $store_info['base_' . $store_info['base_default']] . 'account/login?&token=' . $token);
            }
        }
        //else {
            $this->document->setTitle(Language::getVar('SUMO_ADMIN_NOT_FOUND'));

            $this->template = 'error/not_found.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());
        //}
    }

    public function history()
    {
        $this->load->model('sale/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) {
            $this->model_sale_customer->addHistory($this->request->get['customer_id'], $this->request->post['comment']);

            $return = array('success' => Language::getVar('SUMO_SUCCESS_HISTORY_ADDED'));
            $this->response->setOutput(json_encode($return));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
            $return = array('error' => Language::getVar('SUMO_ERROR_NO_PERMISSION'));
            $this->response->setOutput(json_encode($return));
        }

        $this->data['histories'] = array();

        $results = $this->model_sale_customer->getHistories($this->request->get['customer_id']);

        foreach ($results as $result) {
            $this->data['histories'][] = array_merge($result, array(
                'comment'     => $result['comment'],
                'date_added'  => Formatter::date($result['date_added'])
            ));
        }
    }

    public function transaction()
    {
        $this->load->model('sale/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) {
            $this->model_sale_customer->addTransaction($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['amount']);

            $return = array('success' => Language::getVar('SUMO_SUCCESS_TRANSACTION_ADDED'));
            $this->response->setOutput(json_encode($return));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
            $return = array('error' => Language::getVar('SUMO_ERROR_NO_PERMISSION'));
            $this->response->setOutput(json_encode($return));
        }

        $this->data['transactions'] = array();

        $results = $this->model_sale_customer->getTransactions($this->request->get['customer_id']);

        foreach ($results as $result) {
            $this->data['transactions'][] = array_merge($result, array(
                'amount'      => Formatter::currency($result['amount']),
                'date_added'  => Formatter::date($result['date_added'])
            ));
          }

        $this->data['transaction_balance'] = $this->currency->format($this->model_sale_customer->getTransactionTotal($this->request->get['customer_id']), $this->config->get('currency'));

    }

    public function reward()
    {
        $this->load->model('sale/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/customer')) {
            $this->model_sale_customer->addReward($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['points']);

            $return = array('success' => Language::getVar('SUMO_SUCCESS_REWARD_ADDED'));
            $this->response->setOutput(json_encode($return));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$this->user->hasPermission('modify', 'sale/customer')) {
            $return = array('error' => Language::getVar('SUMO_ERROR_NO_PERMISSION'));
            $this->response->setOutput(json_encode($return));
        }

        $this->data['rewards'] = array();

        $results = $this->model_sale_customer->getRewards($this->request->get['customer_id']);

        foreach ($results as $result) {
            $this->data['rewards'][] = array_merge($result, array(
                'date_added'  => Formatter::date($result['date_added'])
            ));
          }

        $this->data['points_balance'] = $this->model_sale_customer->getRewardTotal($this->request->get['customer_id']);
    }

    public function addBanIP()
    {
        $json = array();

        if (isset($this->request->post['ip'])) {
            if (!$this->user->hasPermission('modify', 'sale/customer')) {
                $json['error'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
            } else {
                $this->load->model('sale/customer');
                $this->model_sale_customer->addBanIP($this->request->post['ip']);

                $json['success'] = Language::getVar('SUMO_SUCCESS_IPBAN_ADDED');
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function removeBanIP()
    {
        $json = array();

        if (isset($this->request->post['ip'])) {
            if (!$this->user->hasPermission('modify', 'sale/customer')) {
                $json['error'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
            } else {
                $this->load->model('sale/customer');
                $this->model_sale_customer->removeBanIP($this->request->post['ip']);

                $json['success'] = Language::getVar('SUMO_SUCCESS_IPBAN_REMOVED');
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('sale/customer');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 20
            );

            $results = $this->model_sale_customer->getCustomers($data);

            foreach ($results as $result) {
                $result['address']  = $this->model_sale_customer->getAddresses($result['customer_id']);
                $result['name']     = strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
                $json[]             = $result;
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
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

    public function address()
    {
        $json = array();

        if (!empty($this->request->get['address_id'])) {
            $this->load->model('sale/customer');

            $json = $this->model_sale_customer->getAddress($this->request->get['address_id']);
        }

        $this->response->setOutput(json_encode($json));
    }
}
