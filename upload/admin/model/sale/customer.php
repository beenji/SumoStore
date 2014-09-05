<?php
namespace Sumo;
class ModelSaleCustomer extends Model
{
    public function addCustomer($data)
    {
        $this->query("
            INSERT INTO PREFIX_customer
            SET date_added  = :date",
            array(
                'date'      => date('Y-m-d H:i:s')
            )
        );

        $customer_id = $this->lastInsertId();

        // Approve customer?
        if ($data['customer_group_id'] == 1) {
            $data['approved'] = 1;
        }

        return $this->editCustomer($customer_id, $data);
    }

    public function editCustomer($customer_id, $data)
    {
        if ($data['password']) {
            $this->query(
                "UPDATE PREFIX_customer
                SET salt            = '" . ($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "',
                    password        = '" . sha1($salt . sha1($salt . sha1($data['password']))) . "'
                WHERE customer_id   = " . (int)$customer_id);
        }
        unset($data['password']);
        unset($data['confirm']);
        $tax = $this->config->get('tax_percentages');
        $this->query("DELETE FROM PREFIX_address WHERE customer_id = " . (int)$customer_id);
        if (isset($data['address'])) {
            foreach ($data['address'] as $address) {
                $this->query(
                    "INSERT INTO PREFIX_address
                    SET address_id      = :address_id,
                        customer_id     = :customer_id,
                        firstname       = :firstname,
                        middlename      = :middlename,
                        lastname        = :lastname,
                        company         = :company,
                        company_id      = :cid,
                        tax_id          = :tax_id,
                        tax_percentage  = :tax_percentage,
                        address_1       = :address_1,
                        number          = :number,
                        addon           = :addon,
                        address_2       = :address_2,
                        city            = :city,
                        postcode        = :postcode,
                        country_id      = :country_id,
                        zone_id         = :zone_id",
                    array(
                        'address_id'    => (int)$address['address_id'],
                        'customer_id'   => (int)$customer_id,
                        'firstname'     => $address['firstname'],
                        'middlename'    => $address['middlename'],
                        'lastname'      => $address['lastname'],
                        'company'       => isset($address['company']) ? $address['company'] : '',
                        'cid'           => isset($address['company_id']) ? $address['company_id'] : '',
                        'tax_id'        => isset($address['tax_id']) ? $address['tax_id'] : 0,
                        'tax_percentage'=> isset($address['tax_percentage']) ? $address['tax_percentage'] : 0,
                        'address_1'     => isset($address['address_1']) ? $address['address_1'] : '',
                        'number'        => isset($address['number']) ? $address['number'] : '',
                        'addon'         => isset($address['addon']) ? $address['addon'] : '',
                        'address_2'     => isset($address['address_2']) ? $address['address_2'] : '',
                        'city'          => $address['city'],
                        'postcode'      => $address['postcode'],
                        'country_id'    => (int)$address['country_id'],
                        'zone_id'       => (int)$address['zone_id']
                    )
                );

                if (isset($address['default'])) {
                    $address_id = $this->lastInsertId();
                    $this->query("UPDATE PREFIX_customer SET address_id = " . (int)$address_id . " WHERE customer_id = " . (int)$customer_id);
                }
            }
            unset($data['address']);
        }

        $data['newsletter']         = (int)$data['newsletter'];
        $data['customer_group_id']  = (int)$data['customer_group_id'];
        $data['status']             = (int)$data['status'];
        $data['birthdate']          = Formatter::dateReverse($data['birthdate']);
        $data['gender']             = in_array($data['gender'], array('m', 'f')) ? $data['gender'] : 'm';

        $sql = "UPDATE PREFIX_customer SET ";
        $values = array();
        foreach ($data as $key => $value) {
            $sql .= $key . ' = :' . md5($key) . ',';
            $values[md5($key)] = $value;
        }
        $sql = rtrim($sql, ',') . " WHERE customer_id = " . (int)$customer_id;
        $this->query($sql, $values);

        Cache::removeAll();
    }

    public function editToken($customer_id, $token)
    {
        $this->query("UPDATE PREFIX_customer SET token = '" . $token . "' WHERE customer_id = " . (int)$customer_id);
    }

    public function deleteCustomer($customer_id)
    {
        $this->query("DELETE FROM PREFIX_customer WHERE customer_id = " . (int)$customer_id);
        $this->query("DELETE FROM PREFIX_customer_reward WHERE customer_id = " . (int)$customer_id);
        $this->query("DELETE FROM PREFIX_customer_transaction WHERE customer_id = " . (int)$customer_id);
        $this->query("DELETE FROM PREFIX_customer_ip WHERE customer_id = " . (int)$customer_id);
        $this->query("DELETE FROM PREFIX_address WHERE customer_id = " . (int)$customer_id);

        Cache::removeAll();
    }

    public function getCustomer($customer_id)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_customer WHERE customer_id = " . (int)$customer_id)->fetch();
    }

    public function getCustomerByEmail($email)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_customer WHERE LCASE(email) = :email", array('email' => utf8_strtolower($email)))->fetch();

    }

    public function getCustomers($data = array())
    {
        $cache = 'customer.data.' . json_encode($data);
        $cacheData = Cache::find($cache);
        if (is_array($cacheData)) {
            return $cacheData;
        }

        $values = array('language' => $this->config->get('language_id'));
        $sql = "SELECT *, CONCAT(c.lastname, ', ', c.firstname) AS name, cgd.name AS customer_group
        FROM PREFIX_customer AS c
        LEFT JOIN PREFIX_customer_group_description AS cgd
            ON c.customer_group_id = cgd.customer_group_id AND cgd.language_id = :language";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.lastname, ', ', c.firstname) LIKE :filter_name";
            $values['filter_name'] = '%' . $data['filter_name'] . '%';
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE :email";
            $values['filter_email'] = '%' . $data['filter_email'] . '%';
        }

        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "c.newsletter = " . (int)$data['filter_newsletter'];
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "c.customer_group_id = " . (int)$data['filter_customer_group_id'];
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "c.customer_id IN (SELECT customer_id FROM PREFIX_customer_ip WHERE ip LIKE :filter_ip)";
            $values['filter_ip'] = '%' . $data['filter_ip'] . '%';
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = " . (int)$data['filter_status'];
        }

        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "c.approved = " . (int)$data['filter_approved'];
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = :filter_date_added";
            $values['filter_date_added'] = Formatter::dateReverse($data['filter_date_added']);
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'c.email',
            'customer_group',
            'c.status',
            'c.approved',
            'c.ip',
            'c.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        }
        else {
            $sql .= " ORDER BY name";
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

        $data = $this->fetchAll($sql, $values);
        Cache::set($cache, $data);
        return $data;
    }

    public function approve($customer_id)
    {
        $customer_info = $this->getCustomer($customer_id);
        if ($customer_info) {
            $this->query("UPDATE PREFIX_customer SET approved = 1 WHERE customer_id = " . (int)$customer_id);
            Mailer::setCustomer($customer_info);
            $template = Mailer::getTemplate('account_approved', !empty($customer_info['language_id']) ? $customer_info['language_id'] : null);

            Mail::setTo($customer_info['email']);
            Mail::setSubject($template['subject']);
            Mail::setText($template['content']);
            Mail::send();
        }
        Cache::removeAll();
    }

    public function getAddress($address_id)
    {
        $address_query = $this->query("SELECT * FROM PREFIX_address WHERE address_id = " . (int)$address_id)->fetch();

        if ($address_query) {
            $country_query = $this->query("SELECT * FROM PREFIX_country WHERE country_id = " . (int)$address_query['country_id'])->fetch();

            $country            = '';
            $iso_code_2         = '';
            $iso_code_3         = '';
            $address_format     = '';
            $zone               = '';
            $zone_code          = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1} {number} {addon}' . "\n" . '{address_2}' . "\n" . '{postcode} {city}' . "\n" . '{zone}' . "\n" . '{country}';
            if ($country_query) {
                $country        = $country_query['name'];
                $iso_code_2     = $country_query['iso_code_2'];
                $iso_code_3     = $country_query['iso_code_3'];
                //$address_format = $country_query['address_format'];
            }
            if ($address_query['zone_id']) {
                $zone_query = $this->query("SELECT * FROM PREFIX_zone WHERE zone_id = " . (int)$address_query['zone_id'])->fetch();

                if ($zone_query) {
                    $zone           = $zone_query['name'];
                    $zone_code      = $zone_query['code'];
                }
            }

            return array(
                'address_id'        => $address_query['address_id'],
                'customer_id'       => $address_query['customer_id'],
                'firstname'         => $address_query['firstname'],
                'middlename'        => $address_query['middlename'],
                'lastname'          => $address_query['lastname'],
                'company'           => $address_query['company'],
                'company_id'        => $address_query['company_id'],
                'tax_id'            => $address_query['tax_id'],
                'tax_percentage'    => $address_query['tax_id'],
                'address_1'         => $address_query['address_1'],
                'number'            => $address_query['number'],
                'addon'             => $address_query['addon'],
                'address_2'         => $address_query['address_2'],
                'postcode'          => $address_query['postcode'],
                'city'              => $address_query['city'],
                'zone_id'           => $address_query['zone_id'],
                'zone'              => $zone,
                'zone_code'         => $zone_code,
                'country_id'        => $address_query['country_id'],
                'country'           => $country,
                'iso_code_2'        => $iso_code_2,
                'iso_code_3'        => $iso_code_3,
                'address_format'    => $address_format
            );
        }
    }

    public function getAddresses($customer_id)
    {
        $address_data = array();

        $query = $this->fetchAll("SELECT address_id FROM PREFIX_address WHERE customer_id = " . (int)$customer_id);

        foreach ($query as $result) {
            $address_info = $this->getAddress($result['address_id']);

            if ($address_info) {
                $address_data[$result['address_id']] = $address_info;
            }
        }

        return $address_data;
    }

    public function getTotalCustomers($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM PREFIX_customer";
        $values = array();
        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.lastname, ', ', c.firstname) LIKE :filter_name";
            $values['filter_name'] = '%' . $data['filter_name'] . '%';
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE :email";
            $values['filter_email'] = '%' . $data['filter_email'] . '%';
        }

        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "c.newsletter = " . (int)$data['filter_newsletter'];
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "c.customer_group_id = " . (int)$data['filter_customer_group_id'];
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "c.customer_id IN (SELECT customer_id FROM PREFIX_customer_ip WHERE ip LIKE :filter_ip)";
            $values['filter_ip'] = '%' . $data['filter_ip'] . '%';
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = " . (int)$data['filter_status'];
        }

        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "c.approved = " . (int)$data['filter_approved'];
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = :filter_date_added";
            $values['filter_date_added'] = Formatter::dateReverse($data['filter_date_added']);
        }

        $data = $this->query($sql, $values)->fetch();
        return $data['total'];
    }

    public function getTotalCustomersAwaitingApproval()
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer WHERE status = 0 OR approved = 0")->fetch();
        return $data['total'];
    }

    public function getTotalAddressesByCustomerId($customer_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_address WHERE customer_id = " . (int)$customer_id)->fetch();
        return $query['total'];
    }

    public function getTotalAddressesByCountryId($country_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_address WHERE country_id = " . (int)$country_id)->fetch();
        return $query['total'];
    }

    public function getTotalAddressesByZoneId($zone_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_address WHERE zone_id = " . (int)$zone_id)->fetch();
        return $query['total'];
    }

    public function getTotalCustomersByCustomerGroupId($customer_group_id)
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer WHERE customer_group_id = :group", array('group' => $customer_group_id))->fetch();
        return $data['total'];
    }

    public function addHistory($customer_id, $comment)
    {
        if (!empty($comment)) {
            $this->query(
                "INSERT INTO PREFIX_customer_history
                SET customer_id     = :customer_id,
                    comment         = :comment,
                    date_added      = :date_added",
                array(
                    'customer_id'   => $customer_id,
                    'comment'       => strip_tags($comment),
                    'date_added'    => Formatter::dateReverse(time())
                )
            );
            Cache::removeAll();
        }
    }

    public function getHistories($customer_id, $start = 0, $limit = 0)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 0) {
            $limit = 10;
        }

        $sql_limit = "";
        if ($limit > 0) {
            $sql_limit = "LIMIT " . (int)$start . "," . (int)$limit;
        }

        return $this->fetchAll("SELECT comment, date_added FROM PREFIX_customer_history WHERE customer_id = " . (int)$customer_id . " ORDER BY date_added DESC " . $sql_limit);
    }

    public function getTotalHistories($customer_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_history WHERE customer_id = " . (int)$customer_id)->fetch();
        return $query['total'];
    }

    public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0)
    {
        $customer_info = $this->getCustomer($customer_id);

        if ($customer_info && !empty($description)) {
            $this->query("INSERT INTO PREFIX_customer_transaction SET
                customer_id = '" . (int)$customer_id . "',
                order_id = '" . (int)$order_id . "',
                description = :description,
                amount = '" . (float)$amount . "',
                date_added = NOW()", array(
                    'description' => $description
                ));

            Mailer::setCustomer($customer_info);
            $template = Mailer::getTemplate('account_transaction', !empty($customer_info['language_id']) ? $customer_info['language_id'] : null);

            Mail::setTo($customer_info['email']);
            Mail::setSubject($template['subject']);
            Mail::setHtml($template['content']);
            Mail::send();
        }
        Cache::removeAll();
    }

    public function deleteTransaction($order_id)
    {
        $this->query("DELETE FROM PREFIX_customer_transaction WHERE order_id = " . (int)$order_id);
    }

    public function getTransactions($customer_id, $start = 0, $limit = 0)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 0) {
            $limit = 10;
        }

        $sql_limit = "";
        if ($limit > 0) {
            $sql_limit = "LIMIT " . (int)$start . "," . (int)$limit;
        }

        return $this->fetchAll("SELECT * FROM PREFIX_customer_transaction WHERE customer_id = " . (int)$customer_id . " ORDER BY date_added DESC " . $sql_limit);
    }

    public function getTotalTransactions($customer_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total  FROM PREFIX_customer_transaction WHERE customer_id = " . (int)$customer_id)->fetch();
        return $query['total'];
    }

    public function getTransactionTotal($customer_id)
    {
        $query = $this->query("SELECT SUM(amount) AS total FROM PREFIX_customer_transaction WHERE customer_id = " . (int)$customer_id)->fetch();
        return $query['total'];
    }

    public function getTotalTransactionsByOrderId($order_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_transaction WHERE order_id = " . (int)$order_id)->fetch();
        return $query['total'];
    }

    public function addReward($customer_id, $description = '', $points = '', $order_id = 0)
    {
        $customer_info = $this->getCustomer($customer_id);

        if ($customer_info && !empty($description)) {
            $this->query("INSERT INTO PREFIX_customer_reward SET
                customer_id = '" . (int)$customer_id . "',
                order_id = '" . (int)$order_id . "',
                points = '" . (int)$points . "',
                description = :description,
                date_added = NOW()", array(
                    'description' => $description
                ));

            Mailer::setCustomer($customer_info);
            $template = Mailer::getTemplate('account_reward', !empty($customer_info['language_id']) ? $customer_info['language_id'] : null);

            Mail::setTo($customer_info['email']);
            Mail::setSubject($template['subject']);
            Mail::setHtml($template['content']);
            Mail::send();
        }

        Cache::removeAll();
    }

    public function deleteReward($order_id)
    {
        $this->query("DELETE FROM PREFIX_customer_reward WHERE order_id = '" . (int)$order_id . "'");
        Cache::removeAll();
    }

    public function getRewards($customer_id, $start = 0, $limit = 0)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 0) {
            $limit = 10;
        }

         $sql_limit = "";
        if ($limit > 0) {
            $sql_limit = "LIMIT " . (int)$start . "," . (int)$limit;
        }

        return $this->fetchAll("SELECT * FROM PREFIX_customer_reward WHERE customer_id = " . (int)$customer_id . " ORDER BY date_added DESC " . $sql_limit);
    }

    public function getTotalRewards($customer_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_reward WHERE customer_id = " . (int)$customer_id)->fetch();
        return $query['total'];
    }

    public function getRewardTotal($customer_id)
    {
        $query = $this->query("SELECT SUM(points) AS total FROM PREFIX_customer_reward WHERE customer_id = " . (int)$customer_id)->fetch();
        return $query['total'];
    }

    public function getTotalCustomerRewardsByOrderId($order_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_reward WHERE order_id = " . (int)$order_id)->fetch();
        return $query['total'];
    }

    public function getIpsByCustomerId($customer_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_customer_ip WHERE customer_id = " . (int)$customer_id);
    }

    public function getTotalCustomersByIp($ip)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_ip WHERE ip = :ip", array('ip' => $ip))->fetch();
        return $query['total'];
    }

    public function addBanIp($ip)
    {
        $this->query("INSERT INTO PREFIX_customer_ban_ip SET ip = :ip", array('ip' => $ip));
    }

    public function removeBanIp($ip)
    {
        $this->query("DELETE FROM PREFIX_customer_ban_ip WHERE ip = :ip", array('ip' => $ip));
    }

    public function getTotalBanIpsByIp($ip)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_ban_ip WHERE ip = :ip", array('ip' => $ip))->fetch();
        return $query['total'];
    }

    public function getCustomersPerCountry()
    {
        return $this->query("SELECT COUNT(*) AS customers, c.name AS country_name
            FROM PREFIX_address a, PREFIX_country c
            WHERE a.country_id = c.country_id
                AND address_id IN (
                    SELECT address_id
                    FROM PREFIX_customer
                )
            GROUP BY a.country_id
            ORDER BY customers DESC
            LIMIT 4")->fetchAll();
    }

    public function getCustomerStats($interval)
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

        return $this->query("SELECT $sqlColumn, COUNT(*) AS value FROM PREFIX_customer WHERE $sqlWhere GROUP BY $sqlGroup")->fetchAll();
    }
}
