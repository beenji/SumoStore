<?php
namespace Sumo;
class ControllerAccountWishList extends Controller
{
    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/wishlist', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        if (!isset($this->session->data['wishlist'])) {
            $this->session->data['wishlist'] = array();
        }

        if (isset($this->request->get['remove']) && is_numeric($this->request->get['remove'])) {
            $key = array_search($this->request->get['remove'], $this->session->data['wishlist']);

            if ($key !== false) {
                unset($this->session->data['wishlist'][$key]);
                $productInfo = $this->model_catalog_product->getProduct((int) $this->request->get['remove']);
                $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_WISHLIST_PRODUCT_REMOVED', array($this->url->link('product/product', 'product_id=' . $this->request->get['remove']), $productInfo['name'], $this->url->link('account/wishlist')));
            }
            else {
                $this->session->data['success'] = Language::getVar('SUMO_ACCOUNT_WISHLIST_PRODUCT_REMOVED');
            }
            $this->redirect($this->url->link('account/wishlist'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_WISHLIST_TITLE'));

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home')
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),

        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_WISHLIST_TITLE'),
            'href'      => $this->url->link('account/wishlist'),

        );

        $this->data['success'] = '';
        
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['products'] = array();

        foreach ($this->session->data['wishlist'] as $key => $product_id) {
            $productInfo = $this->model_catalog_product->getProduct($product_id);

            if ($productInfo) {
                if ($productInfo['image']) {
                    $image = $this->model_tool_image->resize($productInfo['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
                }
                else {
                    $image = false;
                }

                if ($productInfo['quantity'] <= 0) {
                    $stock = $productInfo['stock_status'];
                }
                elseif ($this->config->get('config_stock_display')) {
                    $stock = $productInfo['quantity'];
                }
                else {
                    $stock = Language::getVar('SUMO_PRODUCT_IN_STOCK');
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')));
                }
                else {
                    $price = false;
                }

                if ((float)$productInfo['special']) {
                    $special = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = false;
                }

                $this->data['products'][] = array(
                    'product_id' => $productInfo['product_id'],
                    'thumb'      => $image,
                    'name'       => $productInfo['name'],
                    'model'      => $productInfo['model'],
                    'stock'      => $stock,
                    'price'      => $price,
                    'special'    => $special,
                    'view'       => $this->url->link('product/product', 'product_id=' . $productInfo['product_id']),
                    'remove'     => $this->url->link('account/wishlist', 'remove=' . $productInfo['product_id'])
                );
            }
            else {
                unset($this->session->data['wishlist'][$key]);
            }
        }

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/wishlist.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function add()
    {
        $json = array();

        if (!isset($this->session->data['wishlist'])) {
            $this->session->data['wishlist'] = array();
        }

        $product_id = 0;
        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        }

        $this->load->model('catalog/product');

        $productInfo = $this->model_catalog_product->getProduct($product_id);

        if ($productInfo) {
            if (!in_array($this->request->post['product_id'], $this->session->data['wishlist'])) {
                $this->session->data['wishlist'][] = $this->request->post['product_id'];
            }

            if ($this->customer->isLogged()) {
                $json['success'] = Language::getVar('SUMO_ACCOUNT_WISHLIST_ADDED', array($this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $productInfo['name'], $this->url->link('account/wishlist'), strtolower(Language::getVar('SUMO_NOUN_ACCOUNT_WISHLIST'))));
            } else {
                $json['success'] = Language::getVar('SUMO_ACCOUNT_WISHLIST_LOGIN_REQUIRED', array($this->url->link('account/login', '', 'SSL'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $productInfo['name'], $this->url->link('account/wishlist'), strtolower(Language::getVar('SUMO_NOUN_ACCOUNT_WISHLIST'))));
            }

            $json['total'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
        }

        $this->response->setOutput(json_encode($json));
    }
}
