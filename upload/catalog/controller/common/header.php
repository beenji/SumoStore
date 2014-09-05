<?php
namespace Sumo;
class ControllerCommonHeader extends Controller
{
    protected function index()
    {

        $this->data['title'] = $this->document->getTitle();
        if (stristr($this->data['title'], $this->config->get('title')) == false) {
            $this->data['title'] .= ' - '  . $this->config->get('title');
        }

        $this->data['base'] = $this->url->link();
        $this->data['description'] = $this->document->getDescription();
        $this->data['keywords'] = $this->document->getKeywords();
        $this->data['links'] = $this->document->getLinks();

        foreach ($this->document->getStyles() as $list) {
            $this->data['styles'][] = $list;
        }

        foreach ($this->document->getScripts() as $script) {
            $this->data['scripts'][] = $script;
        }

        $this->data['name'] = $this->config->get('name');

        $this->data['icon'] = '';
        if ($this->config->get('icon') && file_exists(DIR_IMAGE . $this->config->get('icon'))) {
            $this->data['icon'] = 'image/' . $this->config->get('icon');
        }

        $this->data['logo'] = '';
        if ($this->config->get('logo') && file_exists(DIR_IMAGE . $this->config->get('logo'))) {
            $this->data['logo'] = 'image/' . $this->config->get('logo');
        }

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/manufacturer');
        $this->load->model('tool/image');

        $categories = Cache::find('categories_rendered' . $this->config->get('store_id'));

        if (!is_array($categories) || !count($categories) || empty($categories)) {
            $categories = $this->model_catalog_category->getCategories(0);
            foreach ($categories as $category) {
                $children_data = array();
                $children = $this->model_catalog_category->getCategories($category['category_id']);
                foreach ($children as $child) {
                    $data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );
                    // Level 2
                    $children_level_2 = $this->model_catalog_category->getCategories($child['category_id']);
                    $children_data_level_2 = array();
                    foreach ($children_level_2 as $child_level_2) {
                            $data_level_2 = array(
                                    'filter_category_id'  => $child_level_2['category_id'],
                                    'filter_sub_category' => true
                            );
                            $product_total_level_2 = '';
                            if ($this->config->get('config_product_count')) {
                                    $product_total_level_2 = ' (' . $this->model_catalog_product->getTotalProducts($data_level_2) . ')';
                            }

                            $children_data_level_2[] = array(
                                    'name'  =>  $child_level_2['name'],
                                    'url'  => $this->url->link('product/category', 'path=' . $child['category_id'] . '_' . $child_level_2['category_id']),
                                    'id' => $category['category_id']. '_' . $child['category_id']. '_' . $child_level_2['category_id']
                            );
                    }
                    $children_data[] = array(
                            'name'  => $child['name'],
                            'url'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
                            'id' => $category['category_id']. '_' . $child['category_id'],
                            'children_level_2' => $children_data_level_2,
                    );
                }
                // Level 1
                $this->load->model('tool/image');
                $image = empty($category['image']) ? 'no_image.jpg' : $category['image'];
                $thumb = $this->model_tool_image->resize($image, 100, 100);
                $this->data['categories'][] = array(
                    'name'      => $category['name'],
                    'children'  => $children_data,
                    'column'    => $category['column'] ? $category['column'] : 1,
                    'thumb'     => $thumb,
                    'url'       => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
            Cache::set('categories_rendered' . $this->config->get('store_id'), $this->data['categories']);
        }
        else {
            $this->data['categories'] = $categories;
        }

        $this->data['logged'] = $this->customer->isLogged();
        $this->template = 'common/header.tpl';
        $this->render();
    }
}
