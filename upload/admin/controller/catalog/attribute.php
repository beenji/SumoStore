<?php
namespace Sumo;
class ControllerCatalogAttribute extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_ATTRIBUTES'));

        $this->load->model('catalog/attribute');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_ATTRIBUTES'));

        $this->load->model('catalog/attribute');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_attribute->addAttributeGroup($this->request->post);

            $this->redirect($this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function insert_ajax()
    {
        $this->load->model('catalog/attribute');

        $output = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $output = $this->model_catalog_attribute->addAttributeGroup($this->request->post);
        } 
        elseif (isset($this->error['warning'])) {
            $output = array('error' => $this->error['warning']);
        }

        $this->response->setOutput(json_encode($output));
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_ATTRIBUTES'));

        $this->load->model('catalog/attribute');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_attribute->editAttributeGroup($this->request->get['attribute_group_id'], $this->request->post);

            $this->redirect($this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_ATTRIBUTES'));

        $this->load->model('catalog/attribute');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $attribute_group_id) {
                $this->model_catalog_attribute->deleteAttributeGroup($attribute_group_id);
            }

            $this->redirect($this->url->link('catalog/attribute', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_ATTRIBUTES'),
        ));

        $this->data['heading_title'] = Language::getVar('SUMO_NOUN_CATALOG_ATTRIBUTES');

        $this->data['attribute_groups'] = array();

        // Some basic filtering
        $page = 1;
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }

        $data = array(
            'start' => ($page - 1) * 25,
            'limit' => 25
        );

        foreach ($this->model_catalog_attribute->getAttributeGroups($data) as $attribute_group) {
            // Get attributes
            $attributes = $this->model_catalog_attribute->getAttributes(array(
                'filter_attribute_group_id' => $attribute_group['attribute_group_id']
            ));

            $group_attributes = array();

            // Barnacles! array_column is only available since PHP 5.5
            foreach ($attributes as $attribute) {
                $group_attributes[] = $attribute['name'];
            }

            $this->data['attribute_groups'][] = array(
                'attribute_group_id' => $attribute_group['attribute_group_id'],
                'name'               => $attribute_group['name'],
                'attributes'         => implode(', ', $group_attributes),

                // Links
                'edit'               => $this->url->link('catalog/attribute/update', 'token=' . $this->session->data['token'] . '&attribute_group_id=' . $attribute_group['attribute_group_id'], 'SSL')
            );
        }

        // All view-variables gather here please..
        $this->data['insert'] = $this->url->link('catalog/attribute/insert', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['delete'] = $this->url->link('catalog/attribute/delete', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL');

        $this->document->addScript('view/js/pages/attribute_list.js');

        $this->getForm();

        $pagination = new Pagination();
        $pagination->total = $this->model_catalog_attribute->getTotalAttributeGroups();
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->template = 'catalog/attribute_list.tpl';
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
        }
        else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        }
        else {
            $this->data['error_name'] = array();
        }

        if (!isset($this->request->get['attribute_group_id'])) {
            $this->data['attribute_group_id'] = 0;
            $this->data['action'] = $this->url->link('catalog/attribute/insert', 'token=' . $this->session->data['token'], 'SSL');
        }
        else {
            $this->data['attribute_group_id'] = $this->request->get['attribute_group_id'];
            $this->data['action'] = $this->url->link('catalog/attribute/update', 'token=' . $this->session->data['token'] . '&attribute_group_id=' . $this->request->get['attribute_group_id'], 'SSL');
        }

        $this->data['cancel'] = $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['attribute_group_description'])) {
            $this->data['attribute_group_description'] = $this->request->post['attribute_group_description'];
        }
        elseif (isset($this->request->get['attribute_group_id'])) {
            $this->data['attribute_group_description'] = $this->model_catalog_attribute->getAttributeGroupDescriptions($this->request->get['attribute_group_id']);
        }
        else {
            $this->data['attribute_group_description'] = array();
        }

        if (isset($this->request->post['attribute'])) {
            $attributes = $this->request->post['attribute'];

            foreach ($attributes as $attribute) {
                // Add attribute ID to language array
                foreach ($attribute['attribute_description'] as $language_id => $value) {
                    $attribute['attribute_description'][$language_id]['attribute_id'] = $attribute['attribute_id'];
                }

                $this->data['attributes'][] = $attribute['attribute_description'];
            }
        }
        elseif (isset($this->request->get['attribute_group_id'])) {
            $attributes = $this->model_catalog_attribute->getAttributes(array(
                'filter_attribute_group_id' => $this->request->get['attribute_group_id']
            ));

            foreach ($attributes as $attribute) {
                $this->data['attributes'][] = $this->model_catalog_attribute->getAttributeDescriptions($attribute['attribute_id']);
            }
        }
        else {
            $this->data['attributes'] = array();
        }
    }

    protected function validateForm()
    {
        $error = array();

        foreach ($this->request->post['attribute_group_description'] as $language_id => $value) {
            if ($language_id == $this->config->get('language_id') && ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64))) {
                $error[] = Language::getVar('SUMO_ERROR_ALL_LANGUAGES_NAME');

                // We only need this error once
                break;
            }
        }

        foreach ($this->request->post['attribute'] as $attribute) {
            foreach ($attribute['attribute_description'] as $language_id => $value) {
                if ($language_id == $this->config->get('language_id') && ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64))) {
                    $error[] = Language::getVar('SUMO_ERROR_ALL_LANGUAGES_ATTRIBUTES');

                    // Same recipe
                    break 2;
                }
            }
        }

        if (!empty($error)) {
            $this->error['warning'] = implode('<br />', $error);
            return false;
        }
        return true;
    }

    protected function validateDelete()
    {
        $this->load->model('catalog/product');
        $this->load->model('catalog/attribute');

        foreach ($this->request->post['selected'] as $attribute_group_id) {
            $attributes = $this->model_catalog_attribute->getAttributes(array('filter_attribute_group_id' => $attribute_group_id));

            foreach ($attributes as $attribute) {
                $product_total = $this->model_catalog_product->getTotalProductsByAttributeId($attribute['attribute_id']);

                if ($product_total) {
                    $this->error['warning'] = sprintf(Language::getVar('SUMO_ERROR_ATTRIBUTE_IN_USE'), $product_total);
                }
            }
        }

        if (!$this->error) {
            return true;
        }
        return false;

    }
}
