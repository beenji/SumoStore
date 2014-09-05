<?php
namespace Sumo;
class ControllerCommonLogin extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_LOGIN_TITLE'));

        if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->data['token'])) {
            $this->redirect($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['base'] = $this->url->link('', '', 'SSL');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->session->data['token'] = md5(mt_rand() . $_SERVER['REMOTE_ADDR'] . $_SERVER['SERVER_ADDR'] . microtime(true));

            if (isset($this->request->post['redirect'])) {
                if (!stristr($this->request->post['redirect'], '?')) {
                    $this->request->post['redirect'] .= '?';
                }
                $this->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['token']);
            }
            else {
                $this->redirect($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'));
            }
        }

        if ((isset($this->session->data['token']) && !isset($this->request->get['token'])) || ((isset($this->request->get['token']) && (isset($this->session->data['token']) && ($this->request->get['token'] != $this->session->data['token']))))) {
            $this->session->data['token'] = '';
            $this->error['warning'] = Language::getVar('SUMO_ADMIN_TOKEN_ERROR');
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        }
        else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        else {
            $this->data['success'] = '';
        }

        $this->data['action'] = $this->url->link('common/login', '', 'SSL');

        if (isset($this->request->post['username'])) {
            $this->data['username'] = $this->request->post['username'];
        }
        else {
            $this->data['username'] = '';
        }

        if (isset($this->request->post['password'])) {
            $this->data['password'] = $this->request->post['password'];
        }
        else {
            $this->data['password'] = '';
        }

        if (!empty($this->request->get['route'])) {
            $route = $this->request->get['route'];

            unset($this->request->get['route']);

            if (isset($this->request->get['token'])) {
                unset($this->request->get['token']);
            }

            $url = '';

            if ($this->request->get) {
                $url .= http_build_query($this->request->get);
            }

            $this->data['redirect'] = $this->url->link($route, $url, 'SSL');
        }
        else {
            $this->data['redirect'] = '';
        }

        if ($this->config->get('admin_reset_password')) {
            $this->data['forgotten'] = $this->url->link('common/forgotten', '', 'SSL');
        }
        else {
            $this->data['forgotten'] = '';
        }

        $this->template = 'common/login.tpl';
        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        $cookie = isset($_POST['remember']) && $_POST['remember'] == 'on' ? 1 : 0;
        if (isset($this->request->post['username']) && isset($this->request->post['password']) && !$this->user->login($this->request->post['username'], $this->request->post['password'], $cookie)) {
            $this->error['warning'] = Language::getVar('SUMO_ADMIN_LOGIN_ERROR');
        }

        if (!$this->error) {
            return true;
        }
        return false;

    }
}
