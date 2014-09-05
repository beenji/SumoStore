<?php
namespace Sumo;
class ModelLocalisationCountry extends Model
{
    public function getCountry($country_id)
    {
        return self::query("SELECT * FROM PREFIX_country WHERE country_id = :id AND status = 1", array('id' => $country_id))->fetch();
    }

    public function getCountries()
    {
        $cache = 'countries';
        $data = Cache::find($cache);
        if (is_array($cache) && count($cache)) {
            return $data;
        }
        $data = self::fetchAll("SELECT * FROM PREFIX_country WHERE status = 1 ORDER BY name ASC");
        Cache::set($cache, $data);
        return $data;
    }
}
