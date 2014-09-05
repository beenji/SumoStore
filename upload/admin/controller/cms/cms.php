<?php
namespace Sumo;
Class ControllerCMSCMS extends Controller
{
    public function index()
    {
        $title = Language::getVar('SUMO_ADMIN_CMS_LIST_INFORMATION');
        $this->document->setTitle($title);
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_CMS_DASHBOARD'), 'href' => $this->url->link('cms/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => $title));

        $this->data['stores']           = $this->model_settings_stores->getStores();
        $this->data['current_store']    = isset($this->request->get['store_id']) && is_numeric($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        $this->data['new']              = $this->url->link('cms/cms/editor', 'type=information&store_id=' . $this->data['current_store'] . '&token=' . $this->session->data['token'], 'SSL');
        $this->data['type']             = 'information';
        $this->getList('information');

        $this->template = 'cms/list.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function blog()
    {
        $title = Language::getVar('SUMO_ADMIN_CMS_LIST_BLOG');
        $this->document->setTitle($title);
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_CMS_DASHBOARD'), 'href' => $this->url->link('cms/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => $title));

        $this->data['stores']           = $this->model_settings_stores->getStores();
        $this->data['current_store']    = isset($this->request->get['store_id']) && is_numeric($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        $this->data['new']              = $this->url->link('cms/cms/editor', 'type=blog&store_id=' . $this->data['current_store'] . '&token=' . $this->session->data['token'], 'SSL');
        $this->data['type']             = 'blog';
        $this->getList('blog');

        $this->template = 'cms/list.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function editor()
    {
        $this->load->model('cms/cms');
        $this->load->model('localisation/language');

        if (empty($this->request->get['type']) || !in_array($this->request->get['type'], array('blog', 'information')) || !isset($this->request->get['store_id']) || !is_numeric($this->request->get['store_id'])) {
            $this->redirect($this->url->link('cms/cms', '', 'SSL'));
        }

        $data = array();
        if (!empty($this->request->get['id'])) {
            $data = $this->model_cms_cms->getEditorItem($this->request->get['type'], $this->request->get['id']);
            if (!is_array($data) || !count($data)) {
                $this->redirect($this->url->link('cms/cms' . ($this->request->get['type'] == 'blog' ? '/blog' : ''), 'store_id=' . $this->request->get['store_id'], 'SSL'));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ($this->request->get['type'] == 'information') {
                $id = $this->model_cms_cms->saveInformation($this->request->get['store_id'], $this->request->post, !empty($this->request->get['id']) ? $this->request->get['id'] : 0);
                if (empty($this->request->get['id'])) {
                    $this->request->get['id'] = $id;
                }
            }
            else {
                $id = $this->model_cms_cms->saveBlog($this->request->get['store_id'], $this->request->post, !empty($this->request->get['id']) ? $this->request->get['id'] : 0);
                if (empty($this->request->get['id'])) {
                    $this->request->get['id'] = $id;
                }
            }
            if ($this->request->post['save_and_quit']) {
                $this->redirect($this->url->link('cms/cms' . ($this->request->get['type'] == 'blog' ? '/blog' : ''), 'store_id=' . $this->request->get['store_id'], 'SSL'));
            }
            else {
                $this->redirect($this->url->link('cms/cms/editor', 'type=' . $this->request->get['type'] . '&store_id=' . $this->request->get['store_id'] . '&id=' . $this->request->get['id'], 'SSL'));
            }
        }

        $title = Language::getVar('SUMO_ADMIN_CMS_EDITOR_' . strtoupper($this->request->get['type']) . '_' . (count($data) ? 'EDIT' : 'ADD'));
        $this->document->setTitle($title);
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_CMS_DASHBOARD'), 'href' => $this->url->link('cms/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_CMS_LIST_' . strtoupper($this->request->get['type'])), 'href' => $this->url->link('cms/cms' . ($this->request->get['type'] == 'blog' ? '/blog' : ''), 'store_id=' . $this->request->get['store_id'], 'SSL')));
        $this->document->addBreadcrumbs(array('text' => $title));

        $this->data['data']     = $data;
        $this->data['type']     = $this->request->get['type'];
        $this->data['title']    = $title;
        $this->data['languages']= $this->model_localisation_language->getLanguages();
        if ($this->data['type'] == 'information') {
            $this->data['parents'] = $this->model_cms_cms->getInformationParents($this->request->get['store_id']);
        }

        $this->setParent('cms/cms' . ($this->request->get['type'] == 'blog' ? '/blog' : ''));
        $this->template = 'cms/editor_' . strtolower($this->data['type'] . '.tpl');
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function sort()
    {
        if (empty($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0;
        }

        if (empty($this->request->get['move']) || !in_array($this->request->get['move'], array('up', 'down'))) {
            $this->request->get['move'] = 'down';
        }

        if (!empty($this->request->get['id']) && isset($this->request->get['sort_order'])) {
            $this->load->model('cms/cms');
            $this->model_cms_cms->setOrder($this->request->get['store_id'], $this->request->get['id'], $this->request->get['sort_order'], $this->request->get['move']);
        }

        $this->redirect($this->url->link('cms/cms', 'token=' . $this->session->data['token'] . '&store_id=' . $this->request->get['store_id'], 'SSL'));
    }

    public function status()
    {
        if (empty($this->request->post['type'])) {
            $this->response->setOutput('empty_type');
            return;
        }

        if (empty($this->request->post['id'])) {
            $this->response->setOutput('empty_id');
        }
        $this->load->model('cms/cms');
        $this->model_cms_cms->setActive($this->request->post['type'], $this->request->post['id']);
        $this->response->setOutput('done');
    }

    public function remove()
    {
        if (empty($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0;
        }
        $store = $this->request->get['store_id'];
        $type = !isset($this->request->get['type']) ? false : $this->request->get['type'];
        $id = !isset($this->request->get['id']) ? false : $this->request->get['id'];
        $redir = $this->url->link('cms/cms', 'token=' . $this->session->data['token'] . '&store_id=' . $store, 'SSL');

        if ((int)$id == false || !$type) {
            $this->redirect($redir);
        }

        $this->load->model('cms/cms');
        $this->model_cms_cms->remove($type, $id);
        $this->redirect($redir);
    }

    private function getList($type)
    {
        $this->load->model('cms/cms');

        $items = $this->model_cms_cms->getItems($this->data['current_store'], $type);
        if (count($items)) {
            $this->data['items'] = $items;
        }
    }
}
