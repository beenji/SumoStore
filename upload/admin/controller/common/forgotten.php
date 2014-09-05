<?php
namespace Sumo;
class ControllerCommonForgotten extends Controller
{
    private $error = array();

    public function index() {
        if ($this->user->isLogged()) {
            $this->redirect($this->url->link('common/home', '', 'SSL'));
        }

        if (!$this->config->get('admin_reset_password')) {
            $this->redirect($this->url->link('common/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_NOUN_FORGOT_PASSWORD'));

        $this->load->model('user/user');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $code = sha1(uniqid(mt_rand(), true) . $this->request->server['REMOTE_ADDR']);

            $this->model_user_user->editCode($this->request->post['email'], $code);
            $userData = $this->model_user_user->getUserByCode($code);

            // Get template
            Mailer::setCustomer($userData);

            $template = Mailer::getTemplate('forgot_password_admin');

            // Add link to email
            $template['content'] = str_replace('{reset_link}', $this->url->link('common/reset', 'code=' . $code, 'SSL'), $template['content']);
            $template['content'] = str_replace('{remote_addr}', $this->request->server['REMOTE_ADDR'], $template['content']);

            Mail::setTo($userData['email']);
            Mail::setSubject($template['title']);
            Mail::setHtml($template['content']);
            Mail::send();

            $this->redirect($this->url->link('common/login', '', 'SSL'));
        }

        $this->data = array_merge($this->data, array(
            'base'          => $this->url->link('', '', 'SSL'),
            'action'        => $this->url->link('common/forgotten', '', 'SSL'),
            'cancel'        => $this->url->link('common/login', '', 'SSL'),
            'email'         => isset($this->request->post['email']) ? $this->request->post['email'] : '',
            'error_warning' => isset($this->error['warning']) ? $this->error['warning'] : ''
        ));

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }

        $this->template = 'common/forgotten.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!isset($this->request->post['email']) || empty($this->request->post['email']) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_MAIL_NOT_KNOWN');
        }
        elseif (!$this->model_user_user->getTotalUsersByEmail($this->request->post['email'])) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_MAIL_NOT_KNOWN');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }
}
