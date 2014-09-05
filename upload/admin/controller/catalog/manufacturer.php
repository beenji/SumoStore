<?php
namespace Sumo;
class ControllerCatalogManufacturer extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_BRANDS'));

        $this->load->model('catalog/manufacturer');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_BRANDS'));

        $this->load->model('catalog/manufacturer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_manufacturer->addManufacturer($this->request->post);
            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_BRANDS'));

        $this->load->model('catalog/manufacturer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_manufacturer->editManufacturer($this->request->get['manufacturer_id'], $this->request->post);

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_BRANDS'));

        $this->load->model('catalog/manufacturer');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $manufacturer_id) {
                $this->model_catalog_manufacturer->deleteManufacturer($manufacturer_id);
            }

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_BRANDS'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_BRANDS'),
        ));

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        }
        else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        }
        else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }
        else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['insert'] = $this->url->link('catalog/manufacturer/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('catalog/manufacturer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['cancel'] = $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['manufacturers'] = array();

        $data = array(
            'start' => ($page - 1) * $this->config->get('admin_limit'),
            'limit' => $this->config->get('admin_limit')
        );

        $manufacturer_total = $this->model_catalog_manufacturer->getTotalManufacturers();

        $results = $this->model_catalog_manufacturer->getManufacturers($data);

        foreach ($results as $result) {
            // Get keyword
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($result['manufacturer_id']);

            $this->data['manufacturers'][] = array(
                'manufacturer_id' => $result['manufacturer_id'],
                'name'            => $result['name'],
                'image'           => $result['image'],
                'keyword'         => $manufacturer_info['keyword'],
                'sort_order'      => $result['sort_order'],
                'selected'        => isset($this->request->post['selected']) && in_array($result['manufacturer_id'], $this->request->post['selected']),

                // Links
                'edit'          => $this->url->link('catalog/manufacturer/update', 'token=' . $this->session->data['token'] . '&manufacturer_id=' . $result['manufacturer_id'] . $url, 'SSL')
            );
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

        $url = '';

        $pagination = new Pagination();
        $pagination->total = $manufacturer_total;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('admin_limit');
        $pagination->text  = '';
        $pagination->url   = $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->getForm();

        $this->data['token'] = $this->session->data['token'];

        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');
        $this->document->addScript('view/js/pages/manufacturer_list.js');
        $this->document->addStyle('view/css/pages/uploader.css');

        $this->template = 'catalog/manufacturer_list.tpl';
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
            $this->data['error_name'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (!isset($this->request->get['manufacturer_id'])) {
            $this->data['manufacturer_id'] = 0;
            $this->data['action'] = $this->url->link('catalog/manufacturer/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        }
        else {
            $this->data['manufacturer_id'] = $this->request->get['manufacturer_id'];
            $this->data['action'] = $this->url->link('catalog/manufacturer/update', 'token=' . $this->session->data['token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, 'SSL');
        }

        $this->data['cancel'] = $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['manufacturer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
              $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
        }

        $this->data['token'] = $this->session->data['token'];

        if (isset($this->request->post['name'])) {
              $this->data['name'] = $this->request->post['name'];
        }
        elseif (!empty($manufacturer_info)) {
            $this->data['name'] = $manufacturer_info['name'];
        }
        else {
              $this->data['name'] = '';
        }

        $this->load->model('settings/stores');

        $stores = $this->model_settings_stores->getStores();
        $this->data['stores'] = array();
        foreach ($stores as $list) {
            $this->data['stores'][] = $list;
        }

        if (isset($this->request->post['manufacturer_store'])) {
            $this->data['manufacturer_store'] = $this->request->post['manufacturer_store'];
        }
        elseif (isset($this->request->get['manufacturer_id'])) {
            $this->data['manufacturer_store'] = $this->model_catalog_manufacturer->getManufacturerStores($this->request->get['manufacturer_id']);
        }
        else {
            $this->data['manufacturer_store'] = array(0);
        }

        if (isset($this->request->post['keyword'])) {
            $this->data['keyword'] = $this->request->post['keyword'];
        }
        elseif (!empty($manufacturer_info)) {
            $this->data['keyword'] = $manufacturer_info['keyword'];
        }
        else {
            $this->data['keyword'] = '';
        }

        if (isset($this->request->post['image'])) {
            $this->data['image'] = $this->request->post['image'];
        }
        elseif (!empty($manufacturer_info)) {
            $this->data['image'] = $manufacturer_info['image'];
        }
        else {
            $this->data['image'] = '';
        }
    }

    protected function validateForm()
    {
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = Language::getVar('SUMO_ERROR_NAME');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateDelete()
    {
        $this->load->model('catalog/product');

        foreach ($this->request->post['selected'] as $manufacturer_id) {
            $product_total = $this->model_catalog_product->getTotalProductsByManufacturerId($manufacturer_id);

            if ($product_total) {
                $this->error['warning'] = sprintf(Language::getVar('SUMO_ERROR_MANUFACTURER_HAS_PRODUCTS'), $product_total);
            }
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/manufacturer');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 20
            );

            $results = $this->model_catalog_manufacturer->getManufacturers($data);

            foreach ($results as $result) {
                $json[] = array(
                    'manufacturer_id' => $result['manufacturer_id'],
                    'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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
}
