<?php
namespace Sumo;
class ControllerAccountAccount extends Controller
{
    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        return $this->redirect($this->url->link('account/edit', '', 'SSL'));
    }
}
