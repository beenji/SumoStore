<?php
namespace Sumo;
class ControllerCatalogProduct extends Controller
{
    private $error = array();

    public function index()
    {
        $this->data['current_url'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'], 'SSL');

        $title = Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_OVERVIEW');
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard', '', 'SSL'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_OVERVIEW'),
        ));

        $this->document->setTitle($title);

        $this->load->model('catalog/product');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_ADD'));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_OVERVIEW'),
            'href'      => $this->url->link('catalog/product/index', 'token=' . $this->session->data['token'], 'SSL'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_ADD')
        ));

        $this->load->model('catalog/product');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $id = $this->model_catalog_product->addProduct();
            $this->model_catalog_product->editProduct($id, $this->request->post);

            $this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&' . $this->getFilterURL(), 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $title = Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_EDIT');
        $this->document->setTitle($title);

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_OVERVIEW'),
            'href'      => $this->url->link('catalog/product'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => $title
        ));

        $this->load->model('catalog/product');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_catalog_product->editProduct($this->request->get['product_id'], $this->request->post);
            $files = glob(DIR_IMAGE . 'cache/' . $_POST['product_store'] . '/' . $_POST['product_category'] . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            $this->session->data['success'] = Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_SUCCESS');

            $this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&' . $this->getFilterURL(), 'SSL'));
        }

        $this->getForm();

        $this->data['product_id'] = $this->request->get['product_id'];
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_OVERVIEW'));

        $this->load->model('catalog/product');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $product_id) {
                $this->model_catalog_product->deleteProduct($product_id);
            }

            $this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&' . $this->getFilterURL(), 'SSL'));
        }

        $this->getList();
    }

    public function copy()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_PRODUCT_OVERVIEW'));

        $this->load->model('catalog/product');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $product_id) {
                $this->model_catalog_product->copyProduct($product_id);
            }

            $this->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&' . $this->getFilterURL(), 'SSL'));
        }

        $this->getList();
    }

    public function find_product()
    {
        $response = array();

        if (!empty($this->request->get['product']) && strlen($this->request->get['product']) > 1) {
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $products = $this->model_catalog_product->getProducts(array('filter_name' => $this->request->get['product']));

            foreach ($products as $product)
            {
                if (!empty($product['model'])) {
                    $product['name'] = $product['model'] . ' - ' . $product['name'];
                }
                else {
                    $product['name'] = 'P' . $product['product_id'] . ' - ' . $product['name'];
                }
                $response[$product['name']] = array(
                    'id'        => $product['product_id'],
                    'name'      => $product['name'],
                    'model'     => $product['model'],
                    'model_2'   => $product['model_2'],
                    'price'     => $product['price'],
                    'weight'    => $product['std_weight'],
                    'tax'       => $product['tax_percentage'],
                    'image'     => $this->model_tool_image->resize($product['image'], 50, 50)
                );
            }
        }

        $this->response->setOutput(json_encode($response));
    }

    public function get_product_options()
    {
        $productID = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        $response  = array();

        if (!empty($productID)) {
            $this->load->model('catalog/product');

            $productInfo = $this->model_catalog_product->getProduct($productID);
            //$response = $productInfo['product_options'];
            $response = $this->model_catalog_product->getProductOptions($productID);

            foreach ($response as $i => $option) {
                foreach ($response[$i]['product_option_value'] as $j => $option_value) {
                    $response[$i]['product_option_value'][$j]['price'] = round(floatval($response[$i]['product_option_value'][$j]['price']) * (1 + ($productInfo['tax_percentage'] / 100)), 4);
                }
            }
        }

        $this->response->setOutput(json_encode($response));
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
        $this->load->model('catalog/category');
        $categories = $this->model_catalog_category->getCategoryDescriptions($category_id);

        if (isset($categories[$language_id]['name']) && !empty($categories[$language_id]['name'])) {
            $preview[] = $categories[$language_id]['name'];
        } else {
            // Fall back to default
            $preview[] = $categories[$this->config->get('config_language_id')]['name'];
        }

        // URLify the url-chunks
        $preview = array_map(array($this->url, 'strToURI'), $preview);

        // Get store-info
        /*$this->load->model('settings/stores');

        if ($store > 0) {
            $store = $this->model_settings_stores->getStore($store_id);
            $preview[] = trim($store['url'], '/');
        } else {
            $preview[] = trim($this->config->get('config_url'), '/');
        }*/

        // The category-three is ascending, flip everything around and all should be good!
        $preview = array_reverse($preview);
        $response = implode('/', $preview);

        $this->response->setOutput(json_encode($response));
    }

    protected function getFilterURL()
    {
        // Set filter URL
        $data = array(
            'filter_name'           => '',
            'filter_model'          => '',
            'filter_price_from'     => '',
            'filter_price_to'       => '',
            'filter_stock_from'     => '',
            'filter_stock_to'       => '',
            'filter_stock'          => '',
            'filter_category'       => '',
            'filter_brand'          => '',
            'filter_store'          => 0,
            'filter_model_supplier' => ''
        );

        $filterURL = array();

        foreach ($data as $filter => $default)
        {
            if (isset($this->request->get[$filter]) && !empty($this->request->get[$filter]))
            {
                $filterURL[$filter] = $this->request->get[$filter];
            }
        }

        return http_build_query($filterURL);
    }

    protected function getList()
    {
        $this->load->model('tool/image');
        $this->load->model('catalog/category');
        $this->load->model('settings/stores');

        $page = 1;
        if (!empty($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }

        $filterURL = $this->getFilterURL();

        $this->data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&' . $filterURL, 'SSL');
        $this->data['insert'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . '&' . $filterURL, 'SSL');
        $this->data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . '&' . $filterURL, 'SSL');
        $this->data['url_sort_order'] = $this->url->link('catalog/product/order', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['token'] = $this->session->data['token'];

        $this->data['products'] = $this->data['stores'] = $this->data['categories'] = array();

        // Read limit from url (if present)
        // if not, read from session save
        // in session if different from it
        $limit = 25;
        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        }
        elseif (isset($this->session->data['limit'])) {
            $limit = $this->session->data['limit'];
        }

        // Save limit in session and store for TPL
        $this->session->data['limit'] = $limit;
        $this->data['limit'] = $limit;

        $start = ($page - 1) * $limit;

        $data = array(
            'filter_name'           => '',
            'filter_model'          => '',
            'filter_price_from'     => '',
            'filter_price_to'       => '',
            'filter_stock_from'     => '',
            'filter_stock_to'       => '',
            'filter_stock'          => '',
            'filter_category'       => '',
            'filter_brand'          => '',
            'filter_store'          => 0,
            'filter_model_supplier' => ''
        );

        $this->data['advanced_search'] = false;

        foreach ($data as $filter => $default)
        {
            $data[$filter] = isset($this->request->get[$filter]) ? $this->request->get[$filter] : $default;
            $this->data[$filter] = $data[$filter];

            if (!empty($data[$filter]))
            {
                if ($filter != 'filter_store' && $filter != 'filter_category') {
                    // Show advanced search form
                    $this->data['advanced_search'] = true;
                }
            }
        }

        // Find correct store with category_id (if selected)
        if (!empty($data['filter_category'])) {
            $categoryInfo = $this->model_catalog_category->getCategory($data['filter_category']);

            // Overwrite the already filled data-filters
            $data['filter_store'] = $this->data['filter_store'] = $this->request->get['filter_store'] = (int)$categoryInfo['store_id'];
            $filterURL = $this->getFilterURL();
        }

        // Special add-on for stock-filter
        if (!empty($data['filter_stock_from'])) {
            $this->data['filter_stock_control'] = 'stock_from';
            $this->data['filter_stock'] = $this->data['filter_stock_from'];
        }
        elseif (!empty($data['filter_stock_to'])) {
            $this->data['filter_stock_control'] = 'stock_to';
            $this->data['filter_stock'] = $this->data['filter_stock_to'];
        }
        else {
            $this->data['filter_stock_control'] = 'stock';
        }

        if (!$this->data['advanced_search']) {
            $search = !empty($this->request->get['search']) ? $this->request->get['search'] : false;
            if ($search) {
                $this->data['search'] = $search;
                $data['filter_name'] = $search;
            }
        }

        foreach ($this->model_settings_stores->getStores() as $list) {
            $this->data['stores'][] = array_merge($list, array(
                'total'      => $this->model_catalog_product->getTotalProducts(array('filter_store' => $list['store_id'])),
                'store_link' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&filter_store=' . $list['store_id'])
            ));
        }

        foreach ($this->model_catalog_category->getCategoriesAsList() as $list) {
            // Indent the name for a subcategory
            for ($i = 0; $i < $list['level']; $i++) {
                $list['name'] = '&nbsp;&nbsp;&nbsp;' . $list['name'];
            }

            $this->data['categories'][$list['store_id']][] = $list;
        }

        $data['limit'] = $limit;
        $data['start'] = $start;

        $products = $this->model_catalog_product->getProducts($data);
        $totalProducts = $this->model_catalog_product->getTotalProducts($data);

        // List products
        foreach ($products as $product) {
            $image = $this->model_tool_image->resize('no_image.jpg', 60, 60);
            if ($product['image'] && file_exists(DIR_IMAGE . $product['image'])) {
                $image = $this->model_tool_image->resize($product['image'], 60, 60);
            }

            $special = 0;

            $productSpecials = $this->model_catalog_product->getProductSpecials($product['product_id']);

            foreach ($productSpecials  as $productSpecial) {
                if (($productSpecial['date_start'] == '0000-00-00' || $productSpecial['date_start'] < date('Y-m-d')) && ($productSpecial['date_end'] == '0000-00-00' || $productSpecial['date_end'] > date('Y-m-d'))) {
                    $special = $productSpecial['price'];

                    break;
                }
            }

            $this->data['products'][] = array_merge($product, array(
                'model'         => !empty($product['model_2']) ? $product['model_2'] : $product['model'],
                'price_in'      => $product['price'] * (1 + $product['tax_percentage'] / 100),
                'special'       => !empty($special) ? Formatter::currency($special) : '',
                'special_in'    => !empty($special) ? Formatter::currency($special * (1 + $product['tax_percentage'] / 100)) : '',
                'image'         => $image,
                'options'       => $this->model_catalog_product->getProductOptions($product['product_id']),
                'edit'          => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'] . '&' . $filterURL, 'SSL')
            ));
        }

        $pagination = new Pagination();
        $pagination->total = $totalProducts;
        $pagination->limit = $limit;
        $pagination->page  = $page;
        $pagination->url   = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&page={page}&' . $filterURL);

        $this->data['pagination'] = $pagination->renderAdmin();
        $this->data['filter_url'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . (!empty($filterURL) ? '&' . $filterURL : ''));

        // Get brands
        $this->load->model('catalog/manufacturer');

        $this->data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

        $this->document->addStyle('view/css/pages/product.css');
        $this->document->addScript('view/js/pages/product_list.js');

        $this->template = 'catalog/product_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $output = $this->render();
        $this->response->setOutput($output);
    }

    protected function getForm()
    {
        $this->setParent('catalog/product/insert');
        $this->data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        }
        else {
            $this->data['error_name'] = array();
        }

        if (isset($this->error['meta_description'])) {
            $this->data['error_meta_description'] = $this->error['meta_description'];
        }
        else {
            $this->data['error_meta_description'] = array();
        }

        if (isset($this->error['description'])) {
            $this->data['error_description'] = $this->error['description'];
        }
        else {
            $this->data['error_description'] = array();
        }

        if (isset($this->error['model'])) {
            $this->data['error_model'] = $this->error['model'];
        }
        else {
            $this->data['error_model'] = '';
        }

        if (isset($this->error['date_available'])) {
            $this->data['error_date_available'] = $this->error['date_available'];
        }
        else {
            $this->data['error_date_available'] = '';
        }

        if (!isset($this->request->get['product_id'])) {
            $this->data['action'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'], 'SSL');
            $this->data['id'] = 'XX';
        }
        else {
            $this->data['action'] = $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'], 'SSL');
            $this->data['id'] = $this->request->get['product_id'];
        }

        $this->data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&' . $this->getFilterURL(), 'SSL');

        if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
        }

        $this->data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['product_description'])) {
            $this->data['product_description'] = $this->request->post['product_description'];
        } elseif (isset($this->request->get['product_id'])) {
            $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
        } else {
            foreach ($this->data['languages'] as $language) {
                $this->data['product_description'][$language['language_id']] = array(
                    'title'             => '',
                    'keyword'           => '',
                    'meta_description'  => '',
                    'meta_keyword'      => ''
                );
            }
        }

        if (isset($this->request->post['model'])) {
              $this->data['model'] = $this->request->post['model_2'];
        } elseif (!empty($product_info)) {
            $this->data['model'] = $product_info['model_2'];
        } else {
              $this->data['model'] = '';
        }

        $extra_info_types = array(
            'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn'
        );
        $this->data['extra_info'] = array();
        foreach ($extra_info_types as $type) {
            if (isset($this->request->post[$type])) {
                $this->data['extra_info'][] = array(
                    'type' => $type,
                    'value' => $this->request->post[$type],
                    'visible' => $this->request->post[$type . '_visible']
                );
            }
            else if (!empty($product_info[$type])) {
                $this->data['extra_info'][] = array(
                    'type' => $type,
                    'value' => $product_info[$type],
                    'visible' => $product_info[$type . '_visible']
                );
            }
        }

        if (isset($this->request->post['location'])) {
            $this->data['location'] = $this->request->post['location'];
        } elseif (!empty($product_info)) {
            $this->data['location'] = $product_info['location'];
        } else {
            $this->data['location'] = '';
        }

        $this->load->model('settings/stores');

        $stores = array();
        /*$stores[] = array(
            'url' => HTTP_CATALOG,
            'name' => $this->config->get('config_name'),
            'store_id' => 0
        );*/
        foreach ($this->model_settings_stores->getStores() as $list) {
            $stores[$list['store_id']] = $list;
        }
        $this->data['stores'] = $stores;

        if (isset($this->request->post['product_store'])) {
            $this->data['product_store'] = $this->request->post['product_store'];
        } elseif (isset($this->request->get['product_id'])) {
            $this->data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
        } else {
            $this->data['product_store'] = 0;
        }

        if (isset($this->request->post['image'])) {
            $this->data['image'] = $this->request->post['image'];
        } elseif (!empty($product_info)) {
            $this->data['image'] = $product_info['image'];
        } else {
            $this->data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && file_exists(DIR_IMAGE . $this->request->post['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 150, 150);
        } elseif (!empty($product_info) && $product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
            $this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], 150, 150);
        } else {
            $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 150, 150);
        }

        $this->data['thumb_extra'] = $this->model_tool_image->resize('no_image.jpg', 125, 125);

        if (isset($this->request->post['shipping'])) {
              $this->data['shipping'] = $this->request->post['shipping'];
        } elseif (!empty($product_info)) {
              $this->data['shipping'] = $product_info['shipping'];
        } else {
            $this->data['shipping'] = 1;
        }


        if (isset($this->request->post['model_supplier'])) {
            $this->data['model_supplier'] = $this->request->post['model_supplier'];
        } elseif (!empty($product_info)) {
            $this->data['model_supplier'] = $product_info['model_supplier'];
        } else {
            $this->data['model_supplier'] = '';
        }

        if (isset($this->request->post['product_price'])) {
              $this->data['price'] = $this->request->post['product_price'];
        } elseif (!empty($product_info)) {
            $this->data['price'] = $product_info['price'];
        } else {
              $this->data['price'] = '0.0000';
        }

        // Get tax rates
        $this->load->model('settings/general');

        $tax_percentages = $this->model_settings_general->getSetting('tax_percentage');

        if (is_array($tax_percentages)) {
            foreach ($tax_percentages as $tp) {
                if (is_array($tp)) {
                    // Extra tax percentages
                    foreach ($tp as $tpExtra) {
                        $this->data['tax_percentages'][] = $tpExtra;
                    }
                } else {
                    // Default tax percentage
                    $this->data['tax_percentages'][] = $tp;
                    $this->data['tax_percentage'] = $tp;
                }
            }
        } else {
            $this->data['tax_percentages'] = array();
        }

        if (isset($this->request->post['tax_percentage'])) {
            $this->data['tax_percentage'] = $this->request->post['tax_percentage'];
        } elseif (!empty($product_info)) {
            $this->data['tax_percentage'] = $product_info['tax_percentage'];
        } elseif (!isset($this->data['tax_percentage'])) {
            $this->data['tax_percentage'] = 0;
        }

        if (isset($this->request->post['date_available'])) {
            $this->data['date_available'] = $this->request->post['date_available'];
        } elseif (!empty($product_info) && $product_info['date_available'] != '0000-00-00') {
            $this->data['date_available'] = Formatter::date($product_info['date_available']);
        } else {
            $this->data['date_available'] = Formatter::date(time() - 86400);
        }

        if (isset($this->request->post['product_quantity'])) {
            $this->data['quantity'] = $this->request->post['product_quantity'];
        } elseif (!empty($product_info)) {
            $this->data['quantity'] = $product_info['quantity'];
        } else {
            $this->data['quantity'] = 1;
        }

        $this->data['stock_linked_products'] = $this->model_catalog_product->getLinkedProducts();
        $this->data['stock_product_name'] = '';

        if (isset($this->request->post['stock_id'])) {
            $this->data['stock_id'] = $this->request->post['stock_id'];
            $this->data['stock_product'] = $this->request->post['stock_product'];
            $this->data['stock_product_name'] = $this->request->post['stock_product_name'];
        } elseif (!empty($product_info)) {
            $this->data['stock_id'] = $product_info['stock_id'];
            if ($product_info['stock_id'] != $this->data['id']) {
                $this->data['stock_product'] = true;
                $stock_product_data = $this->model_catalog_product->getProduct($product_info['stock_id']);
                $this->data['stock_product_name'] = $stock_product_data['name'];
            }
            else {
                $this->data['stock_product'] = false;
            }
        } else {
            $this->data['stock_id'] = 0;
            $this->data['stock_product'] = false;
        }

        $this->load->model('localisation/stock_status');

        $this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

        if (isset($this->request->post['stock_status_id'])) {
            $this->data['stock_status_id'] = $this->request->post['stock_status_id'];
        } elseif (!empty($product_info)) {
            $this->data['stock_status_id'] = $product_info['stock_status_id'];
        } else {
            $this->data['stock_status_id'] = $this->config->get('stock_status_id');
        }

        if (isset($this->request->post['stock_visible'])) {
            $this->data['stock_visible'] = $this->request->post['stock_visible'];
        } elseif (!empty($product_info)) {
            $this->data['stock_visible'] = $product_info['stock_visible'];
        } else {
            $this->data['stock_visible'] = 2;
        }

        if (isset($this->request->post['minimum'])) {
            $this->data['minimum'] = $this->request->post['minimum'];
        } elseif (!empty($product_info)) {
            $this->data['minimum'] = $product_info['minimum'];
        } else {
            $this->data['minimum'] = 1;
        }

        if (isset($this->request->post['subtract'])) {
            $this->data['subtract'] = $this->request->post['subtract'];
        } elseif (!empty($product_info)) {
            $this->data['subtract'] = $product_info['subtract'];
        } else {
            $this->data['subtract'] = 1;
        }

        if (isset($this->request->post['sort_order'])) {
            $this->data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($product_info)) {
            $this->data['sort_order'] = $product_info['sort_order'];
        } else {
            $this->data['sort_order'] = 1;
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } elseif (!empty($product_info)) {
            $this->data['status'] = $product_info['status'];
        } else {
            $this->data['status'] = 1;
        }

        if (isset($this->request->post['product_weight'])) {
            $this->data['weight'] = $this->request->post['product_weight'];
        } elseif (!empty($product_info)) {
            $this->data['weight'] = $product_info['weight'];
        } else {
            $this->data['weight'] = '';
        }

        $this->load->model('localisation/weight_class');

        $this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

        if (isset($this->request->post['weight_class_id'])) {
            $this->data['weight_class_id'] = $this->request->post['weight_class_id'];
        } elseif (!empty($product_info)) {
            $this->data['weight_class_id'] = $product_info['weight_class_id'];
        } else {
            $this->data['weight_class_id'] = $this->config->get('config_weight_class_id');
        }

        if (isset($this->request->post['length'])) {
            $this->data['length'] = $this->request->post['length'];
        } elseif (!empty($product_info)) {
            $this->data['length'] = $product_info['length'];
        } else {
            $this->data['length'] = '';
        }

        if (isset($this->request->post['width'])) {
            $this->data['width'] = $this->request->post['width'];
        } elseif (!empty($product_info)) {
            $this->data['width'] = $product_info['width'];
        } else {
            $this->data['width'] = '';
        }

        if (isset($this->request->post['height'])) {
            $this->data['height'] = $this->request->post['height'];
        } elseif (!empty($product_info)) {
            $this->data['height'] = $product_info['height'];
        } else {
            $this->data['height'] = '';
        }

        if (isset($this->request->post['cost_price'])) {
            $this->data['cost_price'] = $this->request->post['cost_price'];
        } elseif (!empty($product_info)) {
            $this->data['cost_price'] = $product_info['cost_price'];
        } else {
            $this->data['cost_price'] = '0.0000';
        }

        $this->load->model('localisation/length_class');

        $this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

        if (isset($this->request->post['length_class_id'])) {
            $this->data['length_class_id'] = $this->request->post['length_class_id'];
        } elseif (!empty($product_info)) {
            $this->data['length_class_id'] = $product_info['length_class_id'];
        } else {
            $this->data['length_class_id'] = $this->config->get('config_length_class_id');
        }

        $this->load->model('catalog/manufacturer');
        $this->data['manufacturers'] = array();
        $this->data['manufacturers'][0] = array(
            'manufacturer_id' => 0,
            'name'  => Language::getVar('SUMO_NOUN_NONE')
        );

        foreach($this->model_catalog_manufacturer->getManufacturers() as $man) {
            $this->data['manufacturers'][] = $man;
        }

        if (isset($this->request->post['manufacturer_id'])) {
            $this->data['manufacturer_id'] = $this->request->post['manufacturer_id'];
        } elseif (!empty($product_info)) {
            $this->data['manufacturer_id'] = $product_info['manufacturer_id'];
        } else {
            $this->data['manufacturer_id'] = 0;
        }

        if (isset($this->request->post['manufacturer'])) {
              $this->data['manufacturer'] = $this->request->post['manufacturer'];
        } elseif (!empty($product_info)) {
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

            if ($manufacturer_info) {
                $this->data['manufacturer'] = $manufacturer_info['name'];
            } else {
                $this->data['manufacturer'] = '';
            }
        } else {
            $this->data['manufacturer'] = '';
        }

        // Categories
        $this->load->model('catalog/category');

        if (isset($this->request->post['product_category'])) {
            $category = $this->request->post['product_category'];
        } elseif (isset($this->request->get['product_id'])) {
            $category = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
        } else {
            $category = array();
        }

        $this->data['product_category'] = $category;

        $categories = $this->model_catalog_category->getCategories();

        $tmp_cats = array();
        foreach ($categories as $store => $storeCats) {
            foreach ($storeCats as $category_info) {
                if (!isset($category_info['name'])) {
                    foreach ($category_info as $cat) {
                        $tmp_cats2[$cat['category_id']] = $cat['name'];
                        $this->data['product_categories'][] = array(
                            'store_id'    => $store,
                            'category_id' => $cat['category_id'],
                            'name'        => $tmp_cats2[$cat['parent_id']] . ' &gt; ' . $cat['name'],
                            'store_name'  => $stores[$store]['name'],
                            'selected'    => in_array($cat['category_id'], $category) ? true : false
                        );
                    }
                    continue;
                }
                $this->data['product_categories'][] = array(
                    'store_id'    => $store,
                    'category_id' => $category_info['category_id'],
                    'name'        => $category_info['name'],
                    'store_name'  => $stores[$store]['name'],
                    'selected'    => in_array($category_info['category_id'], $category) ? true : false
                );
                $tmp_cats[$category_info['category_id']] = $category_info['name'];
                $tmp_cats2[$category_info['category_id']] = $category_info['name'];
            }
        }

        // Attributes
        $this->load->model('catalog/attribute');
        $posted = false;

        if (isset($this->request->post['attribute'])) {
            $posted = true;
            $product_attributes = $this->request->post['attribute'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_attributes = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
        } else {
            $product_attributes = array();
        }

        $this->data['attribute_sets'] = array();

        foreach ($this->model_catalog_attribute->getAttributeGroups() as $group) {
            $attributes = $this->model_catalog_attribute->getAttributes(array('filter_attribute_group_id' => $group['attribute_group_id']));

            // Check certain attributes?
            foreach ($attributes as $key => $attribute) {
                if (in_array($attribute['attribute_id'], $product_attributes)) {
                    $attributes[$key]['checked'] = true;
                } else {
                    $attributes[$key]['checked'] = false;
                }
            }

            $this->data['attribute_sets'][] = array(
                'name'          => $group['name'],
                'attributes'    => $attributes
            );
        }

        // Options
        if (isset($this->request->post['product_option'])) {
            $product_options = $this->request->post['product_option'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
        } else {
            $product_options = array();
        }

        // Set option prices
        foreach ($product_options as $i => $option) {
            if (isset($option['product_option_value'])) {
                foreach ($option['product_option_value'] as $j => $option_value) {
                    $product_options[$i]['product_option_value'][$j]['price'] = round(floatval($product_options[$i]['product_option_value'][$j]['price']) * (1 + ($product_info['tax_percentage'] / 100)), 4);
                }
            }
        }

        $this->data['product_options'] = $product_options;

        $this->load->model('sale/customer_group');
        $this->load->model('sale/volume');

        //$this->data['volumes'] = $this->model_sale_volume->getVolumes();
        $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

        if (isset($this->request->post['product_discount'])) {
            $product_discounts = $this->request->post['product_discount'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
        } else {
            $product_discounts = array();
        }

        foreach ($product_discounts as $i => $discount) {
            $product_discounts[$i]['price_in'] = Formatter::currency($discount['price'] + ($discount['price'] / 100 * $this->data['tax_percentage']));
        }

        $this->data['product_discounts'] = $product_discounts;

        if (isset($this->request->post['product_special'])) {
            $product_specials = $this->request->post['product_special'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_specials = $this->model_catalog_product->getProductSpecials($this->request->get['product_id']);
        } else {
            $product_specials = array();
        }

        foreach ($product_specials as $i => $special) {
            $product_specials[$i]['price_in'] = Formatter::currency($special['price'] + ($special['price'] / 100 * $this->data['tax_percentage']));
        }

        $this->data['product_specials'] = $product_specials;

        // Images
        if (isset($this->request->post['product_image'])) {
            $product_images = $this->request->post['product_image'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_images = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
        } else {
            $product_images = array();
        }

        $this->data['product_images'] = array();

        foreach ($product_images as $product_image) {
            if ($product_image['image'] && file_exists(DIR_IMAGE . $product_image['image'])) {
                $image = $product_image['image'];
            } else {
                $image = 'no_image.jpg';
            }

            $this->data['product_images'][] = array(
                'image'      => $image,
                'thumb'      => $this->model_tool_image->resize($image, 125, 125),
                'sort_order' => $product_image['sort_order']
            );
        }

        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 125, 125);

        // Downloads
        $this->load->model('catalog/download');

        if (isset($this->request->post['product_download'])) {
            $product_downloads = $this->request->post['product_download'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_downloads = $this->model_catalog_product->getProductDownloads($this->request->get['product_id']);
        } else {
            $product_downloads = array();
        }
        $this->data['product_downloads'] = array();

        foreach ($product_downloads as $download_id) {
            $this->data['product_downloads'][] = $this->model_catalog_download->getDownload($download_id);
        }

        if (isset($this->request->post['product_related'])) {
            $products = $this->request->post['product_related'];
        } elseif (isset($this->request->get['product_id'])) {
            $products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
        } else {
            $products = array();
        }

        $this->data['product_related'] = array();

        foreach ($products as $id) {
            if (isset($this->request->get['product_id'])) {
                if ($this->request->get['product_id'] == $id) {
                    continue;
                }
            }

            $related_product = $this->model_catalog_product->getProduct($id);
            $related_product['image'] = $this->model_tool_image->resize($related_product['image'], 50, 50);

            $this->data['product_related'][] = $related_product;
        }

        if (isset($this->request->post['product_points'])) {
              $this->data['points'] = $this->request->post['product_points'];
        } elseif (!empty($product_info)) {
            $this->data['points'] = $product_info['points'];
        } else {
              $this->data['points'] = '';
        }

        if (isset($this->request->post['product_reward'])) {
            $this->data['product_reward'] = $this->request->post['product_reward'];
        } elseif (isset($this->request->get['product_id'])) {
            $this->data['product_reward'] = $this->model_catalog_product->getProductRewards($this->request->get['product_id']);
        } else {
            $this->data['product_reward'] = array();
        }

        if (!empty($this->error)) {
            $this->data['error'] = implode('<br />', $this->error);
        } else {
            $this->data['error'] = '';
        }

        $this->data['tax_settings'] = $this->url->link('settings/store/general', 'token=' . $this->session->data['token'], 'SSL');

        $this->document->addStyle('view/css/pages/product.css');
        $this->document->addStyle('view/css/pages/uploader.css');
        $this->document->addScript('view/js/pages/product_form.js');
        $this->document->addScript('view/js/pages/uploader.js');
        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');
        $this->document->addScript('view/js/jquery/jquery.autocomplete.js');

        $this->template = 'catalog/product_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        foreach ($this->request->post['product_description'] as $language_id => $value) {
            if ($language_id == $this->config->get('language_id') && ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255))) {
                $this->error['name'] = Language::getVar('SUMO_ERROR_NAME');
            }
        }

        // Check store vs category-id for consistency
        $category_present = false;

        if (isset($this->request->post['product_category']) && is_array($this->request->post['product_category'])) {
            foreach ($this->request->post['product_category'] as $k => $product_category) {
                if (empty($product_category) || mb_strlen($this->request->post['product_store'][$k]) == 0) {
                    // Remove category & store from list
                    unset($this->request->post['product_category'][$k]);
                    unset($this->request->post['product_store'][$k]);
                } else {
                    $category_present = true;
                }
            }
        }

        if (!$category_present) {
            $this->error['category'] = Language::getVar('SUMO_ERROR_NO_CATEGORY');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateCopy()
    {
        if (!$this->user->hasPermission('modify', 'catalog/product')) {
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

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_category_id'])) {
            $this->load->model('catalog/product');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_model'])) {
                $filter_model = $this->request->get['filter_model'];
            } else {
                $filter_model = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 20;
            }

            $data = array(
                'filter_name'  => $filter_name,
                'filter_model' => $filter_model,
                'start'        => 0,
                'limit'        => $limit
            );

            $results = $this->model_catalog_product->getProducts($data);

            foreach ($results as $result) {
                $json[] = array(
                    'id' => $result['product_id'],
                    'text'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')) . ' (' . $result['model'] . ')',
                );
            }
        }

        if (isset($this->request->get['asResults'])) {
            $this->response->setOutput(json_encode(array('results' => $json)));
        }
        else {
            $this->response->setOutput(json_encode($json));
        }
    }

    public function status()
    {
        $product_id = $this->request->post['product_id'];
        if (!$product_id) {
            return;
        }
        Database::query("
            UPDATE PREFIX_product
            SET status = " . (int) $this->request->post['status'] . "
            WHERE product_id = " . (int) $product_id
        );
        Cache::removeAll();
    }

    public function stock()
    {
        $stock_id = $this->request->post['stock_id'];
        if (!$stock_id) {
            return;
        }
        Database::query("
            UPDATE PREFIX_product
            SET quantity = " . (int) $this->request->post['quantity'] . "
            WHERE product_id = " . (int) $stock_id
        );
        Cache::removeAll();
    }

    public function optionStock()
    {
        $value_id = $this->request->post['value_id'];
        if (!$value_id) {
            return;
        }
        Database::query("UPDATE PREFIX_product_option_value SET quantity = " . (int)$this->request->post['quantity'] . " WHERE value_id = " . (int)$value_id);
        Cache::removeAll();
    }
}
