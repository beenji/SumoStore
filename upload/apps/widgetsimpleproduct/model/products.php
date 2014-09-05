<?php
namespace Widgetsimpleproduct;
use App;
use Sumo;

class ModelProducts extends App\Model
{
    public function getProducts($data)
    {
        $result = $search = array();
        if (isset($data['path'])) {
            $categories = explode('_', $data['path']);
            $category_id = end($categories);
        }

        if (!empty($data['data']['filter_category_id'])) {
            $category_id = $data['data']['filter_category_id'];
        }

        if (empty($data['data']['start'])) {
            $data['data']['start'] = 0;
        }

        if (empty($data['data']['limit'])) {
            $data['data']['limit'] = 15;
        }

        if (empty($data['data']['sort'])) {
            $data['data']['sort'] = 'p.sort_order';
        }

        if (empty($data['data']['order']) || !in_array($data['data']['order'], array('ASC', 'DESC'))) {
            $data['data']['order'] = 'ASC';
        }

        $cache  = 'wsp_plist_product_category_' . md5(json_encode($data));
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && count($result)) {
            Sumo\Logger::info('Got products from cache: ');
            return $result;
        }

        if ($data['type'] == 'category') {
            $sql = 'SELECT p.product_id, COUNT(*) AS total
            FROM PREFIX_product AS p';
            if ($data['data']['sort'] == 'pd.name') {
                $sql .= ' LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)';
            }

            if (!empty($data['data']['filter_filter'])) {
                $sql .= ' LEFT JOIN PREFIX_product_attribute AS pa ON pa.product_id = p.product_id ';
            }

            $sql .= '
            LEFT JOIN PREFIX_product_to_category AS ptc
                ON ptc.product_id = p.product_id
            WHERE ptc.category_id = :category_id
                AND p.status = 1 ';

            if (!empty($data['data']['filter_filter'])) {
                $filter = array();
                if (is_array($data['data']['filter_filter'])) {
                    foreach ($data['data']['filter_filter'] as $id => $unused) {
                        if (!is_numeric($id) || empty($id)) { continue; }
                        $filter[] = $id;
                    }
                }
                else {
                    if (is_numeric($data['data']['filter_filter']) && !empty($data['data']['filter_filter'])) {
                        $filter[] = $data['data']['filter_filter'];
                    }
                }

                if (count($filter)) {
                    $sql .= ' AND pa.attribute_id IN (' . implode(',', $filter) . ') ';
                }
            }

            $sql .= '
            GROUP BY p.product_id ';
            switch($data['data']['sort']) {
                case 'pd.name':
                    $sql .= 'ORDER BY pd.name ' . $data['data']['order'];
                    break;

                case 'p.price':
                    $sql .= 'ORDER BY p.price ' . $data['data']['order'];
                    break;

                case 'p.model':
                    $sql .= 'ORDER BY p.model_2 ' . $data['data']['order'];
                    break;

                default:
                    $sql .= 'ORDER BY p.sort_order ' . $data['data']['order'];
                    break;
            }
            $values = array('category_id' => $category_id);
        }

        if ($data['type'] == 'category_path') {
            $categoriesData = $this->fetchAll("SELECT category_id FROM PREFIX_category_path WHERE path_id = :id", array('id' => $category_id));
            $categories = array();
            foreach ($categoriesData as $item) {
                $categories[] = $item['category_id'];
            }

            $sql = 'SELECT p.product_id, COUNT(*) AS total
            FROM PREFIX_product AS p';
            if ($data['data']['sort'] == 'pd.name') {
                $sql .= ' LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)';
            }

            if (!empty($data['data']['filter_filter'])) {
                $sql .= ' LEFT JOIN PREFIX_product_attribute AS pa ON pa.product_id = p.product_id ';
            }

            $sql .= '
            LEFT JOIN PREFIX_product_to_category AS ptc
                ON ptc.product_id = p.product_id
            WHERE ';
            if (count($categories)) {
                $sql .= 'ptc.category_id IN(' . implode(',', $categories) . ')
                AND p.status = 1 ';
            }
            else {
                $sql .= 'p.status = 1 ';
            }

            if (!empty($data['data']['filter_filter'])) {
                $filter = array();
                if (is_array($data['data']['filter_filter'])) {
                    foreach ($data['data']['filter_filter'] as $id => $unused) {
                        if (!is_numeric($id) || empty($id)) { continue; }
                        $filter[] = $id;
                    }
                }
                else {
                    if (is_numeric($data['data']['filter_filter']) && !empty($data['data']['filter_filter'])) {
                        $filter[] = $data['data']['filter_filter'];
                    }
                }

                if (count($filter)) {
                    $sql .= ' AND pa.attribute_id IN (' . implode(',', $filter) . ') ';
                }
            }

            $sql .= '
            GROUP BY p.product_id ';
            switch($data['data']['sort']) {
                case 'pd.name':
                    $sql .= 'ORDER BY pd.name ' . $data['data']['order'];
                    break;

                case 'p.price':
                    $sql .= 'ORDER BY p.price ' . $data['data']['order'];
                    break;

                case 'p.model':
                    $sql .= 'ORDER BY p.model_2 ' . $data['data']['order'];
                    break;

                default:
                    $sql .= 'ORDER BY p.sort_order ' . $data['data']['order'];
                    break;
            }
            $values = array();
        }

        if ($data['type'] == 'manufacturer') {
            $sql = 'SELECT p.product_id, COUNT(*) AS total
            FROM PREFIX_product AS p';
            if ($data['data']['sort'] == 'pd.name') {
                $sql .= ' LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)';
            }

            if (!empty($data['data']['filter_filter'])) {
                $sql .= ' LEFT JOIN PREFIX_product_attribute AS pa ON pa.product_id = p.product_id ';
            }

            $sql .= '
            LEFT JOIN PREFIX_product_to_category AS ptc
                ON ptc.product_id = p.product_id
            WHERE p.manufacturer_id = :manufacturer_id
                AND p.status = 1 ';

            if (!empty($data['data']['filter_filter'])) {
                $filter = array();
                if (is_array($data['data']['filter_filter'])) {
                    foreach ($data['data']['filter_filter'] as $id => $unused) {
                        if (!is_numeric($id) || empty($id)) { continue; }
                        $filter[] = $id;
                    }
                }
                else {
                    if (is_numeric($data['data']['filter_filter']) && !empty($data['data']['filter_filter'])) {
                        $filter[] = $data['data']['filter_filter'];
                    }
                }

                if (count($filter)) {
                    $sql .= ' AND pa.attribute_id IN (' . implode(',', $filter) . ') ';
                }
            }

            $sql .= '
            GROUP BY p.product_id ';
            switch($data['data']['sort']) {
                case 'pd.name':
                    $sql .= 'ORDER BY pd.name ' . $data['data']['order'];
                    break;

                case 'p.price':
                    $sql .= 'ORDER BY p.price ' . $data['data']['order'];
                    break;

                case 'p.model':
                    $sql .= 'ORDER BY p.model_2 ' . $data['data']['order'];
                    break;

                default:
                    $sql .= 'ORDER BY p.sort_order ' . $data['data']['order'];
                    break;
            }
            $values = array('manufacturer_id' => $data['data']['filter_manufacturer_id']);
        }

        if (!empty($sql)) {
            $total = $this->query($sql, $values)->fetch();
            $result['total'] = $total['total'];
            $sql .= ' LIMIT ' . $data['data']['start'] . ',' . $data['data']['limit'];
            if ($data['type'] == 'category_path') {
                if (!$total || !count($total)) {
                    Sumo\Logger::warning('total query failed');
                    return;
                }
                Sumo\Logger::warning(print_r($total,true));
            }
            $products = $this->fetchAll($sql, $values);
            Sumo\Logger::info('Total products count: ' . $result['total']  . ', fetchAll with limit: ' . print_r($products,true));
        }

        if (is_array($products) && count($products)) {
            $count = 1;
            foreach ($products as $p) {
                if (empty($p['product_id'])) {
                    continue;
                }
                $product = $this->getProduct($p['product_id']);
                if ((!$product['stock_visible'] || ($product['stock_visible'] == 2 && !$this->config->get('display_stock_empty'))) && $product['quantity'] <= 0) {
                    Sumo\Logger::info('Product ' . $p['product_id'] . ' has no stock and may not be visible, or shop is set to disable low quantity products');
                    continue;
                }
                $result[$count++] = $product;
            }
        }

        if (!$result['total'] && $data['type'] == 'category') {
            $data['type'] = 'category_path';
            return $this->getProducts($data);
        }

        Sumo\Cache::set($cache, $result);
        Sumo\Logger::info('Put products in cache');
        return $result;
    }

    public function getLatest($data)
    {
        if (empty($data['limit'])) {
            $data['limit'] = 6;
        }

        if (!empty($data['category_id'])) {
            $data['search'] = 'ptc.category_id';
            $data['search_id'] = $data['category_id'];
        }
        else
        if (!empty($data['store_id'])) {
            $data['search'] = 'pts.store_id';
            $data['search_id'] = $data['store_id'];
        }
        else
        if (empty($data['category_id']) && empty($data['store_id'])) {
            $data['search'] = 'pts.store_id';
            $data['search_id'] = $this->config->get('store_id');
        }

        $cache  = 'wsp_plist_product_latest_' . md5(json_encode($data));
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && count($result)) {
            return $result;
        }

        $data = $this->fetchAll(
            "SELECT p.product_id
            FROM PREFIX_product AS p
            LEFT JOIN PREFIX_product_to_store AS pts
                ON pts.product_id = p.product_id
            LEFT JOIN PREFIX_product_to_category AS ptc
                ON ptc.product_id = p.product_id
            WHERE status = 1
                AND " . $data['search'] . " = " . (int) $data['search_id'] . "
            ORDER BY date_added DESC LIMIT 0," . (int)$data['limit'], $data);

        $result = array();
        foreach ($data as $item) {
            if (empty($item['product_id'])) {
                continue;
            }
            $result[$item['product_id']] = $this->getProduct($item['product_id']);
            if (empty($result[$item['product_id']])) {
                unset($result[$item['product_id']]);
            }
        }

        Sumo\Cache::set($cache, $result);
        return $result;
    }

    public function getRelated($input)
    {
        $related_id = $input['product_id'];
        $cache = 'wsp_item_product_related_' . $related_id;
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && !empty($result)) {
            return $result;
        }

        $data = $this->fetchAll(
            "SELECT related_id
            FROM PREFIX_product_related
            WHERE product_id = :id
            ",
            array(
                'id' => $related_id
            )
        );

        $result = array();
        foreach ($data as $item) {
            $product = $this->getProduct($item['related_id']);
            if (!is_array($product) || !isset($product['product_id']) || (!$product['stock_visible'] || ($product['stock_visible'] == 2 && !$this->config->get('display_stock_empty'))) && $product['quantity'] <= 0) {
                continue;
            }
            $result[$item['related_id']] = $product;
        }

        Sumo\Cache::set($cache, $result);
        return $result;
    }

    public function getProduct($id)
    {
        $cache = 'wsp_pitem_product_category_' . $id;
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && !empty($result)) {
            return $result;
        }

        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        }
        else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $product = $this->query(
            "SELECT DISTINCT *,
                pd.name AS name,
                p.image,
                m.name AS manufacturer,
                ptc.category_id,
                (
                    SELECT price
                    FROM PREFIX_product_discount pd2
                    WHERE pd2.product_id = p.product_id
                        AND pd2.customer_group_id = " . (int)$customer_group_id . "
                        AND pd2.quantity = '1'
                        AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                        AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW()))
                    ORDER BY pd2.priority ASC, pd2.price ASC
                    LIMIT 1
                ) AS discount,
                (
                    SELECT price
                    FROM PREFIX_product_special ps
                    WHERE ps.product_id = p.product_id
                        AND ps.customer_group_id = " . (int)$customer_group_id . "
                        AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                        AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
                    ORDER BY ps.priority ASC, ps.price ASC
                    LIMIT 1
                ) AS special,
                (
                    SELECT ss.name
                    FROM PREFIX_stock_status ss
                    WHERE ss.stock_status_id = p.stock_status_id
                        AND ss.language_id = " . (int)$this->config->get('language_id') . "
                ) AS stock_status,
                (
                    SELECT wcd.unit
                    FROM PREFIX_weight_class_description wcd
                    WHERE p.weight_class_id = wcd.weight_class_id
                        AND wcd.language_id = " . (int)$this->config->get('language_id') . "
                ) AS weight_class,
                (
                    SELECT lcd.unit
                    FROM PREFIX_length_class_description lcd
                    WHERE p.length_class_id = lcd.length_class_id
                        AND lcd.language_id = " . (int)$this->config->get('language_id') . "
                ) AS length_class,
                (
                    SELECT AVG(rating) AS total
                    FROM PREFIX_review r1
                    WHERE r1.product_id = p.product_id
                        AND r1.status = '1'
                    GROUP BY r1.product_id
                ) AS rating,
                (
                    SELECT COUNT(*) AS total
                    FROM PREFIX_review r2
                    WHERE r2.product_id = p.product_id
                        AND r2.status = '1'
                    GROUP BY r2.product_id
                ) AS reviews,
                p.sort_order
            FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)
            LEFT JOIN PREFIX_product_to_store p2s
                ON (p.product_id = p2s.product_id)
            LEFT JOIN PREFIX_manufacturer m
                ON (p.manufacturer_id = m.manufacturer_id)
            LEFT JOIN PREFIX_product_to_category ptc
                ON (p.product_id = ptc.product_id)
            LEFT JOIN PREFIX_category AS c
                ON (c.category_id = ptc.category_id)
            WHERE p.product_id = " . (int)$id . "
                AND pd.language_id = " . (int)$this->config->get('language_id') . "
                AND p.status = 1
                AND c.status = 1
                AND p2s.store_id = " . (int)$this->config->get('store_id'))->fetch();

        if (!empty($product) && !empty($product['product_id'])) {
            if ($product['stock_id'] != $id) {
                $sub = $this->query("
                    SELECT quantity, stock_status_id
                    FROM PREFIX_product
                    WHERE product_id = " . $product['stock_id']
                );
                $stock = $sub->fetch();
                if (is_array($stock) && count($stock)) {
                    $product['quantity'] = $stock['quantity'];
                    $product['stock_status_id'] = $stock['stock_status_id'];
                }
            }

            $product['images'][] = $product['image'];
            $images = $this->fetchAll("SELECT image FROM PREFIX_product_image WHERE product_id = :id ORDER BY sort_order ASC", array('id' => $id));
            if (is_array($images) && count($images)) {
                foreach ($images as $list) {
                    $product['images'][] = $list['image'];
                }
            }
            if ((!$product['stock_visible'] || ($product['stock_visible'] == 2 && !$this->config->get('display_stock_empty'))) && $product['quantity'] <= 0) {
                return false;
            }
            Sumo\Cache::set($cache, $product);
            return $product;
        }

        return false;
    }

    public function getFilters($data)
    {
        if (empty($data['data']['filter_category_id'])) {
            return;
        }

        if (!empty($data['data']['filter_category_id'])) {
            $category_id = $data['data']['filter_category_id'];
        }

        if (empty($data['data']['start'])) {
            $data['data']['start'] = 0;
        }

        if (empty($data['data']['limit'])) {
            $data['data']['limit'] = 15;
        }

        if (empty($data['data']['sort'])) {
            $data['data']['sort'] = 'p.sort_order';
        }

        if (empty($data['data']['order']) || !in_array($data['data']['order'], array('ASC', 'DESC'))) {
            $data['data']['order'] = 'ASC';
        }

        $cache = 'wsp_filters_' . md5(json_encode($data));
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && !empty($result)) {
            return $result;
        }

        $filters = Sumo\Database::fetchAll(
            "SELECT a.attribute_group_id
            FROM PREFIX_product_attribute AS pa
            LEFT JOIN PREFIX_attribute AS a
                ON a.attribute_id = pa.attribute_id
            LEFT JOIN PREFIX_product_to_category AS ptc
                ON ptc.product_id = pa.product_id
            WHERE ptc.category_id = :cat
                AND pa.language_id = :lang
                ",
            array(
                'cat'   => $category_id,
                'lang'  => $this->config->get('language_id')
            )
        );

        $result = array();
        foreach ($filters as $filter) {
            if (!isset($result[$filter['attribute_group_id']])) {
                $group = Sumo\Database::query(
                    "SELECT name FROM PREFIX_attribute_group_description WHERE attribute_group_id = :group AND language_id = :lang",
                    array('group' => $filter['attribute_group_id'], 'lang' => $this->config->get('language_id'))
                )->fetch();
                $result[$filter['attribute_group_id']]['name'] = $group['name'];

                $attributes = Sumo\Database::fetchAll(
                    "SELECT a.attribute_id, ad.name
                    FROM PREFIX_attribute AS a
                    LEFT JOIN PREFIX_attribute_description AS ad
                        ON a.attribute_id = ad.attribute_id
                    WHERE a.attribute_group_id  = :group
                        AND ad.language_id      = :lang",
                    array(
                        'group'                 => $filter['attribute_group_id'],
                        'lang'                  => $this->config->get('language_id')
                    )
                );

                foreach ($attributes as $list) {
                    if (isset($data['data']['filter_filter'][$list['attribute_id']])) {
                        $list['active'] = true;
                    }
                    $result[$filter['attribute_group_id']]['filters'][$list['attribute_id']] = $list;
                }
            }
        }

        Sumo\Cache::set($cache, $result);
        return $result;
    }

    public function getManufacturers()
    {
        $manufacturer_data = Sumo\Cache::find('manufacturer.' . (int)$this->config->get('store_id'));
        if (!$manufacturer_data) {
            $manufacturer_data = $this->fetchAll("SELECT * FROM PREFIX_manufacturer m LEFT JOIN PREFIX_manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = :store ORDER BY RAND()", array('store' => $this->config->get('store_id')));
            Sumo\Cache::set('manufacturer.' . (int)$this->config->get('store_id'), $manufacturer_data);
        }
        return $manufacturer_data;
    }
}
