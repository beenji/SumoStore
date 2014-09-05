<?php
namespace Inventory;
use Sumo;
use App;

class ModelInventory extends App\Model
{
    public function getProducts($filter = array())
    {
        $cache = 'inventory.getproducts.' . md5(json_encode($filter));
        $data = Sumo\Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $sqlFilter = $this->filterToSQL($filter);

        if (!empty($sqlFilter['sql'])) {
            $sqlFilter['sql'] = ' AND ' . $sqlFilter['sql'];
        }

        $sqlFilter['sqlValues']['languageID'] = (int)$this->config->get('language_id');

        // Limit results?
        if (isset($filter['start']) && isset($filter['limit'])) {
            $limit = ' LIMIT :start, :limit';
            $sqlFilter['sqlValues']['start'] = (int)$filter['start'];
            $sqlFilter['sqlValues']['limit'] = (int)$filter['limit'];
        }
        else {
            $limit = '';
        }

        $products = $this->query('SELECT p.*, pd.name FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON p.product_id = pd.product_id
            WHERE pd.language_id = :languageID' . $sqlFilter['sql'] . '
            ORDER BY name ASC' . $limit, $sqlFilter['sqlValues'])->fetchAll();

        foreach ($products as $k => $product) {
            if ($product['stock_id'] != 0) {
                // Get linked stock
                $linkedStock = $this->query('SELECT quantity FROM PREFIX_product WHERE product_id = :productID', array('productID' => $product['stock_id']))->fetch();
                $products[$k]['quantity'] = $linkedStock['quantity'];
            }
        }
        Sumo\Cache::set($cache, $products);
        return $products;
    }

    public function getTotalProducts($filter = array())
    {
        $cache = 'inventory.products.total.' . md5(json_encode($filter));
        $data = Sumo\Cache::find($cache);
        if ($data) {
            return $data;
        }

        $sqlFilter = $this->filterToSQL($filter);

        if (!empty($sqlFilter['sql'])) {
            $sqlFilter['sql'] = ' AND ' . $sqlFilter['sql'];
        }

        $sqlFilter['sqlValues']['languageID'] = (int)$this->config->get('language_id');

        $total = 0;
        foreach ($this->fetchAll("SELECT p.product_id, quantity, cost_price, stock_id, tax_percentage
            FROM PREFIX_product AS p
            LEFT JOIN PREFIX_product_description pd
                ON p.product_id = pd.product_id
            WHERE pd.language_id = :languageID AND status = 1 " . $sqlFilter['sql'], $sqlFilter['sqlValues']) as $item) {
            // Check for options
            $options = $this->fetchAll("SELECT option_id FROM PREFIX_product_option WHERE product_id = :id", array('id' => $item['product_id']));
            if (count($options)) {
                foreach ($options as $option) {
                    foreach ($this->fetchAll("SELECT quantity FROM PREFIX_product_option_value WHERE active = 1 AND option_id = :id", array('id' => $option['option_id'])) as $value) {
                        $total += $value['quantity'];
                    }
                }
            }
            else {
                if ($item['stock_id'] == $item['product_id']) {
                    $total += $item['quantity'];
                }
                else {
                    $linked = $this->query("SELECT quantity FROM PREFIX_product WHERE product_id = :id", array('id' => $item['stock_id']))->fetch();
                    $total += $linked['quantity'];
                }
            }
        }
        Sumo\Cache::set($cache, $total);
        return $total;
    }

    public function getTotalProductsInView($filter = array())
    {
        $sqlFilter = $this->filterToSQL($filter);

        if (!empty($sqlFilter['sql'])) {
            $sqlFilter['sql'] = ' AND ' . $sqlFilter['sql'];
        }

        $sqlFilter['sqlValues']['languageID'] = (int)$this->config->get('language_id');

        $query = $this->query('SELECT COUNT(p.product_id) AS total
            FROM PREFIX_product p
            LEFT JOIN PREFIX_product_description pd
                ON p.product_id = pd.product_id
            WHERE pd.language_id = :languageID' . $sqlFilter['sql'], $sqlFilter['sqlValues'])->fetch();

        return $query['total'];

    }

    public function getTotalProductsValue($filter = array())
    {
        $cache = 'inventory.product.value.' . md5(json_encode($filter));
        $data = Sumo\Cache::find($cache);
        if ($data) {
            return $data;
        }

        $sqlFilter = $this->filterToSQL($filter);

        if (!empty($sqlFilter['sql'])) {
            $sqlFilter['sql'] = ' AND ' . $sqlFilter['sql'];
        }

        $sqlFilter['sqlValues']['languageID'] = (int)$this->config->get('language_id');

        $total = 0;
        $sql = "SELECT p.product_id, quantity, cost_price, stock_id, tax_percentage
            FROM PREFIX_product AS p
            LEFT JOIN PREFIX_product_description pd
                ON p.product_id = pd.product_id
            WHERE pd.language_id = :languageID AND status = 1 " . $sqlFilter['sql'];
        $values = $sqlFilter['sqlValues'];
        foreach ($this->fetchAll($sql, $values) as $item) {
            // Check for options
            $options = $this->fetchAll("SELECT option_id FROM PREFIX_product_option WHERE product_id = :id", array('id' => $item['product_id']));
            if (count($options)) {
                foreach ($options as $option) {
                    foreach ($this->fetchAll("SELECT quantity FROM PREFIX_product_option_value WHERE active = 1 AND option_id = :id", array('id' => $option['option_id'])) as $value) {
                        $total += ($item['cost_price'] * $value['quantity']);
                    }
                }
            }
            else {
                if ($item['stock_id'] == $item['product_id']) {
                    $total += ($item['cost_price'] * $item['quantity']);
                }
                else {
                    $linked = $this->query("SELECT quantity, cost_price FROM PREFIX_product WHERE product_id = :id", array('id' => $item['stock_id']))->fetch();
                    if ((empty($item['cost_price']) || $item['cost_price'] == 0.00) && (!empty($linked['cost_price']) && $linked['cost_price'] >= 0.00)) {
                        $item['cost_price'] = $linked['cost_price'];
                    }
                    $total += ($item['cost_price'] * $linked['quantity']);
                }
            }
        }

        Sumo\Cache::set($cache, $total);
        return $total;
    }

    public function getTotalProductsPrice($filter = array())
    {
        $cache = 'inventory.product.price.' . md5(json_encode($filter));
        $data = Sumo\Cache::find($cache);
        if ($data) {
            return $data;
        }

        $sqlFilter = $this->filterToSQL($filter);

        if (!empty($sqlFilter['sql'])) {
            $sqlFilter['sql'] = ' AND ' . $sqlFilter['sql'];
        }

        $sqlFilter['sqlValues']['languageID'] = (int)$this->config->get('language_id');

        $total = 0;
        foreach ($this->fetchAll("SELECT p.product_id, quantity, price, cost_price, stock_id, tax_percentage
            FROM PREFIX_product AS p
            LEFT JOIN PREFIX_product_description pd
                ON p.product_id = pd.product_id
            WHERE pd.language_id = :languageID AND status = 1 " . $sqlFilter['sql'], $sqlFilter['sqlValues']) as $item) {
            // Check for options
            $options = $this->fetchAll("SELECT option_id FROM PREFIX_product_option WHERE product_id = :id", array('id' => $item['product_id']));
            if (count($options)) {
                foreach ($options as $option) {
                    foreach ($this->fetchAll("SELECT price, price_prefix, quantity FROM PREFIX_product_option_value WHERE active = 1 AND option_id = :id", array('id' => $option['option_id'])) as $value) {
                        if ($value['price_prefix'] == '-') {
                            $price = $item['price'] - $value['price'];
                        }
                        else {
                            $price = $item['price'] + $value['price'];
                        }

                        $total += ($price * $value['quantity']);
                    }
                }
            }
            else {
                if ($item['stock_id'] == $item['product_id']) {
                    $total += ($item['price'] * $item['quantity']);
                }
                else {
                    $linked = $this->query("SELECT quantity FROM PREFIX_product WHERE product_id = :id", array('id' => $item['stock_id']))->fetch();
                    $total += ($item['price'] * $linked['quantity']);
                }
            }
        }

        Sumo\Cache::set($cache, $total);
        return $total;
    }

    public function getCurrencies()
    {
        return $this->query("SELECT * FROM PREFIX_currency")->fetchAll();
    }

    public function getCategories($parentID = 0, $level = 0)
    {
        $categories = array();

        $query = $this->query("SELECT *
            FROM PREFIX_category c
            LEFT JOIN PREFIX_category_description cd
                ON c.category_id = cd.category_id
            LEFT JOIN PREFIX_category_to_store cts
                ON c.category_id = cts.category_id
            WHERE cd.language_id = :languageID
                AND c.parent_id = :parentID
            ORDER BY sort_order ASC", array(
                'languageID'    => (int)$this->config->get('language_id'),
                'parentID'      => (int)$parentID));
        foreach ($query->fetchAll() as $category) {
            if ($level == 0) {
                $categories[$category['store_id']][] = array_merge($category, array('level' => $level));

                // Add subcategories?
                $categories[$category['store_id']] = array_merge($categories[$category['store_id']], $this->getCategories($category['category_id'], $level + 1));
            }
            else {
                $categories[] = array_merge($category, array('level' => $level));

                // Add subcategories?
                $categories = array_merge($categories, $this->getCategories($category['category_id'], $level + 1));
            }
        }

        return $categories;
    }

    protected function filterToSQL($filter)
    {
        $sql = $sqlValues = array();

        // Name-filter
        if (isset($filter['name']) && !empty($filter['name'])) {
            $sql[] = "pd.name LIKE :name";
            $sqlValues['name'] = '%' . $filter['name'] . '%';
        }

        // Quantity-filter
        if (!empty($filter['quantity_from'])) {
            $sql[] = 'quantity >= :quantityFrom';
            $sqlValues['quantityFrom'] =  (int)$filter['quantity_from'];
        }
        elseif (!empty($filter['quantity_to'])) {
            $sql[] = 'quantity <= :quantityTo';
            $sqlValues['quantityTo'] = (int)$filter['quantity_to'];
        }
        elseif (!empty($filter['quantity'])) {
            $sql[] = 'quantity = :quantity';
            $sqlValues['quantity'] = (int)$filter['quantity'];
        }

        // Category-filter
        if (!empty($filter['category'])) {
            $sql[] = 'p.product_id IN (SELECT product_id FROM PREFIX_product_to_category WHERE category_id = :categoryID)';
            $sqlValues['categoryID'] = (int)$filter['category'];
        }

        // Store-filter
        if (isset($filter['store']) && $filter['store'] != '') {
            $sql[] = 'p.product_id IN (SELECT product_id FROM PREFIX_product_to_store WHERE store_id = :storeID)';
            $sqlValues['storeID'] = (int)$filter['store'];
        }

        return array('sql' => implode(' AND ', $sql), 'sqlValues' => $sqlValues);
    }
}
