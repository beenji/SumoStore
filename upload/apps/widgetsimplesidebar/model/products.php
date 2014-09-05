<?php
namespace Widgetsimpleproduct;
use App;
use Sumo;

class ModelProducts extends App\Model
{
    public function getProducts($data)
    {

        Sumo\Logger::info('getProducts: ' . print_r($data,true));

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
            Sumo\Logger::info('Got products from cache: ' . print_r($result,true));
            return $result;
        }

        if ($data['type'] == 'category') {
            $sql = 'SELECT p.product_id
            FROM PREFIX_product AS p';
            if ($data['data']['sort'] == 'pd.name') {
                $sql .= ' LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)';
            }
            $sql .= '
            LEFT JOIN PREFIX_product_to_category AS ptc
                ON ptc.product_id = p.product_id
            WHERE ptc.category_id = :category_id
                AND p.status = 1
            GROUP BY p.product_id';
            if ($data['data']['sort'] == 'pd.name') {
                $sql .= ' ORDER BY pd.name ' . $data['data']['order'];
            }
            $values = array('category_id' => $category_id);
        }

        if (!empty($sql)) {
            $total = $this->fetchAll($sql, $values);
            $result['total'] = count($total);
            $sql .= ' LIMIT ' . $data['data']['start'] . ',' . $data['data']['limit'];
            $products = $this->fetchAll($sql, $values);
        }

        if (is_array($products)) {
            foreach ($products as $p) {
                $product = $this->getProduct($p['product_id']);
                $result[] = $product;
            }
        }
        Sumo\Cache::set($cache, $result);
        Sumo\Logger::info('Put products in cache: ' . print_r($result,true));
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

        if (!empty($product)) {
            if ($product['discount']) {
                //$data['price'] = $data['discount'];
            }

            $product['images'][] = $product['image'];
            $images = $this->fetchAll("SELECT image FROM PREFIX_product_image WHERE product_id = :id ORDER BY sort_order ASC", array('id' => $id));
            if (is_array($images) && count($images)) {
                foreach ($images as $list) {
                    $product['images'][] = $list['image'];
                }
            }

            Sumo\Cache::set($cache, $product);
            return $product;
        }

        return false;
    }
}
