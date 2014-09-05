<?php
namespace Sumo;
class ControllerAccountLogin extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->model('account/customer');

        // Login override for admin users
        if (!empty($this->request->get['token'])) {
            //$this->customer->logout();
            $this->cart->clear();

            unset($this->session->data['wishlist']);
            unset($this->session->data['shipping_address_id']);
            unset($this->session->data['shipping_country_id']);
            unset($this->session->data['shipping_zone_id']);
            unset($this->session->data['shipping_postcode']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_address_id']);
            unset($this->session->data['payment_country_id']);
            unset($this->session->data['payment_zone_id']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);

            $customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);

            if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
                // Default Addresses
                $this->load->model('account/address');

                $address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

                if ($address_info) {
                    if ($this->config->get('config_tax_customer') == 'shipping') {
                        $this->session->data['shipping_country_id'] = $address_info['country_id'];
                        $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
                        $this->session->data['shipping_postcode'] = $address_info['postcode'];
                    }

                    if ($this->config->get('config_tax_customer') == 'payment') {
                        $this->session->data['payment_country_id'] = $address_info['country_id'];
                        $this->session->data['payment_zone_id'] = $address_info['zone_id'];
                    }
                }
                else {
                    unset($this->session->data['shipping_country_id']);
                    unset($this->session->data['shipping_zone_id']);
                    unset($this->session->data['shipping_postcode']);
                    unset($this->session->data['payment_country_id']);
                    unset($this->session->data['payment_zone_id']);
                }

                //$this->redirect($this->url->link('account/account', '', 'SSL'));
                logger::warning('customer validated, should be logged in now');
            }
            else {
                logger::warning('customer_info empty or not validated by this-customer-login');
            }
        }

        if ($this->customer->isLogged()) {
            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_LOGIN_TITLE'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            unset($this->session->data['guest']);

            // Default Shipping Address
            $this->load->model('account/address');

            $address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

            if ($address_info) {
                if ($this->config->get('config_tax_customer') == 'shipping') {
                    $this->session->data['shipping_country_id'] = $address_info['country_id'];
                    $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
                    $this->session->data['shipping_postcode'] = $address_info['postcode'];
                }

                if ($this->config->get('config_tax_customer') == 'payment') {
                    $this->session->data['payment_country_id'] = $address_info['country_id'];
                    $this->session->data['payment_zone_id'] = $address_info['zone_id'];
                }
            }
            else {
                unset($this->session->data['shipping_country_id']);
                unset($this->session->data['shipping_zone_id']);
                unset($this->session->data['shipping_postcode']);
                unset($this->session->data['payment_country_id']);
                unset($this->session->data['payment_zone_id']);
            }
            if (isset($this->request->post['ajax'])) {
                exit('OK');
            }
            else
            if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('base_http')) !== false || strpos($this->request->post['redirect'], $this->config->get('base_https')) !== false)) {
                $this->redirect(str_replace('&amp;', '&', $this->request->post['redirect']));
            }
            else {
                $this->redirect($this->url->link('account/account', '', 'SSL'));
            }
        }

        if (isset($this->request->post['ajax'])) {
            exit(Language::getVar('SUMO_ACCOUNT_ERROR_LOGIN_INVALID'));
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
            'text'      => Language::getVar('SUMO_ACCOUNT_LOGIN_TITLE'),
            'href'      => $this->url->link('account/login', '', 'SSL'),

        );

        $this->data['warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['warning'] = $this->error['warning'];
        }

        $this->data['action'] = $this->url->link('account/login', '', 'SSL');
        $this->data['register'] = $this->url->link('account/register', '', 'SSL');
        $this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

        $this->data['redirect'] = '';
        if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], $this->config->get('base_http')) !== false || strpos($this->request->post['redirect'], $this->config->get('base_https')) !== false)) {
            $this->data['redirect'] = $this->request->post['redirect'];
        }
        elseif (isset($this->session->data['redirect'])) {
            $this->data['redirect'] = $this->session->data['redirect'];
            unset($this->session->data['redirect']);
        }

        $this->data['success'] = '';
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['email'] = '';
        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        }

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }

        $this->template = 'account/login.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
            $this->error['warning'] = Language::getVar('SUMO_ACCOUNT_ERROR_LOGIN_INVALID');
        }

        $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

        if ($customer_info && !$customer_info['approved']) {
            $this->error['warning'] = Language::getVar('SUMO_ACCOUNT_ERROR_LOGIN_INVALID');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }
}
