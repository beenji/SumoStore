<?php
namespace Sumo;
class ModelLocalisationZone extends Model
{
    public function addZone($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zone SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "'");

        $this->cache->delete('zone');
    }

    public function editZone($zone_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "zone SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "' WHERE zone_id = '" . (int)$zone_id . "'");

        $this->cache->delete('zone');
    }

    public function deleteZone($zone_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");

        $this->cache->delete('zone');
    }

    public function getZone($zone_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");

        return $query->row;
    }

    public function getZones($data = array())
    {
        $sql = "SELECT *, z.name, c.name AS country FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "country c ON (z.country_id = c.country_id)";

        $sort_data = array(
            'c.name',
            'z.name',
            'z.code'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY c.name";
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

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getZonesByCountryId($country_id)
    {
        $cache = 'zones.' . $country_id;
        $data = Cache::find($cache);
        if (!is_array($data) || !count($data)) {
            $data = $this->fetchAll("SELECT * FROM PREFIX_zone WHERE country_id = :id AND status = 1 ORDER BY name", array('id' => $country_id));
            Cache::set($cache, $data);
        }

        return $data;
    }

    public function getTotalZones()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone");

        return $query->row['total'];
    }

    public function getTotalZonesByCountryId($country_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "'");

        return $query->row['total'];
    }

    public function getCountryToGeoZone($country_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_zone_to_geo_zone WHERE country_id = :id", array('id' => $country_id));
    }
}
