<?php
namespace Sumo;
class ControllerAccountPassword extends Controller
{
    private $error = array();

    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/password', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_PASSWORD_TITLE'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('account/customer');
            $this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);
            $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_PASSWORD_UPDATED');
            $this->redirect($this->url->link('account/password', '', 'SSL'));
        }

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
            'text'      => Language::getVar('SUMO_ACCOUNT_PASSWORD_TITLE'),
            'href'      => $this->url->link('account/password', '', 'SSL'),

        );

        $this->data['error_password'] = '';
        if (isset($this->error['password'])) {
            $this->data['error_password'] = $this->error['password'];
        }

        $this->data['error_confirm'] = '';
        if (isset($this->error['confirm'])) {
            $this->data['error_confirm'] = $this->error['confirm'];
        }

        $this->data['error_token'] = '';
        if (isset($this->error['token'])) {
            $this->data['error_token'] = $this->error['token'];
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['action'] = $this->url->link('account/password', '', 'SSL');

        $token = md5(str_shuffle(microtime(true) . $_SERVER['REMOTE_ADDR']));
        $this->data['token'] = $token;
        $this->session->data['token'] = $token;

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/password.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error['password'] = Language::getVar('SUMO_NOUN_ERROR_PASSWORD');
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = Language::getVar('SUMO_NOUN_ERROR_PASSWORD_CONFIRM');
        }

        if ($this->session->data['token'] != $this->request->get['token']) {
            $this->error['token'] = Language::getVar('SUMO_NOUN_ERROR_TOKEN');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }
}
