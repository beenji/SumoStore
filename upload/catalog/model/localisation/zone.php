<?php
namespace Sumo;
class ModelLocalisationZone extends Model
{
    public function getZone($zone_id)
    {
        return $this->query("SELECT * FROM PREFIX_zone WHERE zone_id = :id AND status = 1", array('id' => $zone_id))->fetch();
    }

    public function getZonesByCountryId($country_id)
    {
        $zone_data = Cache::find('zone.' . (int)$country_id);

        if (!$zone_data) {
            $zone_data = $this->fetchAll("SELECT * FROM PREFIX_zone WHERE country_id = :id AND status = 1 ORDER BY name", array('id' => $country_id));
            Cache::set('zone.' . (int)$country_id, $zone_data);
        }

        return $zone_data;
    }

    public function getCountryToGeoZone($country_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_zone_to_geo_zone WHERE country_id = :id", array('id' => $country_id));
    }
}
