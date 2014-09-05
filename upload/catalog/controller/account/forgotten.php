<?php
namespace Sumo;
class ControllerAccountForgotten extends Controller
{
    private $error = array();

    public function index()
    {
        if ($this->customer->isLogged()) {
            $this->redirect($this->url->link('account/account', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_FORGOTTEN_TITLE'));

        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $start = rand(0, 10);
            $end = rand(10, 14);
            $password = str_shuffle(substr(sha1(uniqid(mt_rand(), true) . $this->request->server['REMOTE_ADDR']), $start, $end));

            $this->model_account_customer->editPassword($this->request->post['email'], $password);

            $customerData = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

            // Get template
            Mailer::setCustomer($customerData);

            $template = Mailer::getTemplate('forgot_password_customer');

            // Add link to email
            $template['content'] = str_replace('{password}', $password, $template['content']);
            $template['content'] = str_replace('{remote_addr}', $this->request->server['REMOTE_ADDR'], $template['content']);

            Mail::setTo($customerData['email']);
            Mail::setSubject($template['title']);
            Mail::setHtml($template['content']);
            Mail::send();

            $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_PASSWORD_SENT');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
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
            'text'      => Language::getVar('SUMO_ACCOUNT_FORGOTTEN_TITLE'),
            'href'      => $this->url->link('account/forgotten', '', 'SSL'),
        );

        $this->data['warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['warning'] = $this->error['warning'];
        }

        $this->data['action'] = $this->url->link('account/forgotten', '', 'SSL');

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }

        $this->template = 'account/forgotten.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        if (!isset($this->request->post['email']) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['warning'] = Language::getVar('SUMO_NOUN_ERROR_FORGOTTEN');
        }
        elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = Language::getVar('SUMO_NOUN_ERROR_FORGOTTEN');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }
}
