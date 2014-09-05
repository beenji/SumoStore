<?php
namespace Sumo;
class ControllerAccountReturn extends Controller
{
    private $error = array();

    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/return', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_RETURN_TITLE'));
        $this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');

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

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_TITLE'),
            'href'      => $this->url->link('account/return', $url, 'SSL'),

        );

        $this->load->model('account/return');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['returns'] = array();

        $totalReturns = $this->model_account_return->getTotalReturns();
        $results = $this->model_account_return->getReturns(($page - 1) * 10, 10);

        foreach ($results as $result) {
            $this->data['returns'][] = array(
                'return_id'  => str_pad($result['return_id'], 6, 0, STR_PAD_LEFT),
                'order_id'   => str_pad($result['order_id'], 6, 0, STR_PAD_LEFT),
                'name'       => $result['firstname'] . ' ' . $result['lastname'],
                'status'     => $result['status'],
                'date'       => Formatter::date($result['date_added']),
                'order'      => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL'),
                'view'       => $this->url->link('account/return/info', 'return_id=' . $result['return_id'] . $url, 'SSL')
            );
        }

        $pagination = new Pagination();
        $pagination->total = $totalReturns;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('config_catalog_limit');
        $pagination->url   = $this->url->link('account/history', 'page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();
        $this->data['insert']     = $this->url->link('account/return/insert', '', 'SSL');

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/return/list.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function info()
    {
        if (isset($this->request->get['return_id'])) {
            $returnID = $this->request->get['return_id'];
        } else {
            $returnID = 0;
        }

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $returnID, 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->load->model('account/return');
        
        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_RETURN_DETAILS'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home', '', 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),

        );

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_TITLE'),
            'href'      => $this->url->link('account/return', $url, 'SSL'),

        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_DETAILS'),
            'href'      => $this->url->link('account/return/info', 'return_id=' . $this->request->get['return_id'] . $url, 'SSL'),

        );

        $returnInfo = $this->model_account_return->getReturn($returnID);

        if ($returnInfo) {
            $this->data = array_merge($this->data, $returnInfo, array(
                'return_id'         => str_pad($returnInfo['return_id'], 6, 0, STR_PAD_LEFT),
                'order_id'          => str_pad($returnInfo['order_id'], 6, 0, STR_PAD_LEFT),
                'date_ordered'      => Formatter::date($returnInfo['date_ordered']),
                'date_added'        => Formatter::date($returnInfo['date_added']),
                'comment'           => nl2br($returnInfo['comment']),
                'order'             => $this->url->link('account/order/info', 'order_id=' . $returnInfo['order_id'], 'SSL'),
                'opened'            => $returnInfo['opened'] ? Language::getVar('SUMO_NOUN_YES') : Language::getVar('SUMO_NOUN_NO')
            ));

            $this->data['histories'] = array();

            $results = $this->model_account_return->getReturnHistories($returnID);

            foreach ($results as $result) {
                $this->data['histories'][] = array(
                      'date_added' => Formatter::date($result['date_added']),
                      'status'     => $result['status'],
                      'comment'    => nl2br($result['comment'])
                );
            }

            $this->data['continue'] = $this->url->link('account/return', $url, 'SSL');
        }
        else {
            $this->data['return_id'] = false;       
        }

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/return/view.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function insert()
    {
        $this->load->model('account/return');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_account_return->addReturn($this->request->post);

            $this->redirect($this->url->link('account/return/success', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_RETURN_NEW'));

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
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_TITLE'),
            'href'      => $this->url->link('account/return/', '', 'SSL'),

        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_NEW'),
            'href'      => $this->url->link('account/return/insert', '', 'SSL'),

        );

        // Load required models
        $this->load->model('catalog/product');
        $this->load->model('localisation/return_reason');
        $this->load->model('account/order');
        $this->load->model('localisation/return_reason');

        if (isset($this->request->get['order_id'])) {
            $orderInfo = $this->model_account_order->getOrder($this->request->get['order_id']);
            $orderID   = $this->request->get['order_id'];
            $productID = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        } 
        else {
            $orderID = $productID = 0;
        }

        $fields = array(
            'order_id'          => $orderID,
            'date_ordered'      => isset($orderInfo['order_date']) ? Formatter::date($orderInfo['order_date']) : '',
            'firstname'         => $this->customer->getFirstName(),
            'lastname'          => $this->customer->getLastName(),
            'email'             => $this->customer->getEmail(),
            'telephone'         => $this->customer->getTelephone(),
            'product'           => '',
            'product_id'        => $productID,
            'model'             => '',
            'quantity'          => 1,
            'opened'            => 0,
            'return_reason_id'  => '',
            'comment'           => '',
            'agree'             => 0
        );

        foreach ($fields as $field => $defaultVal) {
            if (isset($this->request->post[$field])) {
                $fields[$field] = $this->request->post[$field];
            }
            elseif (isset($orderInfo[$field])) {
                $fields[$field] = $orderInfo[$field];
            }
            elseif (isset($orderInfo['customer'][$field])) {
                $fields[$field] = $orderInfo['customer'][$field];
            }
        }

        // Get orders for customer
        $orders = $this->model_account_order->getOrders();
        $rawOrders = array();

        foreach ($orders as $order) {
            // Get products for order
            $products = array();

            foreach ($this->model_account_order->getOrderProducts($order['order_id']) as $product) {
                $products[$product['product_id']] = array(
                    'product'       => $product['name'],
                    'model'         => $product['model'],
                    'quantity'      => $product['quantity']
                );

                if ($order['order_id'] == $fields['order_id']) { 
                    // List products
                    $this->data['products'][] = array(
                        'product_id'    => $product['product_id'],
                        'product'       => $product['name']
                    );
                }
            }

            $this->data['orders'][]['order_id'] = $order['order_id'];

            $rawOrders[$order['order_id']] = array(
                'products'      => $products,
                'order_date'    => Formatter::date($order['order_date'])
            );
        }

        $this->data = array_merge($this->data, $fields, array(
            'error'          => !empty($this->error) ? implode('<br />', $this->error) : '',
            'action'         => $this->url->link('account/return/insert', '', 'SSL'),
            'return_reasons' => $this->model_localisation_return_reason->getReturnReasons(),
            'raw_orders'     => json_encode($rawOrders),
            'text_agree'     => '',
            'cancel'         => $this->url->link('account/return', '', 'SSL'),
            'products'       => isset($this->data['products']) ? $this->data['products'] : array()
        ));

        if ($this->config->get('config_return_id')) {
            $this->load->model('catalog/information');

            $informationInfo = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

            if ($informationInfo) {
                $this->data['text_agree'] = sprintf(Language::getVar('SUMO_ACCOUNT_RETURN_AGREE'), 
                    $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_return_id'), 'SSL'), 
                    $informationInfo['title'], 
                    $informationInfo['title']);
            } 
            else {
                $this->data['text_agree'] = Language::getVar('SUMO_ACCOUNT_RETURN_AGREE');
            }
        }

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/return/form.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function success()
    {
        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_RETURN_TITLE'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_TITLE'),
            'href'      => $this->url->link('account/return', '', 'SSL'),
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_RETURN_DETAILS'),
            'href'      => $this->url->link('account/return/insert', '', 'SSL'),
        );


        $this->data['done'] = true;
        $this->data['continue'] = $this->url->link('account/return', '', 'SSL');

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/return/form.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        if (!$this->request->post['order_id']) {
            $this->error['order_id'] = Language::getVar('SUMO_NOUN_ERROR_RETURN_ORDER_ID');
        }

        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error['firstname'] = Language::getVar('SUMO_NOUN_ERROR_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error['lastname'] = Language::getVar('SUMO_NOUN_ERROR_LASTNAME');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error['email'] = Language::getVar('SUMO_NOUN_ERROR_EMAIL');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = Language::getVar('SUMO_NOUN_ERROR_TELEPHONE');
        }

        if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
            $this->error['product'] = Language::getVar('SUMO_NOUN_ERROR_RETURN_PRODUCT_ID');
        }

        if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
            $this->error['model'] = Language::getVar('SUMO_NOUN_ERROR_RETURN_MODEL');
        }

        if (empty($this->request->post['return_reason_id'])) {
            $this->error['reason'] = Language::getVar('SUMO_NOUN_ERROR_RETURN_REASON');
        }

        /*if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
              $this->error['captcha'] = Language::getVar('SUMO_NOUN_ERROR_CAPTCHA');
        }*/

        if ($this->config->get('config_return_id')) {
            $this->load->model('catalog/information');

            $informationInfo = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

            if ($informationInfo && !isset($this->request->post['agree'])) {
                  $this->error['warning'] = sprintf(Language::getVar('SUMO_ACCOUNT_RETURN_AGREE'), $informationInfo['title']);
            }
        }

        if (!$this->error) {
              return true;
        } else {
              return false;
        }
    }

    /*public function captcha()
    {
        $this->load->library('captcha');

        $captcha = new Captcha();

        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }*/
}
