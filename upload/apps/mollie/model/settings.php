<?php
namespace Mollie;
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
        self::query("DELETE FROM " . $this->baseTable . " WHERE store_id = :id AND setting_name != 'discount'", array('id' => $store_id));

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
        // update 1.0.1; language string for sofort
        Sumo\Language::getVar('APP_MOLLIE_CATALOG_SOFORT_DESCRIPTION');
        $check = self::query("SELECT value FROM PREFIX_translations WHERE key_id = (SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_MOLLIE_CATALOG_SOFORT_DESCRIPTION') AND language_id = 1")->fetch();
        if (empty($check['value'])) {
            self::query("INSERT INTO PREFIX_translations SET value = 'Betaal met SoFort banking', key_id = (SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_MOLLIE_CATALOG_SOFORT_DESCRIPTION'), language_id =1");
        }

        $data = $this->getSettings($store_id);
        if (!$data['enabled']) {
            return true;
        }
        unset($data['enabled']);

        if (empty($data['general']['api_key'])) {
            Sumo\Logger::warning('[Mollie] API Key is not set');
            return false;
        }
        else {
            include('api.php');
            $test = new ModelApi($this->registry);
            try {
                $test->setKey($data['general']['api_key']);
            }
            catch (\Exception $e) {
                Sumo\Logger::warning('[Mollie] API Key is invalid');
                return false;
            }

            try {
                $testData = $test->callMethods();
                if (isset($testData['error'])) {
                    Sumo\Logger::warning('[Mollie] API Key is invalid, error returned from Mollie: ' .  Sumo\Language::getVar('APP_MOLLIE_ERROR_' . strtoupper($response['error']['type'])));
                    return false;
                }
                else {
                    if (!count($testData['data'])) {
                        Sumo\Logger::warning('[Mollie] API Key is valid, but no payment options are available!');
                        return false;
                    }
                    else {
                        $countDisabled = 0;
                        foreach ($testData['data'] as $list) {
                            if (!isset($data[$list['id']]['enabled']) || !$data[$list['id']]['enabled']) {
                                $countDisabled++;
                            }
                        }
                        if ($countDisabled == count($testData['data'])) {
                            Sumo\Logger::warning('[Mollie] There are no payment options enabled');
                            return false;
                        }
                    }
                }
            }
            catch (\Exception $e) {
                Sumo\Logger::warning('[Mollie] Unknown API error occured');
                return false;
            }
        }

        if (!isset($data['tax'])) {
            Sumo\Logger::warning('[Mollie] Tax is not set');
            return false;
        }

        if (empty($data['instructions'][$this->config->get('language_id')])) {
            Sumo\Logger::warning('[Mollie] Instructions for default language is not set');
            return false;
        }
        unset($data['instructions']);

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
