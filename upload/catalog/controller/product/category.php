<?php
namespace Sumo;
class ControllerProductCategory extends Controller
{
    public function index()
    {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $filter = '';
        if (isset($this->request->get['filter'])) {
            $filter = $this->request->get['filter'];
        }

        $sort = 'p.sort_order';
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        }

        $order = 'ASC';
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        }

        $page = 1;
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }

        $limit = 25;
        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        if (isset($this->request->get['path'])) {
            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $path = '';
            $parts = explode('_', (string)$this->request->get['path']);
            $category_id = (int)array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = (int)$path_id;
                }
                else {
                    $path .= '_' . (int)$path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $this->data['breadcrumbs'][] = array(
                        'text'      => $category_info['name'],
                        'href'      => $this->url->link('product/category', 'path=' . $path . $url),
                        'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
                    );
                }
            }
        }
        else {
            $category_id = 0;
        }

        $category_info = $this->model_catalog_category->getCategory($category_id);

        if ($category_info) {
            $this->document->setTitle($category_info['name']);
            $this->document->setDescription($category_info['meta_description']);
            $this->document->setKeywords($category_info['meta_keyword']);

            $this->data['heading_title'] = $category_info['name'];

            // Set the last category breadcrumb
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
                'href'      => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url),
                'separator' => Language::getVar('SUMO_BREADCRUMBS_SEPARATOR')
            );

            if ($category_info['image']) {
                $this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('image_category_width'), $this->config->get('image_category_height'));
            }
            else {
                $this->data['thumb'] = '';
            }

            $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['compare'] = $this->url->link('product/compare');

            $url = '';

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['categories'] = array();

            $results = $this->model_catalog_category->getCategories($category_id);

            foreach ($results as $result) {
                $data = array(
                    'filter_category_id'  => $result['category_id'],
                    'filter_sub_category' => true
                );

                $product_total = $this->model_catalog_product->getTotalProducts($data);
                if ($result['image'] && !empty($result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('image_category_width'), $this->config->get('image_category_height'));
                }
                else {
                    $image = false;
                }
                $this->data['categories'][] = array(
                    'name'  => $result['name'] . ($this->config->get('product_count') ? ' (' . $product_total . ')' : ''),
                    'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
                    'thumb' => $image
                );
            }

            $this->data['products_data'] = array(
                'filter_category_id' => $category_id,
                'filter_filter'      => $filter,
                'sort'               => $sort,
                'order'              => $order,
                'start'              => ($page - 1) * $limit,
                'limit'              => $limit
            );


            $this->data['settings'] = $this->config->get('details_product_category_' . $this->config->get('template'));
            if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'categoryTree', 'data' => $this->data['products_data']));
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimpleproduct/', array('type' => 'filter', 'data' => $this->data['products_data']));
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'banner', 'location' => 'category', 'data' => $this->data['products_data']));
                $this->data['settings']['bottom'][] = $this->getChild('app/widgetsimpleproduct', array('type' => 'latest', 'limit' => 6, 'category_id' => $category_id));
            }

            $this->data['continue'] = $this->url->link('');

            $this->template = 'product/category.tpl';
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
}
