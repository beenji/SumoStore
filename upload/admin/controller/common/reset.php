<?php
namespace Sumo;
class ControllerCommonReset extends Controller
{
    private $error = array();

    public function index()
    {
        if ($this->user->isLogged()) {
            $this->redirect($this->url->link('common/home', '', 'SSL'));
        }

        if (!$this->config->get('admin_reset_password')) {
            $this->redirect($this->url->link('common/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_NOUN_RESET_PASSWORD'));

        if (isset($this->request->get['code'])) {
            $code = $this->request->get['code'];
        }
        else {
            $code = '';
        }

        $this->load->model('user/user');

        $user_info = $this->model_user_user->getUserByCode($code);

        if ($user_info) {
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                $this->model_user_user->editPassword($user_info['user_id'], $this->request->post['password']);

                $this->redirect($this->url->link('common/login', '', 'SSL'));
            }

            $this->data = array_merge($this->data, array(
                'error_warning' => $this->error,
                'base'          => $this->url->link('', '', 'SSL'),
                'action'        => $this->url->link('common/reset', 'code=' . $code, 'SSL'),
                'cancel'        => $this->url->link('common/login', '', 'SSL')
            ));

            $this->template = 'common/reset.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());
        }
        else {
            $this->model_setting_setting->editSettingValue('config', 'password', '0');

            return $this->forward('common/login');
        }
    }

    protected function validate()
    {
        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error = Language::getVar('SUMO_ERROR_PASSWORD');
        }
        elseif ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error = Language::getVar('SUMO_ERROR_PASSWORD_CONFIRM');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }
}
