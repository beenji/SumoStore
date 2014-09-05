<?php
namespace Sumo;
class ControllerLocalisationLanguage extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES'));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'),
            'href'      => $this->url->link('settings/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES'),
        ));
        $this->document->addScript('view/js/pages/languages.js');

        if (!isset($this->request->get['language_id']) || !ctype_digit($this->request->get['language_id'])) {
            $this->redirect($this->url->link('localisation/language', 'language_id=' . $this->config->get('language_id') . '&token=' . $this->session->data['token'], 'SSL'));
        }
        $this->load->model('localisation/language');
        $results = $this->model_localisation_language->getLanguages(array('unused' => 'butnecessary'));

        foreach ($results as $list) {
            if ($list['status']) {
                $list['active'] = true;
                $this->data['language'] = $list['name'];
            }
            else {
                $list['active'] = false;
            }
            $list['name'] = $list['name'] . ' ' . (($list['code'] == $this->config->get('language')) ? Language::getVar('SUMO_NOUN_DEFAULT_LANGUAGE') : null);
            $list['edit'] = $this->url->link('localisation/language/update', 'token=' . $this->session->data['token'] . '&language_id=' . $list['language_id'], 'SSL');
            $this->data['languages'][] = $list;
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $this->data['language_id'] = $this->request->get['language_id'];

        $this->template = 'localisation/language_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function duplicate()
    {
        $language_id = $this->request->get['language_id'];
        if (!empty($language_id)) {
            $this->load->model('localisation/language');
            $this->model_localisation_language->duplicateLanguage($language_id);
            $this->session->data['success'] = Language::getVar('SUMO_LOCALISATION_LANGUAGE_DUPLICATED');
        }
        $this->redirect($this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function delete()
    {
        $language_id = $this->request->get['language_id'];
        if (!empty($language_id)) {
            $this->load->model('localisation/language');
            $this->model_localisation_language->deleteLanguage($language_id);
            $this->session->data['success'] = Language::getVar('SUMO_LOCALISATION_LANGUAGE_DELETED');
        }
        $this->redirect($this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES_ADD'));

        $this->load->model('localisation/language');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_localisation_language->addLanguage($this->request->post);
            $this->redirect($this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES_EDIT'));

        $this->load->model('localisation/language');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_localisation_language->editLanguage($this->request->get['language_id'], $this->request->post);
            $this->redirect($this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    protected function getForm()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'),
            'href'      => $this->url->link('settings/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES'),
            'href'      => $this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL')
        ));

        if (isset($this->request->get['language_id'])) {
            $languageID   = $this->request->get['language_id'];
            $action       = $this->url->link('localisation/language/update', 'token=' . $this->session->data['token'] . '&language_id=' . $languageID, 'SSL');
            $languageInfo = $this->model_localisation_language->getLanguage($languageID);

            $this->document->addBreadcrumbs(array(
                'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES_EDIT'),
            ));
        }
        else {
            $action       = $this->url->link('localisation/language/insert', 'token=' . $this->session->data['token'], 'SSL');
            $languageID   = 0;

            $this->document->addBreadcrumbs(array(
                'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_LANGUAGES_ADD'),
            ));
        }

        $fields = array(
            'fallback'      => '',
            'name'          => '',
            'code'          => '',
            'locale'        => '',
            'image'         => '',
            'sort_order'    => 0,
            'status'        => 1
        );

        foreach ($fields as $field => $defaultValue) {
            if (isset($this->request->post[$field])) {
                $fields[$field] = $this->request->post[$field];
            }
            elseif (isset($languageInfo[$field])) {
                $fields[$field] = $languageInfo[$field];
            }
        }

        $this->data = array_merge($this->data, $fields, array(
            'language_id'   => $languageID,
            'cancel'        => $this->url->link('localisation/language', 'token=' . $this->session->data['token'], 'SSL'),
            'languages'     => $this->model_localisation_language->getLanguages(),
            'action'        => $action,
            'error'         => implode('<br />', $this->error)
        ));

        $this->template = 'localisation/language_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'localisation/language')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
            $this->error['name'] = Language::getVar('SUMO_ERROR_NAME');
        }

        if (utf8_strlen($this->request->post['code']) < 2) {
            $this->error['code'] = Language::getVar('SUMO_ERROR_CODE');
        }

        if (!$this->request->post['locale']) {
            $this->error['locale'] = Language::getVar('SUMO_ERROR_LOCALE');
        }

        if ((utf8_strlen($this->request->post['image']) < 3) || (utf8_strlen($this->request->post['image']) > 32)) {
            $this->error['image'] = Language::getVar('SUMO_ERROR_IMAGE');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'localisation/language')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        $this->load->model('setting/store');
        $this->load->model('sale/order');

        foreach ($this->request->post['selected'] as $language_id) {
            $language_info = $this->model_localisation_language->getLanguage($language_id);

            if ($language_info) {
                if ($this->config->get('language') == $language_info['code']) {
                    $this->error['warning'] = Language::getVar('SUMO_ERROR_DEFAULT_LANGUAGE');
                }

                if ($this->config->get('admin_language') == $language_info['code']) {
                    $this->error['warning'] = Language::getVar('SUMO_ERROR_DEFAULT_ADMIN_LANGUAGE');
                }

                $store_total = $this->model_settings_stores->getTotalStoresByLanguage($language_info['code']);

                if ($store_total) {
                    $this->error['warning'] = sprintf(Language::getVar('SUMO_ERROR_DEFAULT_STORE_LANGUAGE'), $store_total);
                }
            }

            $order_total = $this->model_sale_order->getTotalOrdersByLanguageId($language_id);

            if ($order_total) {
                $this->error['warning'] = sprintf(Language::getVar('SUMO_ERROR_HAS_ORDERS'), $order_total);
            }
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function ajax()
    {
        $return = array();

        $this->load->model('localisation/translation');
        $letter = isset($this->request->get['letter']) ? $this->request->get['letter'] : false;
        $language = isset($this->request->get['language_id']) ? $this->request->get['language_id'] : $this->config->get('language_id');

        if (isset($this->request->get['key_id'])) {
            $return = Language::setVar($this->request->post['key_id'], $language, html_entity_decode($this->request->post['value'], ENT_QUOTES, 'UTF-8'));
        }
        else {
            if ($letter) {
                $return = $this->model_localisation_translation->getTranslationsByTranslation($letter, $language);
            }
            else {
                $return = $this->model_localisation_translation->getTranslationsByTranslation('untranslated', $language);
            }
        }

        $this->response->setOutput(json_encode($return));
    }
}
