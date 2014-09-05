<?php
namespace Sumo;
class ControllerAccountLogout extends Controller
{
    public function index()
    {
        if ($this->customer->isLogged() && $this->request->get['logout'] == $this->session->data['logout']) {
            $this->customer->logout();
            $this->cart->clear();
            unset($this->session->data);
            $this->session->data = array();
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }
        else {
            echo $this->request->get['logout'] . ' vs ' . $this->session->data['logout'] . '<br />';
            exit;
            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }
    }
}
