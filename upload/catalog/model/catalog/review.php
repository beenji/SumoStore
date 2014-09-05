<?php
namespace Sumo;
class ModelCatalogReview extends Model
{
    public function addReview($product_id, $data)
    {
        $data['id'] = $this->customer->getId();
        $data['product'] = $product_id;
        $data['rating'] = (int)$data['rating'];
        $this->query(
            "INSERT INTO PREFIX_review
            SET author      = :name,
            customer_id     = :id,
            product_id      = :product,
            text            = :text,
            rating          = :rating,
            date_added      = NOW()",
            $data
        );
        return true;
    }

    public function getReviewsByProductId($product_id, $start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        return $this->fetchAll(
            "SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added
            FROM PREFIX_review AS r
            LEFT JOIN PREFIX_product AS p
                ON p.product_id = r.product_id
            LEFT JOIN PREFIX_product_description AS pd
                ON p.product_id = pd.product_id
            WHERE p.product_id = :id
                AND r.status = 1
                AND pd.language_id = :lang
            ORDER BY r.date_added DESC
            LIMIT :start, :limit",
            array(
                'id'    => $product_id,
                'lang'  => $this->config->get('language_id'),
                'start' => $start,
                'limit' => $limit
            )
        );
    }

    public function getTotalReviewsByProductId($product_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_review WHERE product_id = :id AND status = 1", array('id' => $product_id))->fetch();
        return $query['total'];
    }
}
