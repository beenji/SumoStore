<?php
namespace Sumo;
class ControllerModuleBestSeller extends Controller
{
    protected function index($setting)
    {
        $this->data['heading_title'] = Language::getVar('SUMO_PRODUCT_BESTSELLER');

        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        if ($this->registry->get('builder')) {
            $this->data['settings'] = Cache::find('builder');
        }
        $this->data['products'] = array();

        $results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);

        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
            } else {
                $image = false;
            }

            $swapimages = $this->model_catalog_product->getProductImages($result['product_id']);
            if ($swapimages) {
                $swapimage = $this->model_tool_image->resize($swapimages[0]['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'), 'h');
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

            if ($result['reviews']) {
                if ($result['reviews'] > 1) {
                    $reviews = Language::getVar('SUMO_PRODUCT_REVIEWS_PLURAL', $result['reviews']);
                }
                else {
                    $reviews = Language::getVar('SUMO_PRODUCT_REVIEWS_SINGULAR');
                }
            }
            else {
                $reviews = Language::getVar('SUMO_PRODUCT_REVIEWS_NONE');
            }

            $this->data['products'][] = array(
                'product_id' => $result['product_id'],
                'thumb'        => $image,
                'thumb_swap'   => $swapimage,
                'name'         => $result['name'],
                'price'        => $price,
                'special'      => $special,
                'rating'     => $rating,
                'reviews'    => $reviews,
                'href'         => $this->url->link('product/product', 'path=' . $result['category_id'] . '&product_id=' . $result['product_id']),
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bestseller.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/bestseller.tpl';
        } else {
            $this->template = 'default/template/module/bestseller.tpl';
        }

        if (count($this->data['products'])) {
            $this->render();
        }
    }
}
