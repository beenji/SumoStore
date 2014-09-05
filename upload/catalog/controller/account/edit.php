<?php
namespace Sumo;
class ControllerAccountEdit extends Controller
{
    private $error = array();

    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_EDIT_TITLE'));

        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_account_customer->editNewsletter($this->request->post['newsletter']);
            if (count($this->request->post) >= 2) {
                $this->model_account_customer->editCustomer($this->request->post);
                $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_UPDATED');
            }
            else {
                $this->session->data['success'] = Language::getVar('SUMO_NOUN_NEWSLETTER_CHANGED');
            }
            $this->redirect($this->url->link('account/edit', '', 'SSL'));
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
            'text'      => Language::getVar('SUMO_ACCOUNT_EDIT_TITLE'),
            'href'      => $this->url->link('account/edit', '', 'SSL'),

        );

        $this->data['warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['warning'] = $this->error['warning'];
        }

        $this->data['success'] = '';
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->error['firstname'])) {
            $this->data['error_firstname'] = $this->error['firstname'];
        } else {
            $this->data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $this->data['error_lastname'] = $this->error['lastname'];
        } else {
            $this->data['error_lastname'] = '';
        }

        if (isset($this->error['middlename'])) {
            $this->data['error_middlename'] = $this->error['middlename'];
        } else {
            $this->data['error_middlename'] = '';
        }

        if (isset($this->error['birthdate'])) {
            $this->data['error_birthdate'] = $this->error['birthdate'];
        } else {
            $this->data['error_birthdate'] = '';
        }

        if (isset($this->error['gender'])) {
            $this->data['error_gender'] = $this->error['gender'];
        } else {
            $this->data['error_gender'] = '';
        }

        if (isset($this->error['email'])) {
            $this->data['error_email'] = $this->error['email'];
        } else {
            $this->data['error_email'] = '';
        }

        if (isset($this->error['telephone'])) {
            $this->data['error_telephone'] = $this->error['telephone'];
        } else {
            $this->data['error_telephone'] = '';
        }

        if (isset($this->error['mobile'])) {
            $this->data['error_mobile'] = $this->error['mobile'];
        } else {
            $this->data['error_mobile'] = '';
        }

        $this->data['action'] = $this->url->link('account/edit', '', 'SSL');

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        }

        if (isset($this->request->post['firstname'])) {
            $this->data['firstname'] = $this->request->post['firstname'];
        } elseif (isset($customer_info)) {
            $this->data['firstname'] = $customer_info['firstname'];
        } else {
            $this->data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $this->data['lastname'] = $this->request->post['lastname'];
        } elseif (isset($customer_info)) {
            $this->data['lastname'] = $customer_info['lastname'];
        } else {
            $this->data['lastname'] = '';
        }

        if (isset($this->request->post['middlename'])) {
            $this->data['middlename'] = $this->request->post['middlename'];
        } elseif (isset($customer_info)) {
            $this->data['middlename'] = $customer_info['middlename'];
        } else {
            $this->data['middlename'] = '';
        }

        if (isset($this->request->post['birthdate'])) {
            $this->data['birthdate'] = $this->request->post['birthdate'];
        } elseif (isset($customer_info)) {
            $this->data['birthdate'] = $customer_info['birthdate'];
        } else {
            $this->data['birthdate'] = '';
        }

        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        } elseif (isset($customer_info)) {
            $this->data['email'] = $customer_info['email'];
        } else {
            $this->data['email'] = '';
        }

        if (isset($this->request->post['telephone'])) {
            $this->data['telephone'] = $this->request->post['telephone'];
        } elseif (isset($customer_info)) {
            $this->data['telephone'] = $customer_info['telephone'];
        } else {
            $this->data['telephone'] = '';
        }

        if (isset($this->request->post['mobile'])) {
            $this->data['mobile'] = $this->request->post['mobile'];
        } elseif (isset($customer_info)) {
            $this->data['mobile'] = $customer_info['mobile'];
        } else {
            $this->data['mobile'] = '';
        }

        if (isset($this->request->post['gender'])) {
            $this->data['gender'] = $this->request->post['gender'];
        } elseif (isset($customer_info)) {
            $this->data['gender'] = $customer_info['gender'];
        } else {
            $this->data['gender'] = 'm';
        }

        if (isset($this->request->post['fax'])) {
            $this->data['fax'] = $this->request->post['fax'];
        } elseif (isset($customer_info)) {
            $this->data['fax'] = $customer_info['fax'];
        } else {
            $this->data['fax'] = '';
        }

        $this->data['back'] = $this->url->link('account/account', '', 'SSL');

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }

        $this->template = 'account/edit.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    protected function validate()
    {
        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error['firstname'] = Language::getVar('SUMO_NOUN_ERROR_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error['lastname'] = Language::getVar('SUMO_NOUN_ERROR_LASTNAME');
        }

        if (empty($this->request->post['gender'])) {
            $this->error['gender'] = Language::getVar('SUMO_NOUN_ERROR_GENDER');
        }

        if (empty($this->request->post['birthdate']) || (utf8_strlen($this->request->post['birthdate']) != 10)) {
            $this->error['birthdate'] = Language::getVar('SUMO_NOUN_ERROR_BIRTHDATE');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error['email'] = Language::getVar('SUMO_NOUN_ERROR_EMAIL');
        }

        if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = Language::getVar('SUMO_NOUN_ERROR_EMAIL_IN_USE');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = Language::getVar('SUMO_NOUN_ERROR_TELEPHONE');
        }

        if (!empty($this->request->post['mobile'])) {
            if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
                $this->error['telephone'] = Language::getVar('SUMO_NOUN_ERROR_TELEPHONE');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            exit(print_r($this->error,true));
            return false;
        }
    }
}
