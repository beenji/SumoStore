<?php
namespace Inventory;
use Sumo;
use App;
class ControllerInventory extends App\Controller
{
    private $error = array();
    private $currency_data = array();

    public function index()
    {
        $this->document->setTitle(Sumo\Language::getVar('SUMO_ADMIN_CATALOG_STOCK'));

        $this->load->model('catalog/product');

        $this->getList();
    }

    public function export()
    {
        $filter_keys = array(
            'name',
            'quantity_from',
            'quantity_to',
            'quantity',
            'category',
            'store'
        );

        $filter = array();

        foreach ($filter_keys as $filter_key) {
            $filter[$filter_key] = isset($this->request->get[$filter_key]) ? $this->request->get[$filter_key] : '';
        }

        // Get product list
        $this->load->appModel('Inventory');
        $products = $this->inventory_model_inventory->getProducts($filter);

        // Output header
        $s = ';';

        $csv_header = array('Model', 'Productnaam', 'Voorraad', 'Financieel Risico');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="inventory-' . date('d-m-Y') . '.csv"');
        echo '"' . implode('"' . $s . '"', $csv_header) . '"' . "\n";

        foreach ($products as $product) {
            $csv_line = array(
                !empty($product['model_2']) ? $product['model_2'] : $product['model'],
                $product['name'],
                $product['quantity'],
                ($product['quantity'] * $product['cost_price'])
            );

            echo '"' . implode('"' . $s . '"', $csv_line) . '"' . "\n";
        }
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Sumo\Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Sumo\Language::getVar('SUMO_ADMIN_CATALOG_STOCK'),
        ));

        $filter_keys = array(
            'name',
            'quantity_from',
            'quantity_to',
            'quantity',
            'category',
            'store'
        );

        $filter = array();
        $url = '';

        foreach ($filter_keys as $filter_key) {
            $filter[$filter_key] = isset($this->request->get[$filter_key]) ? $this->request->get[$filter_key] : '';

            if ($filter[$filter_key] != '') {
                $url .= '&' . $filter_key . '=' . $filter[$filter_key];
            }
        }

        // Limit?
        $limit = 25;
        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        }

        $page  = 1;
        $start = 0;
        if (isset($this->request->get['page']) && $this->request->get['page'] > 0) {
            $page  = (int)$this->request->get['page'];
            $start = ($page - 1) * $limit;
        }

        // We need this variable in all circumstances
        $this->data['products'] = array();

        // Get product list
        $this->load->appModel('Inventory');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $products = $this->inventory_model_inventory->getProducts(array_merge($filter, array('start' => $start, 'limit' => $limit)));

        $totalProducts = $this->inventory_model_inventory->getTotalProducts($filter);
        $totalProductsInView = $this->inventory_model_inventory->getTotalProductsInView($filter);

        foreach ($products as $k => $product) {
            $image = $this->model_tool_image->resize('no_image.jpg', 60, 60);
            if ($product['image'] && file_exists(DIR_IMAGE . $product['image'])) {
                $image = $this->model_tool_image->resize($product['image'], 60, 60);
            }

            $this->data['products'][] = array_merge($product, array(
                'product_id' => $product['product_id'],
                'stock_id'   => $product['stock_id'],
                'name'       => $product['name'],
                'model'      => !empty($product['model_2']) ? $product['model_2'] : $product['model'],
                'image'      => $image,
                'quantity'   => $product['quantity'],
                'price'      => $product['price'],
                'price_net'  => $product['price'] + (($product['price'] * $product['tax_percentage']) / 100),
                'value'      => $product['cost_price'],
                'options'    => $this->model_catalog_product->getProductOptions($product['product_id']),
                // Links
                'edit'       => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'])
            ));
        }

        // The quantity filter is something a bit different
        $filter_quantity = $filter_quantity_type = '';
        if (!empty($filter['quantity_from'])) {
            $filter_quantity = $filter['quantity_from'];
            $filter_quantity_type = 'quantity_from';
        }
        elseif (!empty($filter['quantity_to'])) {
            $filter_quantity = $filter['quantity_to'];
            $filter_quantity_type = 'quantity_to';
        }
        elseif (!empty($filter['quantity'])) {
            $filter_quantity = $filter['quantity'];
            $filter_quantity_type = 'quantity';
        }

        // Get some stats
        $this->data = array_merge($this->data, array(
            'filter_name'          => $filter['name'],
            'filter_category'      => $filter['category'],
            'filter_store'         => $filter['store'],
            'filter_quantity'      => $filter_quantity,
            'filter_quantity_type' => $filter_quantity_type,

            'token'                => $this->session->data['token'],
            'limit'                => $limit,
            'token'                => $this->session->data['token'],
            'total_products'       => $totalProducts,
            'total_value'          => Sumo\Formatter::currency($this->inventory_model_inventory->getTotalProductsValue($filter)),
            'total_price'          => Sumo\Formatter::currency($this->inventory_model_inventory->getTotalProductsPrice($filter)),

            'url'                  => $this->url->link('app/inventory', 'token=' . $this->session->data['token'] . $url),
            'reset'                => $this->url->link('app/inventory', 'token=' . $this->session->data['token']),
            'export'               => $this->url->link('app/inventory/export', 'token=' . $this->session->data['token'] . $url),
        ));

        // List some categories
        $categories = $this->inventory_model_inventory->getCategories();

        foreach ($categories as $store_id => $store_categories) {
            foreach ($store_categories as $k => $category) {
                for ($i = 0; $i < ($category['level'] * 4); $i++) {
                    $categories[$store_id][$k]['name'] = '&nbsp;' . $categories[$store_id][$k]['name'];
                }
            }
        }

        // List some stores
        $this->load->model('settings/stores');

        $stores = $this->model_settings_stores->getStores();

        // Set pagination
        $pagination = new Sumo\Pagination();
        $pagination->total = $totalProductsInView;
        $pagination->limit = $limit;
        $pagination->page  = $page;
        $pagination->url   = $this->url->link('app/inventory', 'token=' . $this->session->data['token'] . $url . '&page={page}&limit=' . $limit);

        $this->data['pagination']      = $pagination->renderAdmin();
        $this->data['stores']          = $stores;
        $this->data['categories']      = isset($categories[$filter['store']]) ? $categories[$filter['store']] : array();
        $this->data['json_categories'] = json_encode($categories);

        $this->document->addStyle('view/css/pages/product.css');
        $this->document->addScript('../apps/inventory/admin/view/js/inventory.js');

        $this->template = 'index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}

