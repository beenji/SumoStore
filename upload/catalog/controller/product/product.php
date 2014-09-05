<?php
namespace Sumo;
class ControllerProductProduct extends Controller
{
    private $error = array();

    public function index()
    {
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        if (isset($this->request->get['path'])) {
            $path = '';
            $parts = explode('_', (string)$this->request->get['path']);
            $category_id = (int)array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = $path_id;
                }
                else {
                    $path .= '_' . $path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $this->data['breadcrumbs'][] = array(
                        'text'      => $category_info['name'],
                        'href'      => $this->url->link('product/category', 'path=' . $path),
                        'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
                    );
                }
            }

            // Set the last category breadcrumb
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
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

                if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
                }

                $this->data['breadcrumbs'][] = array(
                    'text'      => $category_info['name'],
                    'href'      => $this->url->link('product/category', 'path=' . $this->request->get['path']),
                    'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
                );
            }
        }

        if (isset($this->request->get['manufacturer_id'])) {
            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL'),
                'href'      => $this->url->link('product/manufacturer'),
                'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
            );

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

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

            if ($manufacturer_info) {
                $this->data['breadcrumbs'][] = array(
                    'text'      => $manufacturer_info['name'],
                    'href'      => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url),
                    'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
                );
            }
        }

        if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
            $url = '';

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text'      => Language::getVar('SUMO_NOUN_SEARCH_PLURAL'),
                'href'      => $this->url->link('product/search', $url),
                'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
            );
        }

        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        }
        else {
            $product_id = 0;
        }


        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {

            $this->data['product_info'] = $product_info;

            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
                $path = '&path=' . $this->request->get['path'];
            }
            else {
                $url .= '&path=unknown';
                $path = '&path=' . $product_info['category_id'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
                $path = '&path=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text'      => $product_info['name'],
                'href'      => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']),
                'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
            );

            $this->document->setTitle($product_info['name']);
            $this->document->setDescription(!empty($product_info['meta_description']) ? $product_info['meta_description'] : substr(htmlentities(strip_tags(html_entity_decode($product_info['description']))), 0, 150));
            if (!empty($product_info['meta_keyword'])) {
                $this->document->setKeywords($product_info['meta_keyword']);
            }
            $this->document->addLink($this->url->link('product/product', $path . '&product_id=' . $this->request->get['product_id']), 'canonical');

            $this->data['heading_title'] = $product_info['name'];
            $this->data['product_id'] = $this->request->get['product_id'];
            $this->data['manufacturer'] = $product_info['manufacturer'];
            $this->data['manufacturer_link'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $this->data['model'] = $product_info['model'];
            $this->data['points'] = $product_info['points'];

            if ($product_info['quantity'] <= 0) {
                $this->data['stock'] = $product_info['stock_status'];
            }
            elseif ($this->config->get('stock_display') || $product_info['stock_amount_visible'] == 1) {
                $this->data['stock'] = $product_info['quantity'];
            }
            else {
                $this->data['stock'] = Language::getVar('SUMO_PRODUCT_IN_STOCK');
            }


            if ($product_info['image']) {
                $this->data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('image_popup_width'), $this->config->get('image_popup_height'));
            }
            else {
                $this->data['popup'] = $this->model_tool_image->resize('no_image.jpg', 150, 150);
            }
            if ($product_info['image']) {
                $this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('image_thumb_width'), $this->config->get('image_thumb_height'));
                $this->data['additional'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('image_additional_width'), $this->config->get('image_additional_height'));
            }
            else {
                $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 150, 150);
                $this->data['additional'] = $this->model_tool_image->resize('no_image.jpg', 150, 150);
            }

            $this->data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            foreach ($results as $result) {
                if (empty($result['image'])) {
                    $result['image'] = 'no_image.jpg';
                }
                $this->data['images'][] = array(
                    'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('image_popup_width'), $this->config->get('image_popup_height')),
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('image_thumb_width'), $this->config->get('image_thumb_height')),
                    'additional' => $this->model_tool_image->resize($result['image'], $this->config->get('image_additional_width'), $this->config->get('image_additional_height'))
                );
            }

            $this->data['price'] = $this->data['price_raw'] = false;
            if (($this->config->get('customer_price') && $this->customer->isLogged()) || !$this->config->get('customer_price')) {
                if ($this->config->get('tax_enabled')) {
                    $this->data['price'] = Formatter::currency($product_info['price'] + ($product_info['price'] / 100 * $product_info['tax_percentage']));
                }
                else {
                    $this->data['price'] = Formatter::currency($product_info['price']);
                }
                $this->data['price_raw'] = $product_info['price'];
            }

            if ((float)$product_info['special']) {
                $this->data['percent_savings'] = round((($product_info['price'] - $product_info['special']) / $product_info['price'] * 100));
                if ($this->config->get('tax_enabled')) {
                    $this->data['special'] = Formatter::currency($product_info['special'] + ($product_info['special'] / 100 * $product_info['tax_percentage']));
                }
                else {
                    $this->data['special'] = Formatter::currency($product_info['special']);
                }
                $this->data['price_raw'] = $product_info['special'];
            }

            $this->data['tax'] = $this->data['tax_raw'] = false;
            if ($this->config->get('tax_display')) {
                $price = $product_info['special'] ? $product_info['special'] : $product_info['price'];
                $this->data['tax'] = Formatter::currency($price);// + ($price / 100 * $product_info['tax_percentage']));
                $this->data['tax_raw'] = $price;
            }

            $discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

            $this->data['discounts'] = array();

            foreach ($discounts as $discount) {
                $percent_savings = round((($product_info['price'] - $discount['price']) / $product_info['price'] * 100));
                if ($this->config->get('tax_enabled')) {
                    $price = Formatter::currency(round($discount['price'] + $discount['price'] / 100 * $product_info['tax_percentage']));
                }
                else {
                    $price = Formatter::currency($discount['price']);
                }
                $this->data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'percent_savings' => $percent_savings,
                    'price'    => $price
                );
            }

            $this->data['options'] = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);

            $this->data['minimum'] = 1;
            if ($product_info['minimum']) {
                $this->data['minimum'] = $product_info['minimum'];
            }

            $this->data['review_status'] = $this->config->get('review_status');
            $this->data['rating'] = !empty($product_info['rating']) ? $product_info['rating'] : 0;
            $this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['attributes'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

            $this->model_catalog_product->updateViewed($this->request->get['product_id']);

            $this->template = 'product/product.tpl';
            $this->children = array(
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        }
        else {
            return $this->forward('error/not_found');
        }
    }

    public function review()
    {
        $this->load->model('catalog/review');

        $this->data['text_no_reviews'] = Language::getVar('SUMO_PRODUCT_NO_REVIEWS');

        $page = 1;
        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        }

        $this->data['reviews'] = array();

        $review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

        $results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

        foreach ($results as $result) {
            $result['text'] = strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8'));
            $result['text'] = str_replace(array("\r", "\n", PHP_EOL), '<br />', $result['text']);

            $this->data['reviews'][] = array(
                'author'     => $result['author'],
                'text'       => $result['text'],
                'rating'     => (int)$result['rating'],
                'date_added' => date('d-m-Y', strtotime($result['date_added']))
            );
        }

        $pagination = new Pagination();
        $pagination->total = $review_total;
        $pagination->page = $page;
        $pagination->limit = 5;

        $pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

        $this->data['pagination'] = $pagination->render();

        $this->template = 'product/review.tpl';
        $this->response->setOutput($this->render());
    }

    public function write()
    {
        $this->load->model('catalog/review');

        $json = array();
        if (!$this->customer->isLogged()) {
            $json['error'] = Language::getVar('SUMO_ACCOUNT_REQUIRED');
        }
        else
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            parse_str(str_replace('&amp;', '&', $this->request->post['data']), $data);
            if ((utf8_strlen($data['name']) < 3) || (utf8_strlen($data['name']) > 25)) {
                $json['error'] = Language::getVar('SUMO_ERROR_NAME');
            }

            if ((utf8_strlen($data['text']) < 25) || (utf8_strlen($data['text']) > 1000)) {
                $json['error'] = Language::getVar('SUMO_NOUN_ERROR_MESSAGE');
            }

            if (empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                $json['error'] = Language::getVar('SUMO_ERROR_INVALID_RATING');
            }

            if (!isset($json['error'])) {
                $result = $this->model_catalog_review->addReview($this->request->get['product_id'], $data);
                if ($result) {
                    $json['success'] = Language::getVar('SUMO_PRODUCT_REVIEW_ADDED');
                }
                else {
                    $json['error'] = Language::getVar('SUMO_PRODUCT_REVIEW_ERROR');
                }
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function captcha()
    {
        $this->load->library('captcha');

        $captcha = new Captcha();

        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

    public function upload()
    {
        $this->language->load('product/product');

        $json = array();
        if (!$this->customer->isLogged()) {
            $json['error'] = Language::getVar('SUMO_NOUN_RETURNING_CUSTOMER_DESCRIPTION', $this->url->link('account/login', '', 'SSL'));
        }
        else
        if (!empty($this->request->files['file']['name'])) {
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                //$json['error'] = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('file_extension_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
                $json['error'] = Language::getVar('SUMO_ERROR_INVALID_FILE');
            }

            // Allowed file mime types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('file_mime_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array($this->request->files['file']['type'], $allowed)) {
                $json['error'] = Language::getVar('SUMO_ERROR_INVALID_FILE');
            }

            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
            }
        } else {
            $json['error'] = Language::getVar('SUMO_ERROR_UPLOAD');
        }

        if (!$json && is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
            $file = basename($filename) . '.' . md5(mt_rand());

            // Hide the uploaded file name so people can not link to it directly.
            $json['file'] = $this->encryption->encrypt($file);

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);

            $json['success'] = Language::getVar('SUMO_FILE_UPLOADED_SUCCESFULLY');
        }

        $this->response->setOutput(json_encode($json));
    }
}
