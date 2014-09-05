<?php
namespace Sumo;
class ControllerProductManufacturer extends Controller
{
    public function index()
    {
        $this->load->model('catalog/manufacturer');
        $this->load->model('tool/image');

        $this->document->setTitle(Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL'));
        $this->data['heading_title'] = Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL');
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL'),
            'href'      => $this->url->link('product/manufacturer'),

        );

        $this->data['manufacturers'] = array();
        $cache = 'product.manufacturer.brand.overview';
        $data = Cache::find($cache);
        if (!is_array($data) || !count($data)) {
            $results = $this->model_catalog_manufacturer->getManufacturers();
            foreach ($results as $result) {
                if (is_numeric(utf8_substr($result['name'], 0, 1))) {
                    $key = '0 - 9';
                }
                else {
                    $key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
                }

                if (!isset($this->data['manufacturers'][$key])) {
                    $data[$key]['name'] = $key;
                }

                $result['href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']);
                $data[$key]['manufacturer'][] = $result;
            }
            Cache::set($cache, $data);
        }
        $this->data['manufacturers'] = $data;

        $this->data['settings'] = $this->config->get('details_product_category_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'manufacturerTree'));
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'banner', 'location' => 'category'));
            //$this->data['settings']['bottom'][] = $this->getChild('app/widgetsimpleproduct', array('type' => 'latest', 'limit' => 6, 'manufacturer_id' => $manufacturer_id));
        }
        $this->template = 'product/manufacturer.tpl';

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function info()
    {
        $this->load->model('catalog/manufacturer');
        $this->load->model('tool/image');

        $manufacturer_id = 0;
        if (!empty($this->request->get['manufacturer_id'])) {
            $manufacturer_id = $this->request->get['manufacturer_id'];
        }

        $data = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
        if (!count($data)) {
            return $this->forward('product/manufucturer');
        }

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

        $title = Language::getVar('SUMO_NOUN_MANUFACTURER_SINGULAR') . ': ' . $data['name'];
        $this->document->setTitle($title);
        $this->data['heading_title'] = $title;
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL'),
            'href'      => $this->url->link('product/manufacturer'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => $title,
            'href'      => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_id)
        );

        $this->data['type'] = 'manufacturer';
        $this->data['products_data'] = array(
            'filter_manufacturer_id'    => $manufacturer_id,
            'type'                      => 'manufacturer',
            'sort'                      => $sort,
            'order'                     => $order,
            'start'                     => ($page - 1) * $limit,
            'limit'                     => $limit
        );


        $this->data['settings'] = $this->config->get('details_product_category_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'manufacturerTree', 'data' => $this->data['products_data']));
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimpleproduct/', array('type' => 'filter', 'data' => $this->data['products_data']));
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'banner', 'location' => 'category', 'data' => $this->data['products_data']));
            //$this->data['settings']['bottom'][] = $this->getChild('app/widgetsimpleproduct', array('type' => 'latest', 'limit' => 6, 'manufacturer_id' => $manufacturer_id));
        }

        $this->data['continue'] = $this->url->link('');

        $this->template = 'product/category.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
