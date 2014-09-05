<?php
namespace Sumo;
class ModelCatalogReview extends Model
{
    public function addReview($data)
    {
        $this->query("INSERT INTO PREFIX_review SET
            author = :author,
            product_id = :productID,
            text = :text,
            rating = :rating,
            status = :status,
            date_added = :dateAdded", array(
            'author'    => $data['author'],
            'productID' => $data['product_id'],
            'text'      => strip_tags($data['text']),
            'rating'    => $data['rating'],
            'status'    => $data['status'],
            'dateAdded' => Formatter::dateReverse($data['date_added'])));

        Cache::removeAll();
    }

    public function editReview($reviewID, $data)
    {
        $this->query("UPDATE PREFIX_review SET
            author = :author,
            product_id = :productID,
            text = :text,
            rating = :rating,
            status = :status,
            date_added = :dateAdded,
            date_modified = NOW()
            WHERE review_id = :reviewID", array(
                'reviewID'  => $reviewID,
                'author'    => $data['author'],
                'productID' => $data['product_id'],
                'text'      => strip_tags($data['text']),
                'rating'    => $data['rating'],
                'status'    => $data['status'],
                'dateAdded' => Formatter::dateReverse($data['date_added'])));

        Cache::removeAll();
    }

    public function deleteReview($reviewID)
    {
        $this->query("DELETE FROM PREFIX_review
            WHERE review_id = :reviewID", array('reviewID' => $reviewID));

        Cache::removeAll();
    }

    public function getReview($reviewID)
    {
        return $this->query("SELECT DISTINCT *, (SELECT pd.name FROM PREFIX_product_description pd WHERE pd.product_id = r.product_id AND pd.language_id = :languageID) AS product
                    FROM PREFIX_review r
                    WHERE r.review_id = :reviewID", array(
                        'reviewID'   => (int)$reviewID,
                        'languageID' => (int)$this->config->get('language_id')))->fetch();
    }

    public function getReviews($data = array())
    {
        $sql = "SELECT r.review_id, pd.name, r.author, r.rating, r.status, r.date_added
            FROM PREFIX_review r
                LEFT JOIN PREFIX_product_description pd ON (r.product_id = pd.product_id)
            WHERE pd.language_id = :languageID";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $this->query($sql, array('languageID' => (int)$this->config->get('language_id')))->fetchAll();
    }

    public function getTotalReviews()
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_review")->fetch();
        return $data['total'];
    }

    public function getTotalReviewsAwaitingApproval()
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_review WHERE status = 0")->fetch();
        return $data['total'];
    }
}
