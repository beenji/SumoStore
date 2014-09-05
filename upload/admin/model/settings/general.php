<?php
namespace Sumo;

class ModelSettingsGeneral extends Model
{
    private $settings;

    public function getSettings($refresh = false)
    {
        if (is_array($this->settings) && count($this->settings) && !$refresh) {
            return $this->settings;
        }

        foreach (Database::fetchAll("SELECT setting_name, setting_value, is_json FROM PREFIX_settings") as $list) {
            $this->settings[$list['setting_name']] = $list['is_json'] ? json_decode($list['setting_value'], true) : $list['setting_value'];
        }
        return $this->settings;
    }

    public function getSetting($key)
    {
        if (empty($key)) {
            throw new \Exception('ModelSettingsGeneral->getSetting requires a non-empty $key');
        }

        if (!is_array($this->settings) || !isset($this->settings[$key])) {
            $this->getSettings();
            if (!isset($this->settings[$key])) {
                return false;
            }
        }
        return $this->settings[$key];
    }

    public function setSetting($key, $value)
    {
        if (empty($key)) {
            return false;
        }

        if ($key == 'tax_percentage') {
            $old = $this->getSetting('tax_percentage');
            foreach ($old as $type => $percentage) {
                if (isset($value[$type]) && $value[$type] != $percentage) {
                    Database::query("UPDATE PREFIX_product SET tax_percentage = :new WHERE tax_percentage = :old", array('new' => $value[$type], 'old' => $percentage));
                }
            }
        }

        Database::query("DELETE FROM PREFIX_settings WHERE setting_name = :name", array('name' => $key));
        Database::query(
            "INSERT INTO PREFIX_settings SET setting_name = :key, setting_value = :value, is_json = :json",
            array(
                'key'   => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'json'  => is_array($value) ? 1 : 0
            )
        );
    }

    public function setSettings($array)
    {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as $key => $value) {

            $this->setSetting($key, $value);
        }
    }
}
