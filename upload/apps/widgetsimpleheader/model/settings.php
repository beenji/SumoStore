<?php
namespace Widgetsimpleheader;
use Sumo;
use App;

class ModelSettings extends App\Model
{
    private $settings;

    public function getSettings($store_id)
    {
        if (is_array($this->settings[$store_id]) && count($this->settings[$store_id])) {
            return $this->settings[$store_id];
        }

        $settings = $this->select('*', null, 'store_id = ' . $store_id);
        foreach ($settings as $list) {
            $this->settings[$store_id][$list['setting_name']] = $list['json'] ? json_decode($list['setting_value'], true) : $list['setting_value'];
        }
        return $this->settings[$store_id];
    }

    public function setSetting($store_id, $key, $value)
    {
        self::query("DELETE FROM " . $this->baseTable . " WHERE store_id = :id AND setting_name = :name", array('id' => $store_id, 'name' => $key));
        self::query("INSERT INTO " . $this->baseTable . " SET store_id = :id, setting_name = :name, setting_value = :value, json = :json",
            array(
                'id'    => $store_id,
                'name'  => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'json'  => is_array($value) ? 1 : 0
            )
        );
    }

    public function setSettings($store_id, $data)
    {
        self::query("DELETE FROM " . $this->baseTable . " WHERE store_id = :id", array('id' => $store_id));

        $data['enabled'] = $data['enabled'] == 'on' ? 1 : 0;
        $this->setAppStatus($store_id, $data['enabled']);

        foreach ($data as $key => $value) {
            self::query(
                "INSERT INTO " . $this->baseTable . " SET store_id = :store, setting_name = :name, setting_value = :value, json = :json",
                array(
                    'store' => $store_id,
                    'name'  => $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'json'  => is_array($value) ? 1 : 0
                )
            );
        }
    }

    public function checkSettings($store_id = 0)
    {
        $data = $this->getSettings($store_id);
        if (!$data['enabled']) {
            return true;
        }

        return true;
    }

    public function wasInstalled()
    {
        try {
            $this->select();
            return true;
        }
        catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
