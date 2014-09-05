<?php
class ControllerModuleLatest extends Controller 
{
    protected function index($setting) 
    {
        $this->language->load('module/latest');
        
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['button_cart'] = $this->language->get('button_cart');
                
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        
        if ($this->registry->get('builder')) {
            $this->data['settings'] = Cache::find('builder', 'home');
            $this->data['settings']['category'] = Cache::find('builder', 'category');
        }
        if (isset($this->data['settings']['latest_products_limit'])) {
            $setting['limit'] = $this->data['settings']['latest_products_limit'];
        }
        $this->data['products'] = array();
        
        $data = array(
            'sort'  => 'p.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => $setting['limit']
        );
        $tmp = array();
        if (isset($this->request->get['path'])) {
            $tmp = explode('_', $this->request->get['path']);
        }
        else if (isset($this->request->get['category_id'])) {
            $tmp = explode('_', $this->request->get['category_id']);
        }
        if (!count($tmp)) {
            
        }
        else if (count($tmp) == 1) {
            $data['filter_category_id'] = $tmp[0];
        }
        else {
            $data['filter_category_id'] = $tmp[0];
            $data['filter_sub_category'] = $tmp[1];
        }
        
        $results = $this->model_catalog_product->getProducts($data);
        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
            } else {
                $image = false;
            }
            $swapimages = $this->model_catalog_product->getProductImages($result['product_id']);
            if ($swapimages) {
                $swapimage = $this->model_tool_image->resize($swapimages[0]['image'], $setting['image_width'], $setting['image_height']);
            } else {
                $swapimage = false;
            }
                        
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $price = false;
            }
                    
            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $special = false;
            }
            
            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }
            
            if (!empty($result['parent_id'])) {
                $result['category_id'] .= '_' . $result['parent_id'];
            }
            
            $this->data['products'][] = array(
                'product_id'    => $result['product_id'],
                'thumb'         => $image,
                'thumb_swap'    => $swapimage,
                'name'          => $result['name'],
                'price'         => $price,
                'special'       => $special,
                'rating'        => $rating,
                'reviews'       => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
                'href'          => $this->url->link('product/product', 'path=' . $result['category_id'] . '&product_id=' . $result['product_id']),
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/latest.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/latest.tpl';
        } else {
            $this->template = 'default/template/module/latest.tpl';
        }

        $this->render();
    }
}
