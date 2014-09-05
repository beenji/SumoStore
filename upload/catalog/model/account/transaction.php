<?php
namespace Sumo;
class ModelAccountTransaction extends Model
{
    public function getTransactions($data = array())
    {
        $sql    = "SELECT * FROM PREFIX_customer_transaction WHERE customer_id = :customerID";
        $fields = array('customerID' => (int)$this->customer->getId());

        $sort_data = array(
            'amount',
            'description',
            'date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY :sort";
            $fields['sort'] = $data['sort'];
        } 
        else {
            $sql .= " ORDER BY date_added";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } 
        else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT :start, :limit";
            $fields['start'] = $data['start'];
            $fields['limit'] = $data['limit'];
        }

        return $this->query($sql, $fields)->fetchAll();
    }

    public function getTotalTransactions()
    {
        $query = $this->query("SELECT COUNT(*) AS total 
            FROM PREFIX_customer_transaction 
            WHERE customer_id = :customerID", array(
                'customerID' => (int)$this->customer->getId()
            ))->fetch();

        return $query['total'];
    }

    public function getTotalAmount()
    {
        $query = $this->query("SELECT SUM(amount) AS total 
            FROM PREFIX_customer_transaction 
            WHERE customer_id = :customerID GROUP BY customer_id", array(
                'customerID' => (int)$this->customer->getId()
            ))->fetch();

        if ($query['total']) {
            return $query['total'];
        } else {
            return 0;
        }
    }
}
