<?php
namespace Sumo;
class ControllerProductCompare extends Controller
{
    public function index()
    {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        if (!isset($this->session->data['compare'])) {
            $this->session->data['compare'] = array();
        }

        if (isset($this->request->get['remove'])) {
            $key = array_search($this->request->get['remove'], $this->session->data['compare']);
            if ($key !== false) {
                unset($this->session->data['compare'][$key]);
            }
            $this->redirect($this->url->link('product/compare'));
        }

        $this->document->setTitle(Language::getVar('SUMO_PRODUCT_COMPARE', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0)));
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home')
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_PRODUCT_COMPARE', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0)),
        );

        $this->data['heading_title'] = Language::getVar('SUMO_PRODUCT_COMPARE', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        else {
            $this->data['success'] = '';
        }

        $this->data['review_status'] = $this->config->get('config_review_status');
        $this->data['products'] = array();
        $this->data['attribute_groups'] = array();

        foreach ($this->session->data['compare'] as $key => $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                if ($product_info['image']) {
                    $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_compare_width'), $this->config->get('config_image_compare_height'));
                }
                else {
                    $image = false;
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                }
                else {
                    $price = false;
                }

                if ((float)$product_info['special']) {
                    $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                }
                else {
                    $special = false;
                }

                if ($product_info['quantity'] <= 0) {
                    $availability = $product_info['stock_status'];
                }
                elseif ($this->config->get('config_stock_display') || $product_info['stock_amount_visible'] == 1) {
                    $availability = $product_info['quantity'];
                }
                else {
                    $availability = Language::getVar('SUMO_PRODUCT_IN_STOCK');
                }

                $attribute_data = array();

                $attribute_groups = $this->model_catalog_product->getProductAttributes($product_id);

                foreach ($attribute_groups as $attribute_group) {
                    foreach ($attribute_group['attribute'] as $attribute) {
                        $attribute_data[$attribute['attribute_id']] = $attribute['text'];
                    }
                }
                if ($product_info['reviews']) {
                    if ($product_info['reviews'] > 1) {
                        $reviews = Language::getVar('SUMO_PRODUCT_REVIEWS_PLURAL', $product_info['reviews']);
                    }
                    else {
                        $reviews = Language::getVar('SUMO_PRODUCT_REVIEWS_SINGULAR');
                    }
                }
                else {
                    $reviews = Language::getVar('SUMO_PRODUCT_REVIEWS_NONE');
                }
                $this->data['products'][$product_id] = array(
                    'product_id'   => $product_info['product_id'],
                    'name'         => $product_info['name'],
                    'thumb'        => $image,
                    'price'        => $price,
                    'special'      => $special,
                    'description'  => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
                    'model'        => $product_info['model_2'],
                    'manufacturer' => $product_info['manufacturer'],
                    'availability' => $availability,
                    'rating'       => (int)$product_info['rating'],
                    'reviews'      => $reviews,
                    'weight'       => $this->weight->format($product_info['weight'], $product_info['weight_class_id']),
                    'length'       => $this->length->format($product_info['length'], $product_info['length_class_id']),
                    'width'        => $this->length->format($product_info['width'], $product_info['length_class_id']),
                    'height'       => $this->length->format($product_info['height'], $product_info['length_class_id']),
                    'attribute'    => $attribute_data,
                    'order'        => $this->url->link('product/product', 'path=' . $product_info['category_id'] . '&product_id=' . $product_id),
                    'remove'       => $this->url->link('product/compare', 'remove=' . $product_id)
                );

                foreach ($attribute_groups as $attribute_group) {
                    $this->data['attribute_groups'][$attribute_group['attribute_group_id']]['name'] = $attribute_group['name'];

                    foreach ($attribute_group['attribute'] as $attribute) {
                        $this->data['attribute_groups'][$attribute_group['attribute_group_id']]['attribute'][$attribute['attribute_id']]['name'] = $attribute['name'];
                    }
                }
            } else {
                unset($this->session->data['compare'][$key]);
            }
        }

        $this->data['settings'] = $this->config->get('details_product_category_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'categoryTree', 'data' => array()));
        }

        $this->template = 'product/compare.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function add()
    {
        $json = array();

        if (!isset($this->session->data['compare'])) {
            $this->session->data['compare'] = array();
        }

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        }
        else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (!in_array($this->request->post['product_id'], $this->session->data['compare'])) {
                if (count($this->session->data['compare']) >= 4) {
                    array_shift($this->session->data['compare']);
                }

                $this->session->data['compare'][] = $this->request->post['product_id'];
            }

            $json['success'] = Language::getVar('SUMO_PRODUCT_COMPARE_ADD', array($this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('product/compare')));
            $json['total'] = Language::getVar('SUMO_PRODUCT_COMPARE', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
        }

        $this->response->setOutput(json_encode($json));
    }
}
