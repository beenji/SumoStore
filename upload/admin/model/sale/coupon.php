<?php
namespace Sumo;
class ModelSaleCoupon extends Model
{
    public function addCoupon($data)
    {
        $this->query("INSERT INTO PREFIX_coupon SET date_added = :date", array('date' => date('Y-m-d H:i:s')));
        $coupon_id = $this->lastInsertId();
        return $this->editCoupon($coupon_id, $data);
    }

    public function editCoupon($coupon_id, $data)
    {
        $this->query(
            "UPDATE PREFIX_coupon
            SET name        = :name,
                code        = :code,
                discount    = :discount,
                tax_percentage = :tax_percentage,
                type        = :type,
                total       = :total,
                logged      = :logged,
                shipping    = :shipping,
                date_start  = :date_start,
                date_end    = :date_end,
                uses_total  = :uses_total,
                uses_customer = :uses_customer,
                status      = :status
            WHERE coupon_id = :id",
            array(
                'name'      => $data['name'],
                'code'      => $data['code'],
                'discount'  => (float)$data['discount'],
                'tax_percentage' => (float)$data['tax_percentage'],
                'type'      => $data['type'],
                'total'     => (float)$data['total'],
                'logged'    => (int)$data['logged'],
                'shipping'  => (int)$data['shipping'],
                'date_start'=> Formatter::dateReverse($data['date_start']),
                'date_end'  => Formatter::dateReverse($data['date_end']),
                'uses_total'=> (int)$data['uses_total'],
                'uses_customer' => (int)$data['uses_customer'],
                'status'    => (int)$data['status'],
                'id'        => $coupon_id
            )
        );

        $this->query("DELETE FROM PREFIX_coupon_product WHERE coupon_id = " . (int)$coupon_id);
        if (isset($data['coupon_product'])) {
            foreach ($data['coupon_product'] as $product_id) {
                $this->query("INSERT INTO PREFIX_coupon_product SET coupon_id = " . (int)$coupon_id . ", product_id = " . (int)$product_id);
            }
        }

        $this->query("DELETE FROM PREFIX_coupon_category WHERE coupon_id = " . (int)$coupon_id);
        if (isset($data['coupon_category'])) {
            foreach ($data['coupon_category'] as $category_id) {
                $this->query("INSERT INTO PREFIX_coupon_category SET coupon_id = " . (int)$coupon_id . ", category_id = " . (int)$category_id);
            }
        }
    }

    public function deleteCoupon($coupon_id)
    {
        $this->query("DELETE FROM PREFIX_coupon WHERE coupon_id = " . (int)$coupon_id);
        $this->query("DELETE FROM PREFIX_coupon_product WHERE coupon_id = " . (int)$coupon_id);
        $this->query("DELETE FROM PREFIX_coupon_category WHERE coupon_id = " . (int)$coupon_id);
        $this->query("DELETE FROM PREFIX_coupon_history WHERE coupon_id = " . (int)$coupon_id);
    }

    public function getCoupon($coupon_id)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_coupon WHERE coupon_id = " . (int)$coupon_id)->fetch();
    }

    public function getCouponByCode($code)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_coupon WHERE code = :code", array('code' => $code))->fetch();
    }

    public function getCoupons($data = array())
    {
        return $this->fetchAll("SELECT coupon_id, name, code, discount, type, date_start, date_end, status FROM PREFIX_coupon");
    }

    public function getCouponProducts($coupon_id)
    {
        $coupon_product_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_coupon_product WHERE product_id > 0 AND coupon_id = " . (int)$coupon_id);

        foreach ($query as $result) {
            $coupon_product_data[] = $result['product_id'];
        }

        return $coupon_product_data;
    }

    public function getCouponCategories($coupon_id)
    {
        $coupon_category_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_coupon_category WHERE category_id > 0 AND coupon_id = " . (int)$coupon_id);

        foreach ($query as $result) {
            $coupon_category_data[] = $result['category_id'];
        }

        return $coupon_category_data;
    }

    public function getTotalCoupons()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_coupon")->fetch();

        return $query['total'];
    }

    public function getCouponHistories($coupon_id, $start = 0, $limit = 10, $order_id = 0)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        if ($order_id > 0) {
            $orderSQL = ' AND ch.order_id = ' . (int)$order_id;
        } else {
            $orderSQL = '';
        }

        return $this->fetchAll(
            "SELECT ch.order_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, ch.amount, ch.date_added
            FROM PREFIX_coupon_history AS ch
            LEFT JOIN PREFIX_customer AS c
                ON (ch.customer_id = c.customer_id)
            WHERE ch.coupon_id = " . (int)$coupon_id . $orderSQL . "
            ORDER BY ch.date_added DESC
            LIMIT " . (int)$start . "," . (int)$limit);
    }

    public function getTotalCouponHistories($coupon_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_coupon_history WHERE coupon_id = " . (int)$coupon_id)->fetch();

        return $query['total'];
    }

    public function getTotalCouponHistoriesByCustomer($coupon_id, $customer_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_coupon_history WHERE coupon_id = " . (int)$coupon_id . " AND customer_id = " . (int)$customer_id)->fetch();

        return $query['total'];
    }
}
