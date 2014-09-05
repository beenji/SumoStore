<?php
namespace Sumo;
class ModelSaleReturn extends Model
{
    public function addReturn($data)
    {
        $this->query("INSERT INTO PREFIX_return SET date_added = :date", array('date' => date('Y-m-d H:i:s')));
        $return_id = $this->lastInsertId();
        return $this->editReturn($return_id, $data);
    }

    public function editReturn($return_id, $data)
    {
        $data['quantity']           = (int)$data['quantity'];
        $data['opened']             = (int)$data['opened'];
        $data['date_ordered']       = Formatter::dateReverse($data['date_ordered']);
        $data['return_id']          = $return_id;

        $this->query(
            "UPDATE PREFIX_return
            SET order_id            = :order_id,
                product_id          = :product_id,
                customer_id         = :customer_id,
                firstname           = :firstname,
                lastname            = :lastname,
                telephone           = :telephone,
                product             = :product,
                model               = :model,
                quantity            = :quantity,
                opened              = :opened,
                return_reason_id    = :return_reason_id,
                return_action_id    = :return_action_id,
                return_status_id    = :return_status_id,
                comment             = :comment,
                date_ordered        = :date_ordered,
                date_modified       = NOW()
            WHERE return_id         = :return_id",
            $data
        );
        Cache::removeAll();
    }

    public function editReturnAction($return_id, $return_action_id)
    {
        $this->query("UPDATE PREFIX_return SET return_action_id = " . (int)$return_action_id . " WHERE return_id = " . (int)$return_id);
        Cache::removeAll();
    }

    public function deleteReturn($return_id)
    {
        $this->query("DELETE FROM PREFIX_return WHERE return_id = " . (int)$return_id);
        $this->query("DELETE FROM PREFIX_return_history WHERE return_id = " . (int)$return_id);
        Cache::removeAll();
    }

    public function getReturn($return_id)
    {
        return $this->query(
            "SELECT
                DISTINCT *,
                (
                    SELECT CONCAT(c.firstname, ' ', c.lastname)
                    FROM PREFIX_customer AS c
                    WHERE c.customer_id = r.customer_id
                ) AS customer
            FROM PREFIX_return AS r
            WHERE r.return_id = :id",
            array('id' => $return_id)
        )->fetch();
    }

    public function getReturns($data = array())
    {
        $values = array('language' => $this->config->get('language_id'));
        $sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, (
                        SELECT rs.name
                        FROM PREFIX_return_status AS rs
                        WHERE rs.return_status_id = r.return_status_id AND rs.language_id = :language
                    ) AS status
                FROM PREFIX_return AS r";

        $implode = array();

        if (!empty($data['filter_return_id'])) {
            $implode[] = "r.return_id = :filter_return_id";
            $values['filter_return_id'] = $data['filter_return_id'];
        }

        if (!empty($data['filter_order_id'])) {
            $implode[] = "r.order_id = :filter_order_id";
            $values['filter_order_id'] = $data['filter_order_id'];
        }

        if (!empty($data['filter_customer'])) {
            $implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE :filter_customer";
            $values['filter_customer'] = $data['filter_customer'] . '%';
        }

        if (!empty($data['filter_product'])) {
            $implode[] = "r.product = :filter_product";
            $values['filter_product'] = $data['filter_product'];
        }

        if (!empty($data['filter_model'])) {
            $implode[] = "r.model = :filter_model";
            $values['filter_model'] = $data['filter_model'];
        }

        if (!empty($data['filter_return_status_id'])) {
            $implode[] = "r.return_status_id = :filter_return_status_id";
            $values['filter_return_status_id'] = $data['filter_return_status_id'];
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(r.date_added) = DATE('" . Formatter::dateRevers($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $implode[] = "DATE(r.date_modified) = DATE('" . Formatter::dateRevers($data['filter_date_modified']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'r.return_id',
            'r.order_id',
            'customer',
            'r.product',
            'r.model',
            'status',
            'r.date_added',
            'r.date_modified'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        }
        else {
            $sql .= " ORDER BY r.return_id";
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

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $this->fetchAll($sql, $values);
    }

    public function getTotalReturns($data = array())
    {
        unset($data['start']);
        unset($data['limit']);
        $cache = 'return_total_cached.' . json_encode($data);

        $data = Cache::find($cache);
        if (!is_array($data) || !count($data)) {
            $fetch = $this->getReturns($data);
            $data = count($fetch);
            Cache::set($cache, $data);
        }
        return $data;
    }

    public function getTotalReturnsByReturnStatusId($return_status_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_return WHERE return_status_id = " . (int)$return_status_id)->fetch();

        return $query['total'];
    }

    public function getTotalReturnsByReturnReasonId($return_reason_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM `PREFIX_return` WHERE return_reason_id = " . (int)$return_reason_id)->fetch();

        return $query['total'];
    }

    public function getTotalReturnsByReturnActionId($return_action_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM `PREFIX_return` WHERE return_action_id = " . (int)$return_action_id)->fetch();

        return $query['total'];
    }

    public function addReturnHistory($return_id, $data)
    {
        $this->query(
            "UPDATE PREFIX_return
            SET return_status_id    = :status_id,
                date_modified       = :date
            WHERE return_id         = :id",
            array(
                'status_id'         => $data['return_status_id'],
                'date'              => date('Y-m-d H:i:s'),
                'id'                => $return_id
            )
        );

        if (!isset($data['notify'])) {
            $data['notify'] = 0;
        }
        $data['date']       = date('Y-m-d H:i:s');
        $data['return_id']  = $return_id;
        $this->query(
            "INSERT INTO PREFIX_return_history
            SET return_id           = :return_id,
                return_status_id    = :return_status_id,
                notify              = :notify,
                comment             = :comment,
                date_added          = :date",
            $data
        );

        if ($data['notify']) {

            $return_data = $this->getReturn($return_id);

            Mailer::setCustomer($return_data);
            Mailer::setReturn($return_data);
            $mail = Mailer::getTemplate('update_return_status_' . $data['return_status_id']);

            if (!empty($data['comment'])) {
                $mail['content'] = str_replace('{hasComments}', $data['comment'] . '<br /><br />', $mail['content']);
            }
            else {
                $mail['content'] = str_replace('{hasComments}', '', $mail['content']);
            }

            Mail::setTo($return_data['email']);
            Mail::setSubject($mail['title']);
            Mail::setHTML($mail['content']);
            Mail::send();
        }
        Cache::removeAll();
    }

    public function getReturnHistories($return_id, $start = 0, $limit = 10)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        return $this->fetchAll(
            "SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify
            FROM PREFIX_return_history AS rh
            LEFT JOIN PREFIX_return_status AS rs
                ON rh.return_status_id = rs.return_status_id
            WHERE rh.return_id = " . (int)$return_id . "
                AND rs.language_id = '" . (int)$this->config->get('language_id') . "'
            ORDER BY rh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
    }

    public function getTotalReturnHistories($return_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_return_history WHERE return_id = '" . (int)$return_id . "'");

        return $query->row['total'];
    }

    public function getTotalReturnHistoriesByReturnStatusId($return_status_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_return_history WHERE return_status_id = '" . (int)$return_status_id . "' GROUP BY return_id");

        return $query->row['total'];
    }

    public function getReturnStats($interval)
    {
        switch ($interval) {
            case 'day':
                $sqlColumn = 'HOUR(date_added) AS label';
                $sqlGroup  = 'HOUR(date_added)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 23 HOUR) <= date_added'; 
            break;

            case 'week':
                $sqlColumn = 'WEEKDAY(date_added) AS label';
                $sqlGroup  = 'WEEKDAY(date_added)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 6 DAY) <= date_added';
            break;

            case 'month':
                $sqlColumn = 'WEEKOFYEAR(date_added) AS label';
                $sqlGroup  = 'WEEKOFYEAR(date_added)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= date_added';
            break;

            case 'year':
                $sqlColumn = 'MONTH(date_added) AS label';
                $sqlGroup  = 'MONTH(date_added)';
                $sqlWhere  = 'DATE_SUB(CURDATE(), INTERVAL 1 YEAR) <= date_added';
            break;
        }

        return $this->query("SELECT $sqlColumn, COUNT(*) AS value FROM PREFIX_return WHERE $sqlWhere GROUP BY $sqlGroup")->fetchAll();
    }
}
