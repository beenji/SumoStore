<?php
namespace Sumo;
class ModelLocalisationGeoZone extends Model
{
    public function addGeoZone($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");

        $geo_zone_id = $this->db->getLastId();

        if (isset($data['zone_to_geo_zone'])) {
            foreach ($data['zone_to_geo_zone'] as $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '"  . (int)$value['country_id'] . "', zone_id = '"  . (int)$value['zone_id'] . "', geo_zone_id = '"  .(int)$geo_zone_id . "', date_added = NOW()");
            }
        }

        Cache::remove('geo_zone');
    }

    public function editGeoZone($geo_zone_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

        if (isset($data['zone_to_geo_zone'])) {
            foreach ($data['zone_to_geo_zone'] as $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '"  . (int)$value['country_id'] . "', zone_id = '"  . (int)$value['zone_id'] . "', geo_zone_id = '"  .(int)$geo_zone_id . "', date_added = NOW()");
            }
        }

        Cache::remove('geo_zone');
    }

    public function deleteGeoZone($geo_zone_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

        Cache::remove('geo_zone');
    }

    public function getGeoZone($geo_zone_id)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_geo_zone WHERE geo_zone_id = :id", array('id' => $geo_zone_id))->fetch();
    }

    public function getGeoZones($data = array())
    {
        if ($data) {
            $sql = "SELECT * FROM PREFIX_geo_zone";
            $sort_data = array(
                'name',
                'description'
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

            return $this->fetchAll($sql);
        }
        else {
            $data = Cache::find('geo_zone');

            if (!$data) {
                $data = $this->fetchAll("SELECT * FROM PREFIX_geo_zone ORDER BY name ASC");
                Cache::set('geo_zone', $data);
            }
            return $data;
        }
    }

    public function getTotalGeoZones()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "geo_zone");

        return $query->row['total'];
    }

    public function getZoneToGeoZone($country_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_zone_to_geo_zone WHERE country_id = " . (int)$country_id);
    }

    public function getTotalZoneToGeoZoneByGeoZoneId($geo_zone_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

        return $query->row['total'];
    }

    public function getTotalZoneToGeoZoneByCountryId($country_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$country_id . "'");

        return $query->row['total'];
    }

    public function getTotalZoneToGeoZoneByZoneId($zone_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE zone_id = '" . (int)$zone_id . "'");

        return $query->row['total'];
    }
}
