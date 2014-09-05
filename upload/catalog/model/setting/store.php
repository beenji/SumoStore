<?php
namespace Sumo;
class ModelSettingStore extends Model
{
    public function getStores($data = array())
    {
        $store_data = Cache::find('store');
        if (!$store_data || !is_array($store_data) || empty($store_data)) {
            $store_data = Database::fetchAll("SELECT * FROM PREFIX_store ORDER BY url");
            Cache::set('store', $store_data);
        }

        return $store_data;
    }
}
