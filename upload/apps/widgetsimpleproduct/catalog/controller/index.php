<?php
namespace Widgetsimpleproduct;
use App;
use Sumo;
class ControllerWidgetsimpleproduct extends App\Controller
{
    public function index($input = array())
    {
        if (!isset($input['type']) || !method_exists($this, $input['type'])) {
            exit($input['type'] . ' is not supported');
            $input['type'] = 'category';
        }
        return $this->$input['type']($input);
    }

    public function latest($input)
    {
        $this->load->appModel('products');
        $this->data['input'] = $input;

        $this->data['products'] = $this->widgetsimpleproduct_model_products->getLatest($input);
        $this->template = 'product_list.tpl';
        return $this->output = $this->render();
    }

    public function manufacturers($input)
    {
        $this->load->appModel('products');
        $this->data['input'] = $input;

        $this->data['manufacturers'] = $this->widgetsimpleproduct_model_products->getManufacturers();
        $this->template = 'manufacturer_list.tpl';
        return $this->output = $this->render();
    }

    public function category($input)
    {
        $this->load->appModel('products');
        $this->getSortAndLimits();
        if (!empty($input['filter_type'])) {
            $input['type'] = $input['filter_type'];
        }
        $this->data['input'] = $input;

        $products = $this->widgetsimpleproduct_model_products->getProducts($input);

        if (!empty($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }
        else {
            $page = 1;
        }

        $url = '';
        if (isset($this->request->get['filter'])) {
            if (is_array($this->request->get['filter'])) {
                $tmp = '';
                foreach ($this->request->get['filter'] as $key => $value) {
                    $tmp .= '&filter[' . $key . ']=' . $value;
                }
                $url .= $tmp;
            }
            else {
                $url .= '&filter=' . $this->request->get['filter'];
            }
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Sumo\Pagination();
        $pagination->total = $products['total'];
        $pagination->page = $page;
        $pagination->limit = !empty($input['data']['limit']) ? $input['data']['limit'] : 25;
        if (isset($this->request->get['path'])) {
            $pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');
        }
        else {
            $pagination->url = $this->url->link('product/' . $input['filter_type'] . '/info', $input['filter_type'] . '_id=' . $this->request->get[$input['filter_type'] . '_id'] . $url . '&page={page}');
        }

        $this->data['pagination'] = $pagination->render();
        unset($products['total']);
        $this->data['products'] = $products;

        $this->template = 'category_list.tpl';
        $this->output = $this->render();
    }

    public function search($products)
    {
        $this->getSortAndLimits(true);

        if (!empty($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }
        else {
            $page = 1;
        }

        $url = '';
        if (isset($this->request->get['search'])) {
            $url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['tag'])) {
            $url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
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

        $pagination = new Sumo\Pagination();
        $pagination->total = $products['data']['total'];
        $pagination->page = $page;
        $pagination->limit = !empty($input['data']['limit']) ? $input['data']['limit'] : 25;
        $pagination->url = $this->url->link('product/search', $url . '&page={page}');

        $this->data['pagination'] = $pagination->render();
        unset($products['data']['total']);
        $this->data['products'] = $products['data'];

        $this->template = 'category_list.tpl';
        $this->output = $this->render();
    }

    public function filter($input)
    {
        $this->load->appModel('products');
        $url = '';
        if (isset($this->request->get['filter'])) {
            if (is_array($this->request->get['filter'])) {
                $tmp = '';
                foreach ($this->request->get['filter'] as $key => $value) {
                    $tmp .= '&filter[' . $key . ']=' . $value;
                }
                $url .= $tmp;
            }
            else {
                $url .= '&filter=' . $this->request->get['filter'];
            }
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $filters = $this->widgetsimpleproduct_model_products->getFilters($input);
        if (is_array($filters) && count($filters)) {
            foreach ($filters as $list) {
                foreach ($list['filters'] as $k => $filter) {
                    if (isset($filter['active'])) {
                        $filter['url'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . str_replace('filter[' . $filter['attribute_id'] . ']=' . $filter['name'] . '&', '', $url));
                    }
                    else {
                        $filter['url'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&filter[' . $filter['attribute_id'] . ']=' . $filter['name']);
                    }
                    $list['filters'][$k] = $filter;
                }
                $this->data['filters'][] = $list;
            }
        }


        $this->template = 'category_filters.tpl';
        $this->output = $this->render();
    }

    public function related($input)
    {
        $this->load->appModel('products');
        $products = $this->widgetsimpleproduct_model_products->getRelated($input);
        $this->data['products'] = $products;
        $this->data['input'] = $input;

        $this->template = 'product_list.tpl';
        $this->output = $this->render();
    }

    private function getSortAndLimits($search = false)
    {
        $url = '';

        if (!$search) {
            if (isset($this->request->get['filter'])) {
                if (is_array($this->request->get['filter'])) {
                    foreach ($this->request->get['filter'] as $key => $value) {
                        $url .= '&filter[' . $key . ']=' . $value;
                    }
                }
                else {
                    $url .= '&filter=' . $this->request->get['filter'];
                }
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            if (isset($this->request->get['sort'])) {
                //$url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                //$url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['path'])) {
                $base = $this->url->link('product/category', 'path=' . $this->request->get['path']);
            }
            else if (isset($this->request->get['manufacturer_id'])) {
                $base = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id']);
            }
        }
        else {
            if (isset($this->request->get['search'])) {
                $url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
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

            $base = $this->url->link('product/search');
        }

        $this->data['sorts'] = array();
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_DEFAULT'),
            'value' => 'p.sort_order-ASC',
            'href'  => $base . '&sort=p.sort_order&order=ASC' . $url
        );
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_NAME_ASC'),
            'value' => 'pd.name-ASC',
            'href'  => $base . '&sort=pd.name&order=ASC' . $url
        );
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_NAME_DESC'),
            'value' => 'pd.name-DESC',
            'href'  => $base . '&sort=pd.name&order=DESC' . $url
        );
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_PRICE_ASC'),
            'value' => 'p.price-ASC',
            'href'  => $base . '&sort=p.price&order=ASC' . $url
        );
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_PRICE_DESC'),
            'value' => 'p.price-DESC',
            'href'  => $base . '&sort=p.price&order=DESC' . $url
        );
        if ($this->config->get('config_review_status')) {
            $this->data['sorts'][] = array(
                'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_RATING_DESC'),
                'value' => 'rating-DESC',
                'href'  => $base . '&sort=rating&order=DESC' . $url
            );
            $this->data['sorts'][] = array(
                'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_RATING_ASC'),
                'value' => 'rating-ASC',
                'href'  => $base . '&sort=rating&order=ASC' . $url
            );
        }
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_MODEL_ASC'),
            'value' => 'p.model-ASC',
            'href'  => $base . '&sort=p.model&order=ASC' . $url
        );
        $this->data['sorts'][] = array(
            'text'  => Sumo\Language::getVar('SUMO_CATEGORY_SORT_MODEL_DESC'),
            'value' => 'p.model-DESC',
            'href'  => $base . '&sort=p.model&order=DESC' . $url
        );

        $this->data['limits'] = array();
        $limits = array_unique(array($this->config->get('catalog_display_limit'), 12, 24, 36, 48, 96));
        sort($limits);
        foreach($limits as $limit) {
            if (empty($limit)) {
                continue;
            }
            $this->data['limits'][] = array(
                'text'  => Sumo\Language::getVar('SUMO_NOUN_DISPLAY_AMOUNT', $limit),
                'value' => $limit,
                'href'  => $base . $url . '&limit=' . $limit
            );
        }
    }
}
