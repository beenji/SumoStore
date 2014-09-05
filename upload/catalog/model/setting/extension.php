<?php
namespace Sumo;
class ModelSettingExtension extends Model
{
    public function getExtensions($type)
    {
        $cache = Cache::find('extensions.' . $this->config->get('config_store_id'), $type);
        if (!is_array($cache)) {
            $cache = Database::fetchAll("SELECT * FROM PREFIX_extension WHERE `type` = :type", array('type' => $type));
            Cache::set('extensions.' . $this->config->get('config_store_id'), $type, $cache);
        }

        return $cache;
    }
}
