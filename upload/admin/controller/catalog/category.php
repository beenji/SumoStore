<?php
namespace Sumo;
class ControllerCatalogCategory extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'));

        $this->load->model('catalog/category');
        $this->data['token'] = $this->session->data['token'];

        $this->load->model('settings/stores');
        $this->data['stores'] = $this->model_settings_stores->getStores();


        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_ADD'));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'),
            'href'      => $this->url->link('catalog/category')
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_ADD')
        ));

        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_category->addCategory($this->request->post);

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $url .= '#store-' . $this->request->post['category_store'];

            $this->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_EDIT'));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'),
            'href'      => $this->url->link('catalog/category')
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_EDIT')
        ));

        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_category->editCategory($this->request->get['category_id'], $this->request->post);

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $url .= '#store-' . $this->request->post['category_store'];

            $this->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->data['category_id'] = $this->request->get['category_id'];

        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'));

        $this->load->model('catalog/category');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $category_id) {
                $this->model_catalog_category->deleteCategory($category_id);
            }

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function repair()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'));

        $this->load->model('catalog/category');

        if ($this->validateRepair()) {
            $this->model_catalog_category->repairCategories();

            $this->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function move_up()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'));
        $this->load->model('catalog/category');

        $this->model_catalog_category->moveCategoryUp($this->request->get['category_id']);

        // Move category up
        $this->getList();
    }

    public function move_down()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'));
        $this->load->model('catalog/category');

        $this->model_catalog_category->moveCategoryDown($this->request->get['category_id']);

        // Move category down
        $this->getList();
    }

    // SEO url preview
    public function preview_url()
    {
        $category_id = $this->request->get['category_id'];
        $store_id    = $this->request->get['store_id'];
        $language_id = $this->request->get['language_id'];
        $name        = $this->request->get['name'];
        $preview     = array($this->url->strToURI($name));

        // Get category-info
        if ($category_id > 0) {
            $this->load->model('catalog/category');
            $categories = $this->model_catalog_category->getCategoryDescriptions($category_id);

            if (isset($categories[$language_id]['name']) && !empty($categories[$language_id]['name'])) {
                $preview[] = $categories[$language_id]['name'];
            } else {
                // Fall back to default
                $preview[] = $categories[$this->config->get('language_id')]['name'];
            }
        }

        // URLify the url-chunks
        $preview = array_map(array($this->url, 'strToURI'), $preview);

        // Get store-info
        if ($store > 0) {
            $this->load->model('settings/stores');
            $store_url = $this->model_settings_stores->getSetting($store_id, 'store_url');
        } else {
            $this->load->model('settings/general');
            $store_url = $this->model_settings_general->getSetting('url');
        }

        $preview[] = trim($store_url, '/');

        // The category-three is ascending, flip everything around and all should be good!
        $preview = array_reverse($preview);
        $response = implode('/', $preview);

        $this->response->setOutput(json_encode($response));
    }

    protected function getList()
    {
        $page = 1;
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_CATEGORY_OVERVIEW'),
        ));

        $this->data['insert'] = $this->url->link('catalog/category/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['repair'] = $this->url->link('catalog/category/repair', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['url_sort_order'] = $this->url->link('catalog/category/order', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['categories'] = array();

        $data = array();
        $category_total = $this->model_catalog_category->getTotalCategories();
        $this->load->model('settings/stores');

        // Get all stores
        $stores = array();
        foreach ($this->model_settings_stores->getStores() as $list) {
            $this->data['stores'][$list['store_id']] = $list;
            $this->data['categories'][$list['store_id']] = array();
        }

        $results = $this->model_catalog_category->getCategories($data);

        foreach ($results as $store_id => $cats) {
            $store_data = $this->model_settings_stores->getSettings($store_id);
            $base = isset($store_data['base_http']) ? $store_data['base_http'] : '';
            foreach ($cats as $result) {
                if (!isset($result['category_id'])) {
                    // Subcat?
                    foreach ($result as $list) {
                        $this->data['categories'][$store_id][] = array(
                            'category_id'       => $list['category_id'],
                            'name'              => $list['name'],
                            'store_name'        => $list['store_name'],
                            'parent_name'       => !empty($list['parent_id']) ? isset($result[$list['parent_id']]['name']) ? $prev['name'] . ' &gt; ' .$result[$list['parent_id']]['name'] : $prev['name'] : '',
                            'store_id'          => $list['store_id'],
                            'parent_id'         => $list['parent_id'],
                            'description'       => $list['description'],
                            'meta_description'  => $list['meta_description'],
                            'meta_keyword'      => $list['meta_keyword'],
                            'status'            => $list['status'],
                            'selected'          => isset($this->request->post['selected']) && in_array($list['category_id'], $this->request->post['selected']),
                            'move_up'           => $this->url->link('catalog/category/move_up', 'token=' . $this->session->data['token'] . '&category_id=' . $list['category_id'] . $url, 'SSL'),
                            'move_down'         => $this->url->link('catalog/category/move_down', 'token=' . $this->session->data['token'] . '&category_id=' . $list['category_id'] .$url, 'SSL'),
                        );

                        if (empty($list['parent_id'])) {
                            $prev = $list;
                        }
                    }
                    continue;
                }
                if (!isset($prev)) {
                    $prev = $result;
                }

                $this->data['categories'][$store_id][] = array(
                    'category_id'       => $result['category_id'],
                    'name'              => $result['name'],
                    'store_name'        => $result['store_name'],
                    'parent_name'       => !empty($result['parent_id']) ? $prev['name'] : '',
                    'store_id'          => $result['store_id'],
                    'parent_id'         => $result['parent_id'],
                    'description'       => $result['description'],
                    'meta_description'  => $result['meta_description'],
                    'meta_keyword'      => $result['meta_keyword'],
                    'move_up'           => $this->url->link('catalog/category/move_up', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, 'SSL'),
                    'move_down'         => $this->url->link('catalog/category/move_down', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] .$url, 'SSL'),
                    'status'            => $result['status'],
                    'selected'          => isset($this->request->post['selected']) && in_array($result['category_id'], $this->request->post['selected']),
                );

                if (empty($result['parent_id'])) {
                    $prev = $result;
                }
            }
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

        $this->data['token'] = $this->session->data['token'];

        $this->document->addScript('view/js/pages/category_list.js');

        $this->template = 'catalog/category_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = array();
        }

        if (!isset($this->request->get['category_id'])) {
            $this->data['action'] = $this->url->link('catalog/category/insert', 'token=' . $this->session->data['token'], 'SSL');
        } else {
            $this->data['action'] = $this->url->link('catalog/category/update', 'token=' . $this->session->data['token'] . '&category_id=' . $this->request->get['category_id'], 'SSL');
        }

        $this->data['cancel'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
              $category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);
        }

        $this->data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();


        if (isset($this->request->post['parent_id'])) {
            $this->data['parent_id'] = $this->request->post['parent_id'];
        } elseif (!empty($category_info)) {
            $this->data['parent_id'] = $category_info['parent_id'];
        } else {
            $this->data['parent_id'] = 0;
        }

        $categories = $this->model_catalog_category->getCategories(array(), false);
        $js_categories = array();
        foreach ($categories as $store_id => $cats) {
            foreach ($cats as $result) {
                if (!isset($result['category_id'])) {
                    // Subcat?
                    foreach ($result as $list) {
                        $this->data['categories_to_choose'][$list['category_id']] = array(
                            'store_id'      => $store_id,
                            'category_id'   => $list['category_id'],
                            'name'          => (!empty($list['parent_id']) ? isset($prev[$list['parent_id']]['name']) ? $prev['name'] . ' &gt; ' .$prev[$list['parent_id']]['name'] : $prev['name'] . ' &gt; ' : '') . $list['name'],
                            'selected'      => $this->data['parent_id'] == $list['category_id'] ? true : false
                        );

                        if (empty($list['parent_id'])) {
                            $prev = $list;
                        }
                    }
                    continue;
                }
                if (!isset($prev)) {
                    $prev = $result;
                }
                $this->data['categories_to_choose'][$result['category_id']] = array(
                    'store_id'      => $store_id,
                    'category_id'   => $result['category_id'],
                    'name'          => (!empty($result['parent_id']) ? $prev['name'] . ' &gt; ' : '') . $result['name'],
                    'selected'      => $this->data['parent_id'] == $result['category_id'] ? true : false,
                );

                if (empty($result['parent_id'])) {
                    $prev = $result;
                }
            }
        }

        if (isset($this->request->post['category_description'])) {
            $this->data['category_description'] = $this->request->post['category_description'];
        } elseif (isset($this->request->get['category_id'])) {
            $this->data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($this->request->get['category_id']);
        } else {
            $this->data['category_description'] = array();
        }

        if (isset($this->request->post['path'])) {
            $this->data['path'] = $this->request->post['path'];
        } elseif (!empty($category_info)) {
            $this->data['path'] = $category_info['path'];
        } else {
            $this->data['path'] = '';
        }

        $this->load->model('settings/stores');

        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['store_default'] = $this->config->get('name');

        if (isset($this->request->post['category_store'])) {
            $this->data['category_store'] = $this->request->post['category_store'];
        } elseif (isset($this->request->get['category_id'])) {
            $this->data['category_store'] = $this->model_catalog_category->getCategoryStores($this->request->get['category_id']);
        } else {
            $this->data['category_store'] = 0;
        }

        if (isset($this->request->post['image'])) {
            $this->data['image'] = $this->request->post['image'];
        } elseif (!empty($category_info)) {
            $this->data['image'] = $category_info['image'];
        } else {
            $this->data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && file_exists(DIR_IMAGE . $this->request->post['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($category_info) && $category_info['image'] && file_exists(DIR_IMAGE . $category_info['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
        } else {
            $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->request->post['top'])) {
            $this->data['top'] = $this->request->post['top'];
        } elseif (!empty($category_info)) {
            $this->data['top'] = $category_info['top'];
        } else {
            $this->data['top'] = 0;
        }

        if (isset($this->request->post['column'])) {
            $this->data['column'] = $this->request->post['column'];
        } elseif (!empty($category_info)) {
            $this->data['column'] = $category_info['column'];
        } else {
            $this->data['column'] = 1;
        }

        if (isset($this->request->post['sort_order'])) {
            $this->data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($category_info)) {
            $this->data['sort_order'] = $category_info['sort_order'];
        } else {
            $this->data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } elseif (!empty($category_info)) {
            $this->data['status'] = $category_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        $this->data['cancel'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token']);
        $this->data['token'] = $this->session->data['token'];

        $this->document->addStyle('view/css/pages/uploader.css');

        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');
        $this->document->addScript('view/js/pages/category_form.js');

        $this->template = 'catalog/category_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        foreach ($this->request->post['category_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = Language::getVar('SUMO_ERROR_NAME');
            }
        }

        if ($this->request->post['category_store'] == '') {
            $this->error['category_store'] = true;
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateRepair()
    {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/category');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 20
            );

            $results = $this->model_catalog_category->getCategories($data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
    }

    public function status()
    {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            exit;
        }
        $return = array();
        if (!empty($this->request->post['category_id']) && isset($this->request->post['status'])) {
            $status = $this->request->post['status'];
            $this->load->model('catalog/category');
            $result = $this->model_catalog_category->updateStatus($this->request->post['category_id'], $status);
            $return['result'] = $result;
        }
        $this->response->setOutput(json_encode($return));
    }

    public function tostore()
    {
        if (!$this->user->hasPermission('modify', 'catalog/category')) {
            exit;
        }
        $return = array();
        $data = $this->request->post['cat'];
        if (!empty($data)) {
            $this->load->model('catalog/category');

            foreach ($data as $cat_id => $list) {
                if (strlen($list['store_id']) == 0) { continue; }
                $this->model_catalog_category->updateStore($cat_id, $list['store_id']);
            }
            $return['result'] = $result;
        }
        $this->response->setOutput(json_encode($return));
    }
}
