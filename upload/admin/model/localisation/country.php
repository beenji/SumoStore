<?php
namespace Sumo;
class ModelLocalisationCountry extends Model
{
    public function addCountry($data) {
        $this->query("INSERT INTO PREFIX_country SET name = '" . $this->db->escape($data['name']) . "', iso_code_2 = '" . $this->db->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->db->escape($data['iso_code_3']) . "', address_format = '" . $this->db->escape($data['address_format']) . "', postcode_required = '" . (int)$data['postcode_required'] . "', status = '" . (int)$data['status'] . "'");

        Cache::removeAll();
    }

    public function editCountry($country_id, $data)
    {
        $this->query(
            "UPDATE PREFIX_country SET name = '" . $this->db->escape($data['name']) . "', iso_code_2 = '" . $this->db->escape($data['iso_code_2']) . "', iso_code_3 = '" . $this->db->escape($data['iso_code_3']) . "', address_format = '" . $this->db->escape($data['address_format']) . "', postcode_required = '" . (int)$data['postcode_required'] . "', status = '" . (int)$data['status'] . "' WHERE country_id = '" . (int)$country_id . "'");

        Cache::removeAll();
    }

    public function deleteCountry($country_id)
    {
        $this->query("DELETE FROM PREFIX_country WHERE country_id = " . (int)$country_id);
        Cache::removeAll();
    }

    public function getCountry($country_id)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_country WHERE country_id = " . (int)$country_id)->fetch();
    }

    public function getCountries($data = array())
    {
        $cache = 'countries.' . json_encode($data);
        $return = Cache::find($cache);
        if (is_array($return)) {
            return $return;
        }

        $sql = "SELECT * FROM PREFIX_country";
        $sort_data = array(
            'name',
            'iso_code_2',
            'iso_code_3'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

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

        $return = $this->fetchAll($sql);
        Cache::set($cache, $return);

        return $return;
    }

    public function getTotalCountries()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_country")->fetch();
        return $query['total'];
    }
}
