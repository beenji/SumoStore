<?php
namespace Sumo;
class ModelSettingSetting extends Model
{
    public function getSetting($group, $store_id = 0)
    {
        $cacheFile = 'settings.' . $store_id . '.' . strtolower($group);
        $data = Cache::find($cacheFile);
        if (is_array($data) || count($data)) {
            return $data;
        }
        $data = array();

        $result = Database::fetchAll("SELECT `key`, `value`, `serialized` FROM PREFIX_setting WHERE store_id = :id AND `group` = :group", array('id' => $store_id, 'group' => $group));

        foreach ($result as $list) {
            $data[$list['key']] = $result['serialized'] ? unserialize($result['value']) : $result['value'];
        }

        Cache::set($cacheFile, $data);
        return $data;
    }
}
