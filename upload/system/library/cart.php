<?php
class Cart
{
    private $config;
    private $data = array();

    public function __construct($registry)
    {
        $this->config = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
        $this->tax = $registry->get('tax');
        $this->weight = $registry->get('weight');
        $this->load = $registry->get('load');

        if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
            $this->session->data['cart'] = array();
        }
    }

    public function getProducts()
    {
        if (!$this->data) {
            $this->load->model('catalog/product');
            foreach ($this->session->data['cart'] as $key => $quantity) {
                $product = explode(':', $key);
                $product_id = $product[0];
                $stock = true;

                // Options
                if (isset($product[1])) {
                    $options = unserialize(base64_decode($product[1]));
                }
                else {
                    $options = array();
                }

                // Fetch product, not from cache but realtime
                $product = Sumo\Database::query(
                    "SELECT *
                    FROM PREFIX_product p
                    LEFT JOIN PREFIX_product_description pd
                        ON (p.product_id = pd.product_id)
                    WHERE p.product_id = :pid
                        AND pd.language_id = :lid
                        AND p.date_available <= NOW()
                        AND p.status = 1",
                    array(
                        'pid' => $product_id,
                        'lid' => $this->config->get('language_id')
                    )
                )->fetch();
                if (is_array($product) && count($product)) {
                    $option_price   = 0;
                    $option_points  = 0;
                    $option_weight  = 0;
                    $option_data    = array();

                    foreach ($options as $option_id => $value_id) {
                        if (!isset($option_data[$option_id])) {
                            $check = Sumo\Database::query("SELECT name FROM PREFIX_product_option_description WHERE option_id = :id AND language_id = :lid", array('id' => $option_id, 'lid' => $this->config->get('language_id')))->fetch();
                            $option_data[$option_id]['name'] = $check['name'];
                            $option_data[$option_id]['options'] = array();
                        }
                        $option_data_raw = Sumo\Database::query(
                            "SELECT name, quantity, subtract, price, price_prefix, weight, weight_prefix, name
                            FROM PREFIX_product_option_value AS pov
                            LEFT JOIN PREFIX_product_option_value_description AS povd
                                ON pov.value_id = povd.value_id
                            WHERE pov.value_id  = :value_id
                                AND language_id = :lid",
                            array(
                                'value_id'      => $value_id,
                                'lid'           => $this->config->get('language_id')
                            )
                        )->fetch();
                        $option_data[$option_id]['options'][$value_id] = $option_data_raw;
                    }

                    if ($this->customer->isLogged()) {
                        $customer_group_id = $this->customer->getCustomerGroupId();
                    }
                    else {
                        $customer_group_id = $this->config->get('customer_group_id');
                    }

                    $price = $product['price'];

                    // Product Discounts
                    $discount_quantity = 0;

                    foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
                        $product_2 = explode(':', $key_2);

                        if ($product_2[0] == $product_id) {
                            $discount_quantity += $quantity_2;
                        }
                    }

                    $product_discount_query = Sumo\Database::query(
                        "SELECT price
                        FROM PREFIX_product_discount
                        WHERE product_id = :pid
                            AND customer_group_id = :cgid
                            AND quantity <= :q
                            AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))
                        ORDER BY quantity DESC, priority ASC, price ASC
                        LIMIT 1",
                        array(
                            'pid'   => $product_id,
                            'cgid'  => $customer_group_id,
                            'q'     => $discount_quantity
                        )
                    )->fetch();

                    if (count($product_discount_query) && !empty($product_discount_query['price'])) {
                        $price = $product_discount_query['price'];
                    }

                    // Product Specials
                    $product_special_query = Sumo\Database::query(
                        "SELECT price
                        FROM PREFIX_product_special
                        WHERE product_id = :pid
                            AND customer_group_id = :cgid
                            AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))
                        ORDER BY priority ASC, price ASC
                        LIMIT 1",
                        array(
                            'pid'   => $product_id,
                            'cgid'  => $customer_group_id
                        )
                    )->fetch();

                    if (count($product_special_query) && !empty($product_special_query['price'])) {
                        $price = $product_special_query['price'];
                    }

                    // Downloads
                    $download_data = array();

                    $download_query = Sumo\Database::fetchAll(
                        "SELECT *
                        FROM PREFIX_product_to_download p2d
                        LEFT JOIN PREFIX_download d
                            ON (p2d.download_id = d.download_id)
                        LEFT JOIN PREFIX_download_description dd
                            ON (d.download_id = dd.download_id)
                        WHERE p2d.product_id = :pid
                            AND dd.language_id = :lid",
                        array(
                            'pid'   => $product_id,
                            'lid'   => $this->config->get('language_id')
                        )
                    );

                    foreach ($download_query as $download) {
                        $download_data[] = $download;
                    }

                    if (count($option_data)) {
                        foreach ($option_data as $data) {
                            foreach ($data['options'] as $option) {
                                if (!$option['quantity'] || $product['quantity'] < $option['quantity']) {
                                    $stock = false;
                                }
                                if (!empty($option['price'])) {
                                    if ($option['price_prefix'] == '-') {
                                        $price -= $option['price'];
                                    }
                                    else {
                                        $price += $option['price'];
                                    }
                                }
                            }
                        }
                    }
                    else {
                        // Check if stock is linked
                        if ($product['stock_id'] != $product['product_id']) {
                            $stockData = Sumo\Database::query("SELECT quantity FROM PREFIX_product WHERE product_id = :id", array('id' => $product['stock_id']))->fetch();
                            $product['quantity'] = $stockData['quantity'];
                        }

                        // Stock
                        if (!$product['quantity'] || ($product['quantity'] < $quantity)) {
                            $stock = false;
                        }
                    }

                    // Image
                    if (empty($product['image'])) {
                        $product['image'] = 'no_image.jpg';
                    }

                    $this->data[$key] = array(
                        'key'               => $key,
                        'product_id'        => $product['product_id'],
                        'name'              => $product['name'],
                        'model'             => $product['model'],
                        'shipping'          => $product['shipping'],
                        'image'             => $product['image'],
                        'options'           => $options,
                        'options_data'      => $option_data,
                        'download'          => $download_data,
                        'quantity'          => $quantity,
                        'minimum'           => $product['minimum'],
                        'subtract'          => $product['subtract'],
                        'stock'             => $stock,
                        'stock_id'          => $product['stock_id'],
                        'in_stock'          => $product['quantity'],
                        'price'             => ($price + $option_price),
                        'total'             => round($price + $option_price, 2) * $quantity,
                        'points'            => ($product['points'] ? ($product['points'] + $option_points) * $quantity : 0),
                        //'tax_class_id'    => $product['tax_class_id'],
                        'tax_percentage'    => $product['tax_percentage'],
                        'weight'            => ($product['weight'] + $option_weight) * $quantity,
                        'weight_class_id'   => $product['weight_class_id'],
                        'length'            => $product['length'],
                        'width'             => $product['width'],
                        'height'            => $product['height'],
                        'length_class_id'   => $product['length_class_id']
                    );
                }
                else {
                    $this->remove($key);
                }
            }
        }

        return $this->data;
    }

    public function add($product_id, $qty = 1, $option = array())
    {
        if (!$option) {
            $key = (int)$product_id;
        }
        else {
            $key = (int)$product_id . ':' . base64_encode(serialize($option));
        }

        if ((int)$qty && ((int)$qty > 0)) {
            if (!isset($this->session->data['cart'][$key])) {
                $this->session->data['cart'][$key] = (int)$qty;
            }
            else {
                $this->session->data['cart'][$key] += (int)$qty;
            }
        }

        $this->data = array();
    }

    public function update($key, $qty)
    {
        if ((int)$qty && ((int)$qty > 0)) {
            $this->session->data['cart'][$key] = (int)$qty;
        }
        else {
            $this->remove($key);
        }

        $this->data = array();
    }

    public function remove($key)
    {
        if (isset($this->session->data['cart'][$key])) {
            unset($this->session->data['cart'][$key]);
        }

        $this->data = array();
    }

    public function clear()
    {
        $this->session->data['cart'] = array();
        $this->data = array();
    }

    public function getWeight()
    {
        $weight = 0;

        foreach ($this->getProducts() as $product) {
            if ($product['shipping']) {

                $tmpWeight = $this->weight->convert($product['weight'], $product['weight_class_id'], 1);
                if (count($product['options_data'])) {
                    foreach ($product['options_data'] as $data) {
                        foreach ($data['option'] as $option) {
                            if ($option['weight']) {
                                if ($option['weight_prefix'] == '-') {
                                    $tmpWeight -= $option['weight'];
                                }
                                else {
                                    $tmpWeight += $option['weight'];
                                }
                            }
                        }
                    }
                }

                $weight += $tmpWeight;
            }
        }

        return $weight;
    }

    public function getSubTotal()
    {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product['total'];
        }

        return $total;
    }

    public function getTaxes()
    {
        $total = 0;
        foreach ($this->getProducts() as $product) {
            $total += ($product['price'] / 100 * $product['tax_percentage']) * $product['quantity'];
        }

        return round($total, 2);
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getProducts() as $product) {
            $price = round($product['price'] + $product['price'] / 100 * $product['tax_percentage'], 2);
            $total += $price * $product['quantity'];
        }

        return round($total, 2);
    }

    public function countProducts()
    {
        $product_total = 0;

        $products = $this->getProducts();
        foreach ($products as $product) {
            $product_total += $product['quantity'];
        }

        return $product_total;
    }

    public function hasProducts()
    {
        return count($this->session->data['cart']);
    }

    public function hasStock()
    {
        $products = $this->getProducts();

        $stocks = array();
        $requested = array();

        foreach ($products as $key => $product) {
            if (count($product['options_data'])) {
                foreach ($product['options_data'] as $data) {
                    foreach ($data['options'] as $option) {
                        if (!$option['quantity']) {
                            return false;
                        }
                        if ($product['quantity'] > $option['quantity']) {
                            return false;
                        }
                    }
                }
            }
            else {
                $stocks[$product['stock_id']] = $product['in_stock'];
                $requested[$product['stock_id']] += $product['quantity'];
            }
        }

        foreach ($requested as $id => $amount) {
            if ($amount > $stocks[$id]) {
                return false;
            }
        }

        return true;
    }

    public function hasShipping()
    {
        foreach ($this->getProducts() as $product) {
            if (!$product['shipping']) {
                return false;
            }
        }

        return true;
    }

    public function hasDownload()
    {
        foreach ($this->getProducts() as $product) {
            if ($product['download']) {
                return true;
            }
        }

        return false;
    }
}
