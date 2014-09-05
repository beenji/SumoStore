<?php
namespace Banktransfer;
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
                    'value' => is_array($value) ? json_encode($value) : (int)$value,
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
        unset($data['enabled']);

        if (empty($data['instructions'][$this->config->get('language_id')])) {
            return false;
        }
        unset($data['instructions']);

        if (empty($data['payment']['status'])) {
            return false;
        }
        unset($data['payment']['status']);

        if (!empty($data['general']['rate']) && (preg_match('#^\d+(\.(\d+))?$#', $data['general']['rate']) != 1)) {
            return false;
        }
        else
        if (!empty($data['general']['extra']) && (preg_match('#^\d+(\.(\d+))?$#', $data['general']['extra']) != 1)) {
            return false;
        }
        else if (empty($data['general']['rate']) && empty($data['general']['extra'])) {
            unset($data['general']);
        }

        foreach ($data['zone'] as $zone_id => $list) {
            if (!empty($list['rate'])) {
                if (preg_match('#^\d+(\.(\d+))?$#', $list['rate']) != 1) {
                    return false;
                }
            }
            if (!empty($list['extra'])) {
                if (preg_match('#^\d+(\.(\d+))?$#', $list['extra']) != 1) {
                    return false;
                }
            }

            if (empty($list['extra']) && empty($list['rate'])) {
                unset($data['zone'][$zone_id]);
            }
        }
        if (!count($data['zone'])) {
            unset($data['zone']);
        }

        if (!count($data)) {
            return false;
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
