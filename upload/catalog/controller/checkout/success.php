<?php
namespace Sumo;
class ControllerCheckoutSuccess extends Controller
{
    public function index()
    {
        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();
        }
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['payment']);
        unset($this->session->data['shipping']);
        unset($this->session->data['guest']);
        unset($this->session->data['comment']);
        unset($this->session->data['order_id']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        unset($this->session->data['voucher']);
        unset($this->session->data['vouchers']);
        unset($this->session->data['discount']);
        unset($this->session->data['customer']);
        unset($this->session->data['force_step']);
        unset($this->session->data['error']);
        unset($this->session->data['order_id']);

        $this->document->setTitle(Language::getVar('SUMO_NOUN_SUCCESS'));

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_CART_TITLE'),
            'href'      => $this->url->link('checkout/cart'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_CHECKOUT_TITLE'),
            'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'href'      => $this->url->link('checkout/success'),
            'text'      => Language::getVar('SUMO_NOUN_SUCCESS'),

        );

        $this->data['heading_title'] = Language::getVar('SUMO_NOUN_SUCCESS');

        $this->data['text_message'] = $this->session->data['success'];
        unset($this->session->data['success']);

        $this->template = 'common/success.tpl';

        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
