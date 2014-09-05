<?php
namespace Sumo;
class ModelCatalogProduct extends Model
{
    public function addProduct()
    {
        $this->query("INSERT INTO PREFIX_product SET date_added = :date", array('date' => date('Y-m-d H:i:s')));

        $product_id = $this->lastInsertId();
        $this->query("
            UPDATE PREFIX_product
            SET model = 'P" . $product_id . "'
            WHERE product_id = " . $product_id
        );

        return $product_id;
    }

    public function editProduct($product_id, $data)
    {
        // stock & quantity
        if ((isset($data['stock_product']) && !$data['stock_product']) || $data['stock_id'] == $product_id) {
            $data['stock_id'] = $product_id;
            $data['product_quantity'] = (int)$data['product_quantity'];
            if (!isset($data['stock_status_id'])) {
                $data['stock_status_id'] = 1;
            }
            $data['stock_status_id'] = (int)$data['stock_status_id'];
        }
        else {
            // Existing product as stock_id?
            $query = $this->query('SELECT product_id FROM PREFIX_product WHERE product_id = ' . (int)$data['stock_id'])->fetch();

            if ($query['product_id'] > 0) {
                $data['product_quantity'] = 'p2.quantity';
                $data['stock_status_id'] = 'p2.stock_status_id';
            }
            else {
                $data['stock_status_id'] = 1;
                $data['stock_id'] = $product_id;
                $data['product_quantity'] = (int)$data['product_quantity'];
            }
        }

        $data['product_price']  = str_replace(',', '.', $data['product_price']);
        $data['cost_price']  = str_replace(',', '.', $data['cost_price']);
        $this->query(
            "UPDATE PREFIX_product
            SET stock_id = :stock
            WHERE product_id = :pid",
            array(
                'stock' => $data['stock_id'],
                'pid'   => $product_id
            )
        );

        $this->query(
            "UPDATE PREFIX_product AS p
            INNER JOIN PREFIX_product AS p2
                ON p.stock_id = p2.product_id
            SET p.model_2           = :model,
                p.model_supplier    = :model_supplier,
                p.location          = :location,
                p.minimum           = :minimum,
                p.subtract          = :subtract,
                p.quantity          = :quantity,
                p.stock_status_id   = :stock_status_id,
                p.stock_id          = :stock_id,
                p.stock_visible     = :stock_visible,
                p.date_available    = :date_available,
                p.manufacturer_id   = :manufacturer_id,
                p.shipping          = :shipping,
                p.price             = :price,
                p.cost_price        = :cost_price,
                p.points            = :points,
                p.weight            = :weight,
                p.weight_class_id   = :wclass_id,
                p.length            = :length,
                p.width             = :width,
                p.height            = :height,
                p.length_class_id   = :lclass_id,
                p.status            = :status,
                p.tax_percentage    = :tax_percentage,
                p.date_modified     = :modified
            WHERE p.product_id      = :product_id",
            array(
                'model'             => $data['model_2'],
                'model_supplier'    => $data['model_supplier'],
                'location'          => $data['location'],
                'minimum'           => $data['minimum'],
                'subtract'          => $data['subract'],
                'quantity'          => $data['product_quantity'],
                'stock_status_id'   => $data['stock_status_id'],
                'stock_id'          => $data['stock_id'],
                'stock_visible'     => $data['stock_visible'],
                'date_available'    => Formatter::dateReverse($data['date_available']),
                'manufacturer_id'   => $data['manufacturer_id'],
                'shipping'          => $data['shipping'],
                'price'             => $data['product_price'],
                'cost_price'        => $data['cost_price'],
                'points'            => $data['product_points'],
                'weight'            => $data['product_weight'],
                'wclass_id'         => $data['weight_class_id'],
                'length'            => $data['length'],
                'lclass_id'         => $data['length_class_id'],
                'height'            => $data['height'],
                'width'             => $data['width'],
                'status'            => $data['status'],
                'tax_percentage'    => $data['tax_percentage'],
                'modified'          => date('Y-m-d H:i:s'),
                'product_id'        => $product_id
            )
        );

        $info_types = array(
            'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn'
        );
        foreach ($info_types as $type) {
            if (isset($data[$type])) {
                if (!isset($data[$type . '_visible'])) {
                    $data[$type . '_visible'] = 0;
                }
                $this->query("
                    UPDATE PREFIX_product
                    SET " . $type . " = :var,
                        " . $type . "_visible = " . (int)$data[$type . '_visible'] . "
                    WHERE product_id = :pid",
                    array(
                        'pid'   => $product_id,
                        'var'   => !empty($data[$type]) ? $data[$type] : ''
                    )
                );
            }
        }

        if (!isset($data['sort_order'])) {
            $sort_order = 0;

            // Get highest sort order for all categories
            foreach ($data['product_category'] as $k => $category_id) {
                $row = $this->query("
                    SELECT MAX(sort_order) AS sort_order
                    FROM PREFIX_product p
                    LEFT JOIN PREFIX_product_to_category AS ptc
                        ON ptc.product_id = p.product_id
                    WHERE ptc.category_id = " . $category_id)->fetch();
                if ($row['sort_order'] > $sort_order) {
                    $sort_order = $row['sort_order'];
                }
            }

            $this->query("
                UPDATE PREFIX_product
                SET sort_order = " . (int)($sort_order + 1) . "
                WHERE product_id = " . (int)$product_id
            );
        }

        // Delete existing url-aliases (this may cause SEO-issues, investigate that a bit...)
        $this->query("DELETE FROM PREFIX_url_alias WHERE `query` = 'product_id=" . $product_id . "'");

        $this->query("DELETE FROM PREFIX_product_description WHERE product_id = " . (int)$product_id);
        foreach ($data['product_description'] as $language_id => $value) {
            unset($value['keyword']);

            $value['product_id'] = $product_id;
            $value['language_id'] = $language_id;
            $this->query("
                INSERT INTO PREFIX_product_description
                SET product_id      = :product_id,
                    language_id     = :language_id,
                    name            = :name,
                    title           = :title,
                    meta_keyword    = :meta_keyword,
                    meta_description = :meta_description,
                    description     = :description,
                    tag             = :tag",
                $value
            );

            foreach (array_unique($data['product_category']) as $category_id) {
                foreach (array_unique($data['product_store']) as $store_id) {
                    Formatter::generateSeoURL($value['name'], 'product_id', $product_id, $language_id, $store_id, $category_id);
                }
            }
        }

        $this->query("DELETE FROM PREFIX_product_to_store WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_to_category WHERE product_id = " . (int)$product_id);

        foreach (array_unique($data['product_category']) as $category_id) {
            $this->query("
                INSERT INTO PREFIX_product_to_category
                SET product_id  = " . (int)$product_id . ",
                    category_id = " . (int)$category_id
            );
        }

        foreach (array_unique($data['product_store']) as $store_id) {
            $this->query("
                INSERT INTO PREFIX_product_to_store
                SET product_id  = " . (int)$product_id . ",
                    store_id    = " . (int)$store_id
            );
        }

        $this->query("DELETE FROM PREFIX_product_attribute WHERE product_id = " . (int)$product_id);

        if (!empty($data['attribute']) && is_array($data['attribute'])) {
            $this->load->model('catalog/attribute');

            foreach ($data['attribute'] as $attributeID) {
                // Get attribute info
                $attributeInfo = $this->model_catalog_attribute->getAttributeDescriptions($attributeID);

                if (!empty($attributeInfo) && is_array($attributeInfo)) {
                    foreach ($attributeInfo as $languageID => $attributeDescription) {
                        $this->query("
                            INSERT INTO PREFIX_product_attribute
                            SET product_id  = " . (int)$product_id . ",
                                attribute_id    = " . (int)$attributeID . ",
                                language_id     = " . (int)$languageID . ",
                                text            = :text",
                            array(
                                'text' => $attributeDescription['name']
                            )
                        );
                    }
                }
            }
        }

        $this->query("DELETE FROM PREFIX_product_option_value_description WHERE value_id IN (SELECT value_id FROM PREFIX_product_option_value WHERE option_id IN (SELECT option_id FROM PREFIX_product_option WHERE product_id = " . (int)$product_id . "))");
        $this->query("DELETE FROM PREFIX_product_option_value WHERE option_id IN (SELECT option_id FROM PREFIX_product_option WHERE product_id = " . (int)$product_id . ")");
        $this->query("DELETE FROM PREFIX_product_option_description WHERE option_id IN (SELECT option_id FROM PREFIX_product_option WHERE product_id = " . (int)$product_id . ")");
        $this->query("DELETE FROM PREFIX_product_option WHERE product_id = " . (int)$product_id);

        if (isset($data['product_option'])) {
            foreach ($data['product_option'] as $productOption) {
                if (!empty($productOption['option_description'][$this->config->get('language_id')]['name'])) {
                    $this->query("INSERT INTO PREFIX_product_option SET
                        product_id = :productID,
                        type = :type,
                        sort_order = 0", array(
                            'productID'  => $product_id,
                            'type'       => $productOption['type']
                        ));

                    $optionID = $this->lastInsertId();

                    // Insert translations
                    foreach ($productOption['option_description'] as $languageID => $productOptionDescription) {
                        $this->query("INSERT INTO PREFIX_product_option_description SET
                            option_id = :optionID,
                            language_id = :languageID,
                            name = :name", array(
                                'optionID'      => $optionID,
                                'languageID'    => $languageID,
                                'name'          => $productOptionDescription['name']));
                    }

                    // Insert values (if applicable)
                    if (in_array($productOption['type'], array('select', 'radio', 'checkbox')) && isset($productOption['product_option_value'])) {
                        foreach ($productOption['product_option_value'] as $productOptionValue) {
                            if (!empty($productOptionValue['option_value_description'][$this->config->get('language_id')]['name'])) {
                                // We want to store the price excluding the tax. Why? The Dutch government likes to change tax
                                // rates. So! Store as ex-tax, show as inc-tax.

                                // Make sure commas are replaced with decimal points
                                $productOptionValue['price']  = str_replace(',', '.', $productOptionValue['price']);
                                $productOptionValue['weight'] = str_replace(',', '.', $productOptionValue['weight']);

                                $productOptionValue['price'] = round(floatval($productOptionValue['price'])  / (1 + ($data['tax_percentage'] / 100)), 4);

                                $this->query("INSERT INTO PREFIX_product_option_value SET
                                    option_id = :optionID,
                                    active = :active,
                                    quantity = :quantity,
                                    subtract = :subtract,
                                    price = :price,
                                    price_prefix = :pricePrefix,
                                    weight = :weight,
                                    weight_prefix = :weightPrefix", array(
                                        'optionID'      => $optionID,
                                        'active'        => $productOptionValue['active'],
                                        'quantity'      => $productOptionValue['quantity'],
                                        'subtract'      => $productOptionValue['subtract'],
                                        'price'         => $productOptionValue['price'],
                                        'pricePrefix'   => $productOptionValue['price_prefix'],
                                        'weight'        => $productOptionValue['weight'],
                                        'weightPrefix'  => $productOptionValue['weight_prefix']));

                                $valueID = $this->lastInsertId();

                                // Insert translations
                                foreach ($productOptionValue['option_value_description'] as $languageID => $productOptionValueDescription) {
                                    $this->query("INSERT INTO PREFIX_product_option_value_description SET
                                        value_id = :valueID,
                                        language_id = :languageID,
                                        name = :name", array(
                                            'valueID'    => $valueID,
                                            'languageID' => $languageID,
                                            'name'       => $productOptionValueDescription['name']));
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->query("DELETE FROM PREFIX_product_discount WHERE product_id = " . (int)$product_id);

        if (isset($data['product_discount']) && count($data['product_discount'])) {
            foreach ($data['product_discount'] as $list) {
                if (empty($list['customer_group_id'])) {
                    continue;
                }
                if (empty($list['customer_group_id'])) {
                    $list['customer_group_id'] = 0;
                }
                $this->db->query("
                    INSERT INTO PREFIX_product_discount
                    SET product_id          = :product_id,
                        customer_group_id   = :customer_group_id,
                        quantity            = :quantity,
                        priority            = :priority,
                        price               = :price,
                        date_start          = :date_start
                        date_end            = :date_end",
                    array(
                        'product_id'        => $product_id,
                        'customer_group_id' => $list['customer_group_id'],
                        'quantity'          => $list['quantity'],
                        'priority'          => $list['priority'],
                        'price'             => $list['price'],
                        'date_start'        => Formatter::dateReverse($list['date_start']),
                        'date_end'          => Formatter::dateReverse($list['date_end'])
                    )
                );
            }
        }

        $this->query("DELETE FROM PREFIX_product_special WHERE product_id = " . (int)$product_id);

        if (isset($data['product_special'])) {
            foreach ($data['product_special'] as $list) {
                if (empty($list['customer_group_id'])) {
                    continue;
                }
                if (empty($list['priority'])) {
                    $list['priority'] = 1;
                }
                $this->query("
                    INSERT INTO PREFIX_product_special
                    SET product_id          = :product_id,
                        customer_group_id   = :customer_group_id,
                        priority            = :priority,
                        price               = :price,
                        date_start          = :date_start,
                        date_end            = :date_end",
                    array(
                        'product_id'        => $product_id,
                        'customer_group_id' => $list['customer_group_id'],
                        'priority'          => $list['priority'],
                        'price'             => $list['price'],
                        'date_start'        => Formatter::dateReverse($list['date_start']),
                        'date_end'          => Formatter::dateReverse($list['date_end'])
                    )
                );
            }
        }

        $this->query("DELETE FROM PREFIX_product_image WHERE product_id = " . (int)$product_id);

        if (isset($data['product_image']) && sizeof($data['product_image']) > 0) {
            foreach ($data['product_image'] as $i => $product_image) {
                if ($i == 0) {
                    $this->query("
                        UPDATE PREFIX_product
                        SET image = :image
                        WHERE product_id = " . (int)$product_id,
                        array('image' => $product_image)
                    );
                }
                else {
                    $this->query("
                        INSERT INTO PREFIX_product_image
                        SET product_id = " . (int)$product_id . ",
                            image = :name,
                            sort_order = " . ($i + 1),
                        array('name' => $product_image)
                    );
                }
            }
        }
        else {
            // Remove main image
            $this->query("UPDATE PREFIX_product SET image = '' WHERE product_id = " . (int) $product_id);
        }

        $this->query("DELETE FROM PREFIX_product_to_download WHERE product_id = " . (int)$product_id);

        if (isset($data['product_download'])) {
            foreach ($data['product_download'] as $download_id) {
                $this->query("
                    INSERT INTO PREFIX_product_to_download
                    SET product_id = " . (int)$product_id . ",
                        download_id = " . (int)$download_id
                );
            }
        }

        $this->query("DELETE FROM PREFIX_product_related WHERE product_id = '" . (int)$product_id . "'");

        if (isset($data['product_related'])) {
            foreach ($data['product_related'] as $related_id) {
                $this->query("INSERT INTO PREFIX_product_related SET product_id = " . (int)$product_id . ", related_id = " . (int)$related_id);
            }
        }

        Cache::removeAll();
    }

    public function copyProduct($product_id)
    {
        $query = $this->query(
            "SELECT DISTINCT *
            FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)
            WHERE p.product_id = :pid
                AND pd.language_id = :lang",
            array(
                'pid'   => $product_id,
                'lang'  => $this->config->get('language_id')
            )
        )->fetch();

        if (count($query)) {
            $data = array();

            $data = $query;

            /*$data['sku'] = '';
            $data['upc'] = '';*/
            $data['viewed'] = '0';
            $data['keyword'] = '';
            $data['status'] = '0';
            $data['date_available'] = Formatter::date($data['date_available']);

            $productImages = array();
            // Don't forget about the default image...
            $productImages[0] = $data['image'];

            foreach ($this->getProductImages($product_id) as $productImage) {
                $productImages[] = $productImage['image'];
            }

            $data = array_merge($data, array(
                'product_price'  => $data['price'],
                'product_points' => $data['points'],
                'product_weight' => $data['weight']
            ));

            $data = array_merge($data, array('attribute'            => $this->getProductAttributes($product_id)));
            $data = array_merge($data, array('product_description'  => $this->getProductDescriptions($product_id)));
            $data = array_merge($data, array('product_discount'     => $this->getProductDiscounts($product_id)));
            $data = array_merge($data, array('product_image'        => $productImages));
            $data = array_merge($data, array('product_option'       => $this->getProductOptions($product_id)));
            $data = array_merge($data, array('product_related'      => $this->getProductRelated($product_id)));
            $data = array_merge($data, array('product_special'      => $this->getProductSpecials($product_id)));
            $data = array_merge($data, array('product_category'     => $this->getProductCategories($product_id)));
            $data = array_merge($data, array('product_download'     => $this->getProductDownloads($product_id)));
            $data = array_merge($data, array('product_store'        => $this->getProductStores($product_id)));

            $new_id = $this->addProduct();
            $data['model'] = 'P' . $new_id;
            $this->editProduct($new_id, $data);
            $this->query("
                UPDATE PREFIX_product
                SET image = '" . $data['image'] . "',
                    status = " . $data['status'] . "
                WHERE product_id = " . $new_id
            );
        }

        Cache::removeAll();
    }

    public function deleteProduct($product_id)
    {
        $this->query("DELETE FROM PREFIX_product WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_attribute WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_description WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_discount WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_image WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_option_value_description WHERE value_id IN (SELECT value_id FROM PREFIX_product_option_value WHERE option_id IN (SELECT option_id FROM PREFIX_product_option WHERE product_id = " . (int)$product_id . "))");
        $this->query("DELETE FROM PREFIX_product_option_value WHERE option_id IN (SELECT option_id FROM PREFIX_product_option WHERE product_id = " . (int)$product_id . ")");
        $this->query("DELETE FROM PREFIX_product_option_description WHERE option_id IN (SELECT option_id FROM PREFIX_product_option WHERE product_id = " . (int)$product_id . ")");
        $this->query("DELETE FROM PREFIX_product_option WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_related WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_related WHERE related_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_special WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_to_category WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_to_download WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_product_to_store WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_review WHERE product_id = " . (int)$product_id);
        $this->query("DELETE FROM PREFIX_url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

        Cache::removeAll();
    }

    public function getProduct($product_id)
    {
        $cache = 'products.single.' . $product_id;
        $result = Cache::find($cache);
        if (is_array($result) && count($result)) {
            return $result;
        }

        $sql = "
            SELECT *
            FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)
            WHERE pd.language_id = " . (int)$this->config->get('language_id') . "
                AND p.product_id = " . (int)$product_id;

        $query = $this->query($sql);
        $row = $query->fetch();

        if (empty($row['product_id'])) {
            return false;
        }

        // Fetch category
        $sub = $this->fetchAll("SELECT category_id FROM PREFIX_product_to_category WHERE product_id = " . $row['product_id']);
        $row['categories'] = array();
        foreach ($sub as $list) {
            $row['categories'][$list['category_id']] = $list['category_id'];
        }

        // Fetch store
        $sub = $this->fetchAll("SELECT store_id FROM PREFIX_product_to_store WHERE product_id = " . $row['product_id']);
        $row['stores'] = array();
        foreach ($sub as $list) {
            $row['stores'][$list['store_id']] = $list['store_id'];
            $row['store_id'] = $list['store_id'];
        }

        // Fetch linked stock
        if ($row['stock_id'] != $product_id) {
            $sub = $this->query("
                SELECT quantity, stock_status_id
                FROM PREFIX_product
                WHERE product_id = " . $row['stock_id']
            );
            $stock = $sub->fetch();
            if (is_array($stock) && count($stock)) {
                $row['quantity'] = $stock['quantity'];
                $row['stock_status_id'] = $stock['stock_status_id'];
            }
        }
        Cache::set($cache, $row);
        return $row;
    }

    public function getProducts($data = array())
    {
        if (empty($data['start'])) {
            $data['start'] = 0;
        }
        if (empty($data['limit'])) {
            $data['limit'] = 25;
        }

        $cache = 'products.data.' . md5(json_encode($data));
        $result = Cache::find($cache);
        if (is_array($result) && count($result)) {
            return $result;
        }

        $values = array();
        $sql = "
            SELECT *, wc.value * p.weight AS std_weight
            FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)
            LEFT JOIN PREFIX_weight_class wc
                ON (p.weight_class_id = wc.weight_class_id)
            WHERE pd.language_id = " . (int)$this->config->get('language_id');

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE :filter_name";
            $values['filter_name'] = '%' . $data['filter_name'] . '%';
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model_2 LIKE :filter_model";
            $values['filter_model'] = '%' . $data['filter_model'] . '%';
        }

        if (!empty($data['filter_model_supplier'])) {
            $sql .= " AND p.model_supplier LIKE :filter_model_supplier";
            $values['filter_model_supplier'] = '%' . $data['filter_model_supplier'] . '%';
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE :filter_price";
            $values['filter_price'] = '%' . $data['filter_price'] . '%';
        }

        if (!empty($data['filter_price_from'])) {
            $sql .= " AND p.price > :filter_price_from";
            $values['filter_price_from'] = $data['filter_price_from'];
        }

        if (!empty($data['filter_price_to'])) {
            $sql .= " AND p.price < :filter_price_to";
            $values['filter_price_to'] = $data['filter_price_to'];
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = :filter_quantity";
            $values['filter_quantity'] = $data['filter_quantity'];
        }

        if (!empty($data['filter_stock_from'])) {
            $sql .= " AND p.quantity > " . intval($data['filter_stock_from']);
        }

        if (!empty($data['filter_stock_to'])) {
            $sql .= " AND p.quantity < " . intval($data['filter_stock_to']);
        }

        if (!empty($data['filter_stock'])) {
            $sql .= " AND p.quantity = " . intval($data['filter_stock']);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = " . (int)$data['filter_status'];
        }

        if (isset($data['filter_brand']) && !empty($data['filter_brand'])) {
            $sql .= " AND p.manufacturer_id = " . (int)$data['filter_brand'];
        }

        if (isset($data['filter_category']) && !empty($data['filter_category'])) {
            // Get all child-categories for selected filter-category
            $this->load->model('catalog/category');

            $childCategories = array($data['filter_category']);

            foreach ($this->model_catalog_category->getCategoriesAsList($data['filter_category']) as $subCategory) {
                $childCategories[] = $subCategory['category_id'];
            }

            $sql .= " AND p.product_id IN (SELECT product_id FROM PREFIX_product_to_category WHERE category_id IN (" . implode(',', $childCategories) . '))';
        }

        if (isset($data['filter_store'])) {
            $sql .= " AND p.product_id IN (SELECT product_id FROM PREFIX_product_to_store WHERE store_id = " . (int)$data['filter_store'] . ')';
        }

        if (isset($data['filter_group'])) {
            $sql .= " GROUP BY " . preg_replace('/^[a-zA-Z.]$/', '', $data['filter_group']);
        }
        else {
            $sql .= " GROUP BY p.product_id";
        }

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.model_2',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        $sql .= ' LIMIT ' . $data['start'] . ',' . $data['limit'];

        $data = $this->fetchAll($sql, $values);
        $return = array();

        foreach ($data as $key => $row) {
            // Fetch category
            $sub = $this->fetchAll("SELECT category_id FROM PREFIX_product_to_category WHERE product_id = " . $row['product_id']);
            $row['categories'] = array();
            foreach ($sub as $list) {
                $row['categories'][$list['category_id']] = $list['category_id'];
            }

            // Fetch store
            $sub = $this->fetchAll("SELECT store_id FROM PREFIX_product_to_store WHERE product_id = " . $row['product_id']);
            $row['stores'] = array();
            foreach ($sub as $list) {
                $row['stores'][$list['store_id']] = $list['store_id'];
                $row['store_id'] = $list['store_id'];
            }

            // Fetch linked stock
            if ($row['stock_id'] != $row['product_id']) {
                $sub = $this->query("
                    SELECT quantity, stock_status_id
                    FROM PREFIX_product
                    WHERE product_id = " . $row['stock_id']
                );
                $stock = $sub->fetch();
                if (is_array($stock) && count($stock)) {
                    $row['quantity'] = $stock['quantity'];
                    $row['stock_status_id'] = $stock['stock_status_id'];
                }
            }
            $return[$row['product_id']] = $row;
        }
        Cache::set($cache, $return);
        return $return;
    }

    public function getProductsByCategoryId($category_id)
    {
        return $this->fetchAll(
            "SELECT *
            FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON (p.product_id = pd.product_id)
            LEFT JOIN PREFIX_product_to_category p2c
                ON (p.product_id = p2c.product_id)
            WHERE pd.language_id = " . (int)$this->config->get('language_id') . "
                AND p2c.category_id = " . (int)$category_id . "
            ORDER BY pd.name ASC");
    }

    public function getLinkedProducts()
    {
        return $this->fetchAll("
            SELECT p.product_id, model, quantity, name
            FROM PREFIX_product AS p
            LEFT JOIN PREFIX_product_description AS pd
                ON pd.product_id = p.product_id
            WHERE p.product_id = p.stock_id
            GROUP BY p.stock_id"
        );
    }

    public function getProductDescriptions($product_id)
    {
        $product_description_data = array();

        $query = $this->fetchAll("
            SELECT *
            FROM PREFIX_product_description
            WHERE product_id = " . (int)$product_id);

        foreach ($query as $result) {
            $seo = $this->query("SELECT keyword FROM PREFIX_url_alias WHERE `query` = 'product_id=" . (int) $product_id . "' AND language_id = " . $result['language_id'])->fetch();
            $default_meta = $this->config->get('meta_template');
            if (isset($default_meta[$result['language_id']])) {
                $default_meta[$result['language_id']] = str_replace('%product%', $result['name'], $default_meta[$result['language_id']]);
            }
            else {
                $default_meta[$result['language_id']] = '';
            }
            $product_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'title'            => $result['title'],
                'description'      => $result['description'],
                'meta_keyword'     => $result['meta_keyword'],
                'meta_description' => !empty($result['meta_description']) ? $result['meta_description'] : $default_meta[$result['language_id']],
                'keyword'          => isset($seo['keyword']) ? $seo['keyword'] : '',
                'tag'              => $result['tag']
            );
        }

        return $product_description_data;
    }

    public function getProductCategories($product_id)
    {
        $product_category_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_product_to_category WHERE product_id = " . (int)$product_id);

        foreach ($query as $result) {
            $product_category_data[] = $result['category_id'];
        }

        return $product_category_data;
    }

    public function getProductAttributes($product_id)
    {
        $product_attributes = $this->fetchAll("SELECT attribute_id
            FROM PREFIX_product_attribute
            WHERE product_id = " . (int)$product_id . "
            GROUP BY attribute_id");

        $attributes = array();

        foreach ($product_attributes as $product_attribute) {
            $attributes[] = $product_attribute['attribute_id'];
        }

        return $attributes;
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
                WHERE option_id = " . (int)$productOption['option_id']);

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

        // Plan B.
        /*$productOptions = $this->fetchAll("SELECT *
            FROM PREFIX_product_option po, PREFIX_option o
            WHERE po.option_id = o.option_id
                AND po.product_id = " . (int)$productID);

        foreach ($productOptions as $k => $productOption) {
            // Grab language-data for options
            $optionDescriptions = $this->fetchAll("SELECT *
                FROM PREFIX_option_description od
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
            $optionValues = $this->fetchAll("SELECT pov.*, ov.option_value_id AS option_value_id, ov.option_id AS option_id
                FROM PREFIX_option_value ov
                LEFT JOIN PREFIX_product_option_value pov ON pov.option_value_id = ov.option_value_id
                WHERE ov.option_id = " . (int)$productOption['option_id']);

            foreach ($optionValues as $l => $optionValue) {
                // Grab language-data for option-values
                $optionValueDescriptions = $this->fetchAll("SELECT ovd.*
                    FROM PREFIX_option_value_description ovd
                    WHERE ovd.option_value_id = " . (int)$optionValue['option_value_id']);

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

        /*$product_option_data = array();

        $product_option_query = $this->fetchAll("
            SELECT *
            FROM PREFIX_product_option po
            LEFT JOIN PREFIX_option o
                ON (po.option_id = o.option_id)
            LEFT JOIN PREFIX_option_description od
                ON (o.option_id = od.option_id)
            WHERE po.product_id = " . (int)$product_id . "
                AND od.language_id = " . (int)$this->config->get('language_id'));

        foreach ($product_option_query as $product_option) {
            $product_option_value_data = array();

            $product_option_value_query = $this->fetchAll("
                SELECT pov.*, povd.name
                FROM PREFIX_product_option_value AS pov
                LEFT JOIN PREFIX_option_value_description AS povd
                    ON (pov.option_value_id = povd.option_value_id)
                WHERE product_option_id = " . (int)$product_option['product_option_id'] . "
                    AND povd.language_id = " . (int)$this->config->get('language_id'));

            foreach ($product_option_value_query as $product_option_value) {
                // Get all descriptions
                $option_value_descriptions = array();

                $option_value_description_query = $this->fetchAll("
                    SELECT language_id, name
                    FROM PREFIX_option_value_description
                    WHERE option_value_id = " . $product_option_value['option_value_id']);

                foreach ($option_value_description_query as $option_value_description) {
                    $option_value_descriptions[$option_value_description['language_id']]['name'] = $option_value_description['name'];
                }

                $product_option_value_data[] = array(
                    'product_option_value_id'   => $product_option_value['product_option_value_id'],
                    'option_id'                 => $product_option_value['option_id'],
                    'option_value_id'           => $product_option_value['option_value_id'],
                    'name'                      => html_entity_decode($product_option_value['name'], ENT_QUOTES, 'UTF-8'),
                    'quantity'                  => $product_option_value['quantity'],
                    'subtract'                  => $product_option_value['subtract'],
                    'price'                     => $product_option_value['price'],
                    'price_prefix'              => $product_option_value['price_prefix'],
                    'points'                    => $product_option_value['points'],
                    'points_prefix'             => $product_option_value['points_prefix'],
                    'weight'                    => $product_option_value['weight'],
                    'weight_prefix'             => $product_option_value['weight_prefix'],
                    'active'                    => $product_option_value['active'],
                    'option_value_description'  => $option_value_descriptions
                );
            }

            $product_option_data[$product_option['option_id']] = array(
                'product_option_id'    => $product_option['product_option_id'],
                'option_id'            => $product_option['option_id'],
                'name'                 => $product_option['name'],
                'type'                 => $product_option['type'],
                'product_option_value' => $product_option_value_data,
                'option_value'         => $product_option['option_value'],
                'required'             => $product_option['required']
            );
        }

        return $product_option_data;*/
    }

    public function getProductImages($product_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_product_image WHERE product_id = " . (int)$product_id);
    }

    public function getProductDiscounts($product_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_product_discount WHERE product_id = " . (int)$product_id . " ORDER BY quantity, priority, price");
    }

    public function getProductSpecials($product_id)
    {
        $cache = 'products.specials.' . $product_id;
        $return = Cache::find($cache);
        if (is_array($return) && count($return)) {
            return $return;
        }

        $rows = $this->fetchAll("SELECT * FROM PREFIX_product_special WHERE product_id = " . (int)$product_id . " ORDER BY priority, price");
        $return = array();
        foreach ($rows as $list) {
            $return[$list['customer_group_id']] = $list;
        }

        Cache::set($cache, $return);
        return $return;
    }

    public function getProductRewards($product_id)
    {
        return;
    }

    public function getProductDownloads($product_id)
    {
        $downloads = $this->fetchAll("SELECT * FROM PREFIX_product_to_download WHERE product_id = " . (int)$product_id);
        $productDownloadIDs = array();

        foreach ($downloads as $download) {
            $productDownloadIDs[] = $download['download_id'];
        }

        return $productDownloadIDs;
    }

    public function getProductStores($product_id)
    {
        $cache = 'products.stores.' . $product_id;
        $return = Cache::find($cache);
        if (is_array($return) && count($return)) {
            return $return;
        }
        $product_store_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_product_to_store WHERE product_id = " . (int)$product_id);

        foreach ($query as $result) {
            $product_store_data[] = $result['store_id'];
        }
        Cache::set($cache, $product_store_data);
        return $product_store_data;
    }

    public function getProductRelated($product_id)
    {
        $product_related_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_product_related WHERE product_id = " . (int)$product_id);

        foreach ($query as $result) {
            $product_related_data[$result['related_id']] = $result['related_id'];
        }

        return $product_related_data;
    }

    public function getTotalProducts($data = array())
    {
        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM PREFIX_product p LEFT JOIN PREFIX_product_description pd ON (p.product_id = pd.product_id)";
        $values = array();

        if (!empty($data['filter_name'])) {
            $sqlFilters[] = "pd.name LIKE :filter_name";
            $values['filter_name'] = '%' . $data['filter_name'] . '%';
        }

        if (!empty($data['filter_model'])) {
            $sqlFilters[] = "p.model_2 LIKE :filter_model";
            $values['filter_model'] = '%' . $data['filter_model'] . '%';
        }

        if (!empty($data['filter_model_supplier'])) {
            $sqlFilters[] = "p.model_supplier LIKE :filter_model_supplier";
            $values['filter_model_supplier'] = '%' . $data['filter_model_supplier'] . '%';
        }

        if (!empty($data['filter_price'])) {
            $sqlFilters[] = "p.price LIKE :filter_price";
            $values['filter_price'] = '%' . $data['filter_price'] . '%';
        }

        if (!empty($data['filter_price_from'])) {
            $sqlFilters[] = "p.price > :filter_price_from";
            $values['filter_price_from'] = $data['filter_price_from'];
        }

        if (!empty($data['filter_price_to'])) {
            $sqlFilters[] = "p.price < :filter_price_to";
            $values['filter_price_to'] = $data['filter_price_to'];
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sqlFilters[] = "p.quantity = :filter_quantity";
            $values['filter_quantity'] = $data['filter_quantity'];
        }

        if (!empty($data['filter_stock_from'])) {
            $sqlFilters[] = "p.quantity > " . intval($data['filter_stock_from']);
        }

        if (!empty($data['filter_stock_to'])) {
            $sqlFilters[] = "p.quantity < " . intval($data['filter_stock_to']);
        }

        if (!empty($data['filter_stock'])) {
            $sqlFilters[] = "p.quantity = " . intval($data['filter_stock']);
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sqlFilters[] = "p.status = " . (int)$data['filter_status'];
        }

        if (isset($data['filter_brand']) && !empty($data['filter_brand'])) {
            $sqlFilters[] = "p.manufacturer_id = " . (int)$data['filter_brand'];
        }

        if (isset($data['filter_category']) && !empty($data['filter_category'])) {
            // Get all child-categories for selected filter-category
            $this->load->model('catalog/category');

            $childCategories = array($data['filter_category']);

            foreach ($this->model_catalog_category->getCategoriesAsList($data['filter_category']) as $subCategory) {
                $childCategories[] = $subCategory['category_id'];
            }

            $sql .= " AND p.product_id IN (SELECT product_id FROM PREFIX_product_to_category WHERE category_id IN (" . implode(',', $childCategories) . '))';
        }

        if (isset($data['filter_store'])) {
            $sqlFilters[] = "p.product_id IN (SELECT product_id FROM PREFIX_product_to_store WHERE store_id = " . (int)$data['filter_store'] . ')';
        }

        if (!empty($sqlFilters)) {
            $sql .= ' WHERE ' . implode(' AND ', $sqlFilters);
        }

        $query = $this->query($sql, $values)->fetch();

        return isset($query['total']) ? $query['total'] : 0;
    }

    public function getTotalProductsByTaxClassId($tax_class_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product WHERE tax_class_id = " . (int)$tax_class_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByStockStatusId($stock_status_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product WHERE stock_status_id = " . (int)$stock_status_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByWeightClassId($weight_class_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product WHERE weight_class_id = " . (int)$weight_class_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByLengthClassId($length_class_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product WHERE length_class_id = " . (int)$length_class_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByDownloadId($download_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product_to_download WHERE download_id = " . (int)$download_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByManufacturerId($manufacturer_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product WHERE manufacturer_id = " . (int)$manufacturer_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByAttributeId($attribute_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product_attribute WHERE attribute_id = " . (int)$attribute_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByOptionId($option_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product_option WHERE option_id = " . (int)$option_id)->fetch();
        return $query['total'];
    }

    public function getTotalProductsByLayoutId($layout_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product_to_layout WHERE layout_id = " . (int)$layout_id)->fetch();
        return $query['total'];
    }

    public function checkForImageUpload($product_id, $main = true, $name)
    {
        $json = array();
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if ($main) {
            if (isset($this->request->files['image']) && $this->request->files['image']['tmp_name']) {
                $filename = basename(html_entity_decode($this->request->files['image']['name'], ENT_QUOTES, 'UTF-8'));

                if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                $directory = rtrim(DIR_IMAGE . $this->request->post['product_store'] . '/', '/');
                if (!is_dir($directory)) {
                    $result = mkdir($directory);
                    if (!$result) {
                        $json['error'] = $this->language->get('error_directory');
                    }
                }

                $directory .= '/' . $this->request->post['product_category'];
                if (!is_dir($directory) && !mkdir($directory)) {
                    $json['error'] = $this->language->get('error_directory');
                }

                if ($this->request->files['image']['size'] > 10000000) {
                    $json['error'] = $this->language->get('error_file_size');
                }

                $allowed = array(
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/x-png',
                    'image/gif',
                    'application/x-shockwave-flash'
                );

                if (!in_array($this->request->files['image']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_file_type');
                }

                $allowed = array(
                    '.jpg',
                    '.jpeg',
                    '.gif',
                    '.png',
                    '.flv'
                );

                if (!in_array(strtolower(strrchr($filename, '.')), $allowed)) {
                    $json['error'] = $this->language->get('error_file_type');
                }

                if ($this->request->files['image']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'error_upload_' . $this->request->files['image']['error'];
                }
            } else {
                //$json['error'] = $this->language->get('error_file');
                return false;
            }

            if (!isset($json['error'])) {
                $tmp = explode('.', $filename);
                $tmp[0] = $name . '-' . $product_id;
                $filename = implode('.', $tmp);
                if (@move_uploaded_file($this->request->files['image']['tmp_name'], $directory . '/' . $filename)) {
                    return rtrim(str_replace(DIR_IMAGE, '', $directory), '/') . '/' . $filename;
                }
            }

            if (isset($json['error'])) {
                $this->session->data['warning'] = $json['error'];
            }
        }
        else {
            $j = 0;
            $return = array();
            if (isset($this->request->post['product_image'])) {
                foreach($this->request->post['product_image'] as $image) {
                    $return[] = $image;
                    $j++;
                }
            }
            if (isset($this->request->files['product_images']) && count($this->request->files['product_images']['tmp_name'])) {
                foreach ($this->request->files['product_images']['tmp_name'] as $i => $tmp_name) {
                    if (!empty($this->request->files['product_images']['error'][$i])) {
                        continue;
                    }
                    $j++;

                    $filename = basename(html_entity_decode($this->request->files['product_images']['name'][$i], ENT_QUOTES, 'UTF-8'));

                    if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                        $json['error'] = $this->language->get('error_filename');
                    }

                    $directory = rtrim(DIR_IMAGE . $this->request->post['product_store'] . '/' . $this->request->post['product_category'], '/');

                    if (!is_dir($directory) && !mkdir($directory)) {
                        $json['error'] = $this->language->get('error_directory');
                    }

                    if ($this->request->files['product_images']['size'][$i] > 100000000) {
                        $json['error'] = $this->language->get('error_file_size');
                    }

                    $allowed = array(
                        'image/jpeg',
                        'image/pjpeg',
                        'image/png',
                        'image/x-png',
                        'image/gif',
                        'application/x-shockwave-flash'
                    );

                    if (!in_array($this->request->files['product_images']['type'][$i], $allowed)) {
                        $json['error'] = $this->language->get('error_file_type');
                    }

                    $allowed = array(
                        '.jpg',
                        '.jpeg',
                        '.gif',
                        '.png',
                        '.flv'
                    );

                    if (!in_array(strtolower(strrchr($filename, '.')), $allowed)) {
                        $json['error'] = $this->language->get('error_file_type');
                    }

                    if (!isset($json['error'])) {
                        $tmp = explode('.', $filename);
                        $tmp[0] = $name . '-' . $product_id . '-' . $j;
                        $filename = implode('.', $tmp);
                        if (@move_uploaded_file($this->request->files['product_images']['tmp_name'][$i], $directory . '/' . $filename)) {
                            $return[] = rtrim(str_replace(DIR_IMAGE, '', $directory), '/') . '/' . $filename;
                        }
                    }

                }

            }
            if (isset($json['error'])) {
                $this->session->data['error'] = $json['error'];
            }
            return $return;

        }

    }
}
