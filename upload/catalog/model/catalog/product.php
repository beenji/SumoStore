<?php
namespace Sumo;
class ModelCatalogProduct extends Model
{
    public function updateViewed($product_id)
    {
        Database::query("UPDATE PREFIX_product SET viewed = (viewed + 1) WHERE product_id = :id", array('id' => $product_id));
    }

    public function getProduct($product_id)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        }
        else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $cache = 'product.' . $product_id . '.' . $customer_group_id;
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $data = self::query("
            SELECT
                DISTINCT *,
                pd.name AS name,
                p.image,
                p.stock_id,
                m.name AS manufacturer,
                ptc.category_id,
                (
                    SELECT price
                    FROM PREFIX_product_discount pd2
                    WHERE pd2.product_id = p.product_id
                        AND pd2.customer_group_id = '" . (int)$customer_group_id . "'
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
                        AND ps.customer_group_id = '" . (int)$customer_group_id . "'
                        AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                        AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
                    ORDER BY ps.priority ASC, ps.price ASC
                    LIMIT 1
                ) AS special,
                (
                    SELECT ss.name
                    FROM PREFIX_stock_status ss
                    WHERE ss.stock_status_id = p.stock_status_id
                        AND ss.language_id = '" . (int)$this->config->get('language_id') . "'
                ) AS stock_status,
                (
                    SELECT wcd.unit
                    FROM PREFIX_weight_class_description wcd
                    WHERE p.weight_class_id = wcd.weight_class_id
                        AND wcd.language_id = '" . (int)$this->config->get('language_id') . "'
                ) AS weight_class,
                (
                    SELECT lcd.unit
                    FROM PREFIX_length_class_description lcd
                    WHERE p.length_class_id = lcd.length_class_id
                        AND lcd.language_id = '" . (int)$this->config->get('language_id') . "'
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
            WHERE p.product_id = '" . (int)$product_id . "'
                AND pd.language_id = '" . (int)$this->config->get('language_id') . "'
                AND p.status = 1
                AND c.status = 1
                AND p2s.store_id = '" . (int)$this->config->get('store_id') . "'")->fetch();

        if (count($data)) {
            // Fetch linked stock
            if ($data['stock_id'] != $product_id) {
                $sub = $this->query("
                    SELECT quantity, stock_status_id
                    FROM PREFIX_product
                    WHERE product_id = " . (int)$data['stock_id']
                );
                $stock = $sub->fetch();
                if (is_array($stock) && count($stock)) {
                    $data['quantity'] = $stock['quantity'];
                    $data['stock_status_id'] = $stock['stock_status_id'];
                }
            }
            if ((!$data['stock_visible'] || ($data['stock_visible'] == 2 && !$this->config->get('display_stock_empty'))) && $data['quantity'] <= 0) {
                return false;
            }

            if ($data['discount']) {
                $data['price'] = $data['discount'];
            }
            $data['rating'] = round($data['rating']);
            if (!$data['reviews']) {
                $data['reviews'] = 0;
            }
            Cache::set($cache, $data);
            return $data;
        }
        return false;
    }

    public function getProducts($data = array(), $countOnly = false)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        }
        else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        if (!$countOnly) {
            $cacheFile = 'products.data.' . implode('-', $data) . '-' . $customer_group_id . '-' . (int)$this->config->get('store_id');
            $cached = Cache::find($cacheFile);
            if (is_array($cached) && count($cached)) {
                return $cached;
            }
        }

        $values = array();
        $sql = "
            SELECT
                p.product_id,
                COUNT(*) AS total_count
        ";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= "
            FROM PREFIX_category_path cp
            LEFT JOIN PREFIX_product_to_category p2c
                ON (cp.category_id = p2c.category_id)";
            }
            else {
                $sql .= "
            FROM PREFIX_product_to_category p2c";
            }

            if (!empty($data['filter_filter'])) {
                $sql .= "
            LEFT JOIN PREFIX_product_filter pf
                ON (p2c.product_id = pf.product_id)
            LEFT JOIN PREFIX_product p
                ON (pf.product_id = p.product_id)";
            }
            else {
                $sql .= "
            LEFT JOIN PREFIX_product p
                ON (p2c.product_id = p.product_id)";
            }
        }
        else {
            $sql .= "
            FROM PREFIX_product p";
        }

        $sql .= "
            LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)
            LEFT JOIN PREFIX_product_description pd2
                ON (p.product_id = pd2.product_id)
            LEFT JOIN PREFIX_product_to_store p2s
                ON (p.product_id = p2s.product_id)
            WHERE pd.language_id = " . (int)$this->config->get('language_id') . "
                AND p.status = 1
                AND p2s.store_id = " . (int)$this->config->get('store_id');

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= "
                AND cp.path_id = " . (int)$data['filter_category_id'];
            }
            else {
                $sql .= "
                AND p2c.category_id = " . (int)$data['filter_category_id'];
            }

            if (!empty($data['filter_filter'])) {
                $implode = array();

                $filters = explode(',', $data['filter_filter']);

                foreach ($filters as $filter_id) {
                    $implode[] = (int)$filter_id;
                }

                $sql .= "
                AND pf.filter_id IN (" . implode(',', $implode) . ")";
            }
        }

        if (!empty($data['filter_name']) || !empty($data['filter_tag']) || !empty($data['filter_description'])) {
            $sql .= "
                AND (";

            if (!empty($data['filter_description'])) {
                $sql .= " OR pd.description LIKE :description";
                $values['description'] = '%' . $data['filter_description'] . '%';
            }

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/[^a-zA-Z0-9-.,]/', ' ', $data['filter_name'])));
                $count = 0;
                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE :assigned_search" . $count;
                    $values['assigned_search' . $count] = '%' . $word . '%';
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                $sql .= " OR LCASE(p.model) LIKE :model_filter1";
                $values['model_filter1'] = strtolower($data['filter_name']) . '%';


                $sql .= " OR LCASE(p.model_2) LIKE :model_filter2";
                $values['model_filter2'] = strtolower($data['filter_name']) . '%';

                foreach (array('sku', 'upc', 'ean', 'jan', 'isbn', 'mpn') as $tag) {
                    $sql .= " OR LCASE(p." . $tag . ") = :" . $tag;
                    $values[$tag] = strtolower($data['filter_name']);
                }

                $sql .= " OR LCASE(p.sku) = :sku";
                $values['sku'] = strtolower($data['filter_name']);
            }

            if (!empty($data['filter_tag'])) {
                $sql .= " OR pd2.tag LIKE :filter_tag";
                $values['filter_tag'] = '%' . $data['filter_tag'] . '%';
            }

            $sql .= ")";
        }

        if (!empty($data['filter_manufacturer_id'])) {
            $sql .= " AND p.manufacturer_id = " . (int)$data['filter_manufacturer_id'];
        }

        $sql .= "
            GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.quantity',
            'p.price',
            'rating',
            'p.sort_order',
            'p.date_added'
        );

        if (!$countOnly) {
            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                    $sql .= "
                ORDER BY LCASE(" . $data['sort'] . ")";
                }
                elseif ($data['sort'] == 'p.price') {
                    $sql .= "
                ORDER BY (
                    CASE
                        WHEN special IS NOT NULL
                            THEN special
                        WHEN discount IS NOT NULL
                            THEN discount
                        ELSE
                            p.price
                    END
                )";
                } else {
                    $sql .= "
                ORDER BY " . $data['sort'];
                }
            }
            else {
                $sql .= "
                ORDER BY p.sort_order";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= "
                    DESC, LCASE(pd.name) DESC";
            }
            else {
                $sql .= "
                    ASC, LCASE(pd.name) ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= "
                LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }
        }

        $product_data = array();

        $data = $this->fetchAll($sql, $values);

        foreach ($data as $result) {
            $result = $this->getProduct($result['product_id']);
            if ((!$result['stock_visible'] || ($result['stock_visible'] == 2 && !$this->config->get('display_stock_empty'))) && $result['quantity'] <= 0) {
                continue;
            }

            $product_data[$result['product_id']] = $result;
            if (!$countOnly) {
                Cache::set($cacheFile, $result['product_id'], $result);
            }
        }

        if ($countOnly) {
            return count($product_data);
        }
        return $product_data;
    }

    public function getProductSpecials($data = array())
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM PREFIX_review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM PREFIX_product_special ps LEFT JOIN PREFIX_product p ON (ps.product_id = p.product_id) LEFT JOIN PREFIX_product_description pd ON (p.product_id = pd.product_id) LEFT JOIN PREFIX_product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'ps.price',
            'rating',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $product_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
        }

        return $product_data;
    }

    public function getLatestProducts($limit)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $product_data = Cache::find('products.latest.' . (int)$this->config->get('language_id') . '.' . (int)$this->config->get('store_id') . '.' . $customer_group_id . '.' . (int)$limit);

        if (!$product_data) {
            $query = $this->db->query("SELECT p.product_id FROM PREFIX_product p LEFT JOIN PREFIX_product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

            foreach ($query->rows as $result) {
                $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
            }

            Cache::set('products.latest.' . (int)$this->config->get('language_id') . '.' . (int)$this->config->get('store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
        }

        return $product_data;
    }

    public function getPopularProducts($limit)
    {
        $product_data = array();

        $query = $this->db->query("SELECT p.product_id FROM PREFIX_product p LEFT JOIN PREFIX_product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('store_id') . "' ORDER BY p.viewed, p.date_added DESC LIMIT " . (int)$limit);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
        }

        return $product_data;
    }

    public function getBestSellerProducts($limit)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $product_data = Cache::find('products.bestseller.' . (int)$this->config->get('language_id') . '.' . (int)$this->config->get('store_id'). '.' . $customer_group_id . '.' . (int)$limit);

        if (!$product_data) {
            $product_data = array();

            $query = $this->fetchAll("SELECT op.product_id, COUNT(*) AS total FROM PREFIX_order_product op LEFT JOIN `PREFIX_order` o ON (op.order_id = o.order_id) LEFT JOIN `PREFIX_product` p ON (op.product_id = p.product_id) LEFT JOIN PREFIX_product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

            foreach ($query as $result) {
                $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
            }

            Cache::set('products.bestseller.' . (int)$this->config->get('language_id') . '.' . (int)$this->config->get('store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
        }

        return $product_data;
    }

    public function getProductAttributes($product_id)
    {

        $product_attribute_group_data = array();

        $product_attribute_group_query = $this->fetchAll("SELECT ag.attribute_group_id, agd.name FROM PREFIX_product_attribute pa LEFT JOIN PREFIX_attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN PREFIX_attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN PREFIX_attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

        foreach ($product_attribute_group_query as $product_attribute_group) {
            $product_attribute_data = array();

            $product_attribute_query = $this->fetchAll("SELECT a.attribute_id, ad.name, pa.text FROM PREFIX_product_attribute pa LEFT JOIN PREFIX_attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN PREFIX_attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('language_id') . "' AND pa.language_id = '" . (int)$this->config->get('language_id') . "' ORDER BY a.sort_order, ad.name");

            foreach ($product_attribute_query as $product_attribute) {
                $product_attribute_data[] = array(
                    'attribute_id' => $product_attribute['attribute_id'],
                    'name'         => $product_attribute['name'],
                    'text'         => $product_attribute['text']
                );
            }

            $product_attribute_group_data[] = array(
                'attribute_group_id' => $product_attribute_group['attribute_group_id'],
                'name'               => $product_attribute_group['name'],
                'attribute'          => $product_attribute_data
            );
        }

        return $product_attribute_group_data;
    }

    public function getProductOptions($productID)
    {
        // Plan C.
        $productOptions = $this->fetchAll("SELECT *
            FROM PREFIX_product_option
            WHERE product_id = " . (int)$productID);

        foreach ($productOptions as $k => $productOption) {
            // Grab language-data for options
            $optionDescriptions = $this->fetchAll("SELECT *
                FROM PREFIX_product_option_description od
                WHERE od.option_id = " . (int)$productOption['option_id']);

            foreach ($optionDescriptions as $optionDescription) {
                if ($optionDescription['language_id'] == $this->config->get('language_id')) {
                    // Default language, add as 'label'
                    $productOptions[$k]['name'] = $optionDescription['name'];
                }

                // Add all descriptions
                $productOptions[$k]['option_description'][$optionDescription['language_id']]['name'] = $optionDescription['name'];
            }

            // Proceed to option values
            $optionValues = $this->fetchAll("SELECT *
                FROM PREFIX_product_option_value
                WHERE active = 1 AND option_id = " . (int)$productOption['option_id']);

            foreach ($optionValues as $l => $optionValue) {
                // Grab language-data for option-values
                $optionValueDescriptions = $this->fetchAll("SELECT *
                    FROM PREFIX_product_option_value_description
                    WHERE value_id = " . (int)$optionValue['value_id']);

                foreach ($optionValueDescriptions as $optionValueDescription) {
                    if ($optionValueDescription['language_id'] == $this->config->get('language_id')) {
                        $optionValues[$l]['name'] = $optionValueDescription['name'];
                    }

                    $optionValues[$l]['option_value_description'][$optionValueDescription['language_id']]['name'] = $optionValueDescription['name'];
                }
            }

            $productOptions[$k]['product_option_value'] = $optionValues;
        }

        return $productOptions;
    }

    public function getProductDiscounts($product_id)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

       return self::fetchAll("SELECT * FROM PREFIX_product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");
    }

    public function getProductImages($product_id)
    {
        $images = Cache::find('products.images.' . $product_id);
        if ($images === null) {
            $images = Database::fetchAll("SELECT * FROM PREFIX_product_image WHERE product_id = :pid ORDER BY sort_order ASC", array('pid' => $product_id));
            Cache::set('products.images.' . $product_id, $images);
        }
        return $images;
    }

    public function getProductRelated($product_id)
    {
        $products = Cache::find('products.related.' . $product_id);
        if (!is_array($products)) {
            $product_ids = Database::fetchAll("
            SELECT *
            FROM PREFIX_product_related AS pr
            LEFT JOIN PREFIX_product AS p
                ON (pr.related_id = p.product_id)
            LEFT JOIN PREFIX_product_to_store AS p2s
                ON (p.product_id = p2s.product_id)
            WHERE pr.product_id = '" . (int)$product_id . "'
                AND p.status = '1'
                AND p2s.store_id = '" . (int)$this->config->get('store_id') . "'");
            $products = array();

            foreach ($product_ids as $list) {
                $products[] = $this->getProduct($list['product_id']);
            }
            Cache::set('products.related.' . $product_id, $products);
        }
        return $products;
    }

    public function getProductLayoutId($product_id)
    {
        $query = $this->db->query("SELECT * FROM PREFIX_product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('store_id') . "'");

        if ($query->num_rows) {
            return $query->row['layout_id'];
        } else {
            return  $this->config->get('layout_product');
        }
    }

    public function getCategories($product_id)
    {
        $query = $this->db->query("SELECT * FROM PREFIX_product_to_category WHERE product_id = '" . (int)$product_id . "'");

        return $query->rows;
    }

    public function getTotalProducts($data = array())
    {
        return $this->getProducts($data, true);
    }

    public function getTotalProductSpecials()
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('customer_group_id');
        }

        $query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM PREFIX_product_special ps LEFT JOIN PREFIX_product p ON (ps.product_id = p.product_id) LEFT JOIN PREFIX_product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

        if (isset($query->row['total'])) {
            return $query->row['total'];
        }
        return 0;
    }
}
