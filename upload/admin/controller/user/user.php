<?php
namespace Sumo;
class ControllerUserUser extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_USERS_OVERVIEW'));

        $this->load->model('user/user');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_USERS_ADD'));

        $this->load->model('user/user');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_user_user->addUser($this->request->post);

            $this->redirect($this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_USERS_UPDATE'));

        $this->load->model('user/user');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_user_user->editUser($this->request->get['user_id'], $this->request->post);

            $this->redirect($this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_USERS_OVERVIEW'));

        $this->load->model('user/user');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $user_id) {
                $this->model_user_user->deleteUser($user_id);
            }

            $this->redirect($this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'),
            'href'      => $this->url->link('settings/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_USERS_OVERVIEW'),
        ));

        // Initiate pagination
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data = array(
            'start' => ($page - 1) * 25,
            'limit' => 25
        );

        $user_total = $this->model_user_user->getTotalUsers();

        foreach ($this->model_user_user->getUsers($data) as $result) {
            $this->data['users'][] = array_merge($result, array(
                'status'         => $result['status'] ? Language::getVar('SUMO_NOUN_ENABLED') : Language::getVar('SUMO_NOUN_DISABLED'),
                'date_added'     => Formatter::date($result['date_added']),
                'date_last_seen' => $result['date_last_seen'] != '0000-00-00 00:00:00' ? Formatter::date($result['date_last_seen']) : '&mdash;',
                'edit'           => $this->url->link('user/user/update', 'token=' . $this->session->data['token'] . '&user_id=' . $result['user_id'], 'SSL')
            ));
        }

        $pagination = new Pagination();
        $pagination->total = $user_total;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('sale/return', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data = array_merge($this->data, array(
            'pagination'    => $pagination->renderAdmin(),
            'insert'        => $this->url->link('user/user/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'delete'        => $this->url->link('user/user/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'u_users'       => $this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL'),
            'u_permissions' => $this->url->link('user/user_permission', 'token=' . $this->session->data['token'], 'SSL')
        ));

        $this->template = 'user/user_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'),
            'href'      => $this->url->link('settings/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_USERS_OVERVIEW'),
            'href'      => $this->url->link('user/user')
        ));

        if (!isset($this->request->get['user_id'])) {
            $userID     = 0;
            $action     = $this->url->link('user/user/insert', 'token=' . $this->session->data['token'], 'SSL');

            $this->document->addBreadcrumbs(array(
                'text'      => Language::getVar('SUMO_ADMIN_USERS_ADD'),
            ));
        } 
        else {
            $userID     = $this->request->get['user_id'];
            $action     = $this->url->link('user/user/update', 'token=' . $this->session->data['token'] . '&user_id=' . $userID, 'SSL');
            $userInfo   = $this->model_user_user->getUser($this->request->get['user_id']);

            $this->document->addBreadcrumbs(array(
                'text'      => Language::getVar('SUMO_ADMIN_USERS_UPDATE'),
            ));
        }

        $fields = array(
            'username'      => '',
            'password'      => '',
            'confirm'       => '',
            'firstname'     => '',
            'lastname'      => '',
            'email'         => '',
            'user_group_id' => '',
            'status'        => 1
        );

        foreach ($fields as $field => $defaultValue) {
            if (isset($this->request->post[$field])) {
                $fields[$field] = $this->request->post[$field];
            } 
            elseif (isset($userInfo[$field])) {
                $fields[$field] = $userInfo[$field];
            }
        }

        $this->data = array_merge($this->data, $fields, array(
            'error'         => implode('<br />', $this->error),
            'action'        => $action,
            'cancel'        => $this->url->link('user/user', 'token=' . $this->session->data['token'], 'SSL'),
        ));

        $this->template = 'user/user_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'user/user')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if ((utf8_strlen($this->request->post['username']) < 3) || (utf8_strlen($this->request->post['username']) > 20)) {
              $this->error['username'] = Language::getVar('SUMO_ERROR_USERNAME');
        }

        $user_info = $this->model_user_user->getUserByUsername($this->request->post['username']);

        if (!isset($this->request->get['user_id'])) {
            if ($user_info) {
                $this->error['warning'] = Language::getVar('SUMO_ERROR_USERNAME_EXISTS');
            }
        } else {
            if ($user_info && ($this->request->get['user_id'] != $user_info['user_id'])) {
                $this->error['warning'] = Language::getVar('SUMO_ERROR_USERNAME_EXISTS');
            }
        }

        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error['firstname'] = Language::getVar('SUMO_ERROR_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error['lastname'] = Language::getVar('SUMO_ERROR_LASTNAME');
        }

        if ($this->request->post['password'] || (!isset($this->request->get['user_id']))) {
            if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
                $this->error['password'] = Language::getVar('SUMO_ERROR_PASSWORD');
            }

            if ($this->request->post['password'] != $this->request->post['confirm']) {
                $this->error['confirm'] = Language::getVar('SUMO_ERROR_PASSWORD_CONFIRM');
            }
        }

        if (!$this->error) {
              return true;
        } else {
              return false;
        }
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'user/user')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        foreach ($this->request->post['selected'] as $user_id) {
            if ($this->user->getId() == $user_id) {
                $this->error['warning'] = Language::getVar('SUMO_ERROR_DELETE_OWN_ACCOUNT');
            }
        }

        if (!$this->error) {
              return true;
        } else {
              return false;
        }
    }
}
