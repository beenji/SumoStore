<?php
namespace Sumo;
class ModelAccountReturn extends Model
{
    public function addReturn($data)
    {
        //$returnStatusID = (int)$this->config->get('return_status_id');
        $returnStatusID = 1;

        $this->query("INSERT INTO PREFIX_return SET 
            order_id = :orderID, 
            customer_id = :customerID,
            firstname = :firstname,
            lastname = :lastname,
            email = :email,
            telephone = :telephone,
            product = :product,
            product_id = :productID,
            model = :model,
            quantity = :quantity,
            opened = :opened,
            return_reason_id = :returnReasonID,
            return_status_id = :returnStatusID, 
            comment = :comment, 
            date_ordered = :dateOrdered, 
            date_added = NOW(), 
            date_modified = NOW()", array(
                'orderID'           => $data['order_id'],
                'customerID'        => (int)$this->customer->getId(),
                'firstname'         => $data['firstname'],
                'lastname'          => $data['lastname'],
                'email'             => $data['email'],
                'telephone'         => $data['telephone'],
                'product'           => $data['product'],
                'productID'         => $data['product_id'],
                'model'             => $data['model'],
                'quantity'          => $data['quantity'],
                'opened'            => $data['opened'],
                'returnReasonID'    => $data['return_reason_id'],
                'returnStatusID'    => $returnStatusID,
                'comment'           => $data['comment'],
                'dateOrdered'       => Formatter::dateReverse($data['date_ordered'])));
    }

    public function getReturn($returnID)
    {
        return $this->query("SELECT r.*, (
                SELECT rr.name 
                FROM PREFIX_return_reason rr 
                WHERE rr.return_reason_id = r.return_reason_id 
                    AND rr.language_id = :languageID
            ) AS reason, (
                SELECT ra.name 
                FROM PREFIX_return_action ra 
                WHERE ra.return_action_id = r.return_action_id 
                    AND ra.language_id = :languageID
            ) AS action, (
                SELECT rs.name 
                FROM PREFIX_return_status rs 
                WHERE rs.return_status_id = r.return_status_id 
                    AND rs.language_id = :languageID
            ) AS status, r.comment, r.date_ordered, r.date_added, r.date_modified 
            FROM PREFIX_return r 
            WHERE return_id = :returnID
                AND customer_id = :customerID", array(
                    'languageID'    => (int)$this->config->get('language_id'),
                    'customerID'    => $this->customer->getId(),
                    'returnID'      => (int)$returnID
                ))->fetch();
    }

    public function getReturns($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        return $this->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, rs.name as status, r.date_added 
            FROM PREFIX_return r 
                LEFT JOIN PREFIX_return_status rs ON (r.return_status_id = rs.return_status_id) 
            WHERE r.customer_id = :customerID
                AND rs.language_id = :languageID 
            ORDER BY r.return_id DESC LIMIT :start, :limit", array(
                'customerID'    => $this->customer->getId(),
                'languageID'    => $this->config->get('language_id'),
                'start'         => $start,
                'limit'         => $limit))->fetchAll();
    }

    public function getTotalReturns()
    {
        $query = $this->query("SELECT COUNT(*) AS total 
            FROM PREFIX_return 
            WHERE customer_id = :customerID", array('customerID' => $this->customer->getId()))->fetch();

        return $query['total'];
    }

    public function getReturnHistories($returnID)
    {
        return $this->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify 
            FROM PREFIX_return_history rh 
                LEFT JOIN PREFIX_return_status rs ON rh.return_status_id = rs.return_status_id 
            WHERE rh.return_id = :returnID 
                AND rs.language_id = :languageID 
            ORDER BY rh.date_added ASC", array(
                'returnID'      => $returnID,
                'languageID'    => (int)$this->config->get('language_id')))->fetchAll();
    }
}
