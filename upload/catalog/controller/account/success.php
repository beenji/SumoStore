<?php
namespace Sumo;
class ControllerAccountSuccess extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_NOUN_SUCCESS'));

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home')
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_SUCCESS'),
            'href'      => $this->url->link('account/success'),
        );

        $this->data['heading_title'] = Language::getVar('SUMO_NOUN_SUCCESS');

        $this->load->model('account/customer_group');

        $customer_group = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());

        if ($customer_group && !$customer_group['approval']) {
            $this->data['text_message'] = Language::getVar('SUMO_ACCOUNT_MESSAGE', $this->url->link('information/contact'));
        }
        else {
            $this->data['text_message'] = Language::getVar('SUMO_ACCOUNT_APPROVAL', array($this->config->get('name'), $this->url->link('information/contact')));
        }

        if ($this->cart->hasProducts()) {
            $this->data['continue'] = $this->url->link('checkout/cart', '', 'SSL');
        }
        else {
            $this->data['continue'] = $this->url->link('account/account', '', 'SSL');
        }

        $this->template = 'common/success.tpl';

        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
