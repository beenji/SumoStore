<?php
namespace Sumo;
class ModelSaleCustomerBanIp extends Model
{
    public function addCustomerBanIp($data)
    {
        $this->query("INSERT INTO PREFIX_customer_ban_ip SET ip = :ip", array('ip' => $data['ip']));
        unlink(DIR_CACHE . 'sumo.ipban');
    }

    public function editCustomerBanIp($customerBanIPID, $data)
    {
        $this->query("UPDATE PREFIX_customer_ban_ip SET ip = :ip WHERE customer_ban_ip_id = :customerBanIPID", array(
            'customerBanIPID'   => (int)$customerBanIPID,
            'ip'                => $data['ip']));
        unlink(DIR_CACHE . 'sumo.ipban');
    }

    public function deleteCustomerBanIp($customerBanIPID)
    {
        $this->query("DELETE FROM PREFIX_customer_ban_ip WHERE customer_ban_ip_id = :customerBanIPID", array('customerBanIPID' => (int)$customerBanIPID));
        unlink(DIR_CACHE . 'sumo.ipban');
    }

    public function getCustomerBanIp($customer_ban_ip_id)
    {
        $data = $this->query("SELECT * FROM PREFIX_customer_ban_ip WHERE customer_ban_ip_id = :customerBanIPID", array('customerBanIPID' => (int)$customerBanIPID))->fetchAll();

        unlink(DIR_CACHE . 'sumo.ipban');
        return $data;
    }

    public function getCustomerBanIps($data = array())
    {
        $sql = "SELECT *, (SELECT COUNT(DISTINCT customer_id) FROM PREFIX_customer_ip ci WHERE ci.ip = cbi.ip) AS total FROM PREFIX_customer_ban_ip cbi";

        $sql .= " ORDER BY ip";

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
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

        return $this->query($sql)->fetchAll();
    }

    public function getTotalCustomerBanIps($data = array())
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_ban_ip")->fetch();
        return $data['total'];
    }
}
