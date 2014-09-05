<?php
namespace Sumo;
class ControllerProductSearch extends Controller
{
    public function index()
    {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');


        if (isset($this->request->get['search'])) {
            $this->document->setTitle(Language::getVar('SUMO_NOUN_SEARCH_FOR') .  ' - ' . $this->request->get['search']);
        } else {
            $this->document->setTitle(Language::getVar('SUMO_NOUN_SEARCH_FOR'));
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

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

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_SEARCH_FOR'),
            'href'      => $this->url->link('product/search', $url),

        );

        if (isset($this->request->get['search'])) {
            $this->data['heading_title'] = Language::getVar('SUMO_NOUN_SEARCH_FOR') .  ' - ' . $this->request->get['search'];
        }
        else {
            $this->data['heading_title'] = Language::getVar('SUMO_NOUN_SEARCH_FOR');
        }

        $product_total = 0;
        $results = array();

        if (isset($this->request->get['search']) || isset($this->request->get['filter_tag'])) {
            $data = array();

            $data['filter_name'] = $data['filter_tag'] = '';
            if (isset($this->request->get['search'])) {
                $data['filter_name'] = $this->request->get['search'];
                $data['filter_tag'] = $this->request->get['search'];
            }

            $data['filter_description'] = '';
            if (isset($this->request->get['description'])) {
                $data['filter_description'] = $this->request->get['description'];
            }

            $data['filter_category_id'] = 0;
            if (isset($this->request->get['category_id'])) {
                $data['filter_category_id'] = $this->request->get['category_id'];
            }

            $data['filter_sub_category'] = '';
            if (isset($this->request->get['sub_category'])) {
                $data['filter_sub_category'] = $this->request->get['sub_category'];
            }

            $data['sort'] = 'p.sort_order';
            if (isset($this->request->get['sort'])) {
                $data['sort'] = $this->request->get['sort'];
            }

            $data['order'] = 'ASC';
            if (isset($this->request->get['order'])) {
                $data['order'] = $this->request->get['order'];
            }

            $page = 1;
            if (!empty($this->request->get['page'])) {
                $page = $this->request->get['page'];
            }

            $limit = 25;
            if (!empty($this->request->get['limit']) && $limit <= 150) {
                $limit = (int)$this->request->get['limit'];
            }

            $product_total = $this->model_catalog_product->getTotalProducts($data);
            $results = $this->model_catalog_product->getProducts($data);
        }

        $results['total'] = $product_total;
        $this->data['products'] = $results;

        $this->data['settings'] = $this->config->get('details_product_category_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $results['filter_category_id'] = 0;
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'categoryTree', 'data' => $results));
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimpleproduct/', array('type' => 'filter', 'data' => $results));
        }

        $this->template = 'product/search.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        // Contains results
        $bind = array();
        $data = array();
        if (isset($this->request->get['keyword'])) {
            // Parse all keywords to lowercase
            $keywords = strtolower($this->request->get['keyword']);

            // Perform search only if we have some keywords
            if (strlen($keywords) >= 2) {
                $cache = 'products.search.' . $keywords;
                $data = Cache::find($cache);
                if (!is_array($data) || !count($data)) {
                    $this->load->model('catalog/product');
                    $data = array();
                    $parts = explode(' ', $keywords );
                    $add = '';
                    // Generating search
                    $partcount = 1;
                    foreach ($parts as $part) {
                        $part = '%' . strtolower($part) . '%';

                        $add .= ' AND (LOWER(pd.name) LIKE :part_' . $partcount;
                        $bind['part_' . $partcount] = $part;
                        $partcount++;

                        $add .= ' OR LOWER(p.model) LIKE :part_' . $partcount;
                        $bind['part_' . $partcount] = $part;
                        $partcount++;

                        $add .= ' OR LOWER(p.model_2) LIKE :part_' . $partcount;
                        $bind['part_' . $partcount] = $part;
                        $partcount++;

                        $add .= ' OR LOWER(pd.tag) LIKE :part_' . $partcount;
                        $bind['part_' . $partcount] = $part;
                        $partcount++;

                        $add .= ' )';
                    }

                    $sql  = 'SELECT pd.product_id
                    FROM PREFIX_product_description AS pd
                    LEFT JOIN PREFIX_product AS p
                        ON p.product_id = pd.product_id
                    LEFT JOIN PREFIX_product_to_store AS p2s
                        ON p2s.product_id = pd.product_id

                    WHERE 1 = 1 ' . $add . '
                        AND p.status = 1
                        AND pd.language_id = ' . (int)$this->config->get('language_id'). '
                        AND p2s.store_id =  ' . (int)$this->config->get('store_id') . '
                    ORDER BY LOWER(tag) ASC, LOWER(pd.name) ASC, LOWER(p.model) ASC
                    LIMIT 15';

                    $data = Database::fetchAll($sql, $bind);
                    if ($data && count($data)) {
                        $basehref = 'product/product&keyword=' . $this->request->get['keyword'] . '&product_id=';
                        foreach ($data as $key => $list) {
                            $product = $this->model_catalog_product->getProduct($list['product_id']);
                            if ((!$product['stock_visible'] || ($product['stock_visible'] == 2 && !$this->config->get('display_stock_empty'))) && $product['quantity'] <= 0) {
                                unset($data[$key]);
                                continue;
                            }
                            $data[$key] = array(
                                'name' => htmlspecialchars_decode($product['name'], ENT_QUOTES),
                                'href' => $this->url->link('product/product', 'path=unknown&product_id=' . $product['product_id'] . '&keyword=' . $this->request->get['keyword'])
                            );
                        }
                    }
                    else {
                        exit('meh, failure');
                    }
                    Cache::set($cache, $data);
                }
            }
        }
        $this->response->setOutput(json_encode($data));
    }
}
