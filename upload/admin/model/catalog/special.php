<?php
namespace Sumo;
class ModelCatalogSpecial extends Model
{
    public function addSpecial($data)
    {
        $this->query('INSERT INTO PREFIX_product_special (product_id, customer_group_id, priority, price, discount, discount_suffix, date_start, date_end) VALUES (
            :productID,
            0,
            1,
            :price,
            :discount,
            :discountSuffix,
            :dateStart,
            :dateEnd)', array(
            'price'             => $data['price'],
            'discount'          => $data['discount'],
            'discountSuffix'    => $data['discount_suffix'],
            'dateStart'         => $data['date_start'],
            'dateEnd'           => $data['date_end'],
            'productID'         => $data['product_id']
        ));
    }

    public function editSpecial($productSpecialID, $data)
    {
        $this->query('UPDATE PREFIX_product_special SET
            price = :price,
            discount = :discount,
            discount_suffix = :discountSuffix,
            date_start = :dateStart,
            date_end = :dateEnd
            WHERE product_special_id = :productSpecialID', array(
            'price'             => $data['price'],
            'discount'          => $data['discount'],
            'discountSuffix'    => $data['discount_suffix'],
            'dateStart'         => $data['date_start'],
            'dateEnd'           => $data['date_end'],
            'productSpecialID'  => $productSpecialID
        ));
    }

    public function deleteSpecial($productSpecialID)
    {
        $this->query('DELETE FROM PREFIX_product_special WHERE product_special_id = :productSpecialID', array('productSpecialID' => $productSpecialID));
    }

    public function getTotalSpecials()
    {
        $result = $this->query('SELECT COUNT(*) AS total_specials FROM PREFIX_product_special')->fetch();

        return $result['total_specials'];
    }

    public function getSpecials($filter)
    {
        return $this->fetchAll('SELECT ps.*, pd.name, p.price AS product_price, p.model_2 AS model
            FROM PREFIX_product_special ps
                LEFT JOIN PREFIX_product p ON ps.product_id = p.product_id
                LEFT JOIN PREFIX_product_description pd ON p.product_id = pd.product_id AND pd.language_id = :languageID
            ORDER BY ps.product_special_id DESC
            LIMIT :start, :end',
            array(
                'languageID' => $this->config->get('language_id'),
                'start'      => (int)$filter['start'],
                'end'        => (int)$filter['limit']
            )
        );
    }

    public function getSpecial($productSpecialID)
    {
        return $this->query('SELECT p.*, pd.name AS product
            FROM PREFIX_product_special p
            LEFT JOIN PREFIX_product_description pd ON p.product_id = pd.product_id AND pd.language_id = :languageID
            WHERE product_special_id = :productSpecialID',
            array(
                'productSpecialID'  => $productSpecialID,
                'languageID'        => $this->config->get('language_id')
            )
        )->fetch();
    }
}
