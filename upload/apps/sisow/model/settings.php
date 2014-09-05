<?php
namespace Sisow;
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
        self::query("DELETE FROM " . $this->baseTable . " WHERE store_id = :id AND setting_name != 'idontwantdiscount' AND setting_name != 'ialreadyhavediscount'", array('id' => $store_id));

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

        if (!count($data['merchant'])) {
            return false;
        }
        else {
            if (empty($data['merchant']['id'])) {
                Sumo\Logger::warning('[Sisow] Merchant id is empty');
                return false;
            }
            if (empty($data['merchant']['key'])) {
                Sumo\Logger::warning('[Sisow] Merchant key is empty');
                return false;
            }
            include('api.php');
            $api = new ModelApi($this->registry);
            try {
                $api->setMerchant($data['merchant']['id'], $data['merchant']['key']);
                $methods = $api->callMethods();
                if (isset($methods['error'])) {
                    Sumo\Logger::warning('[Sisow] Merchant id/key is invalid');
                    return false;
                }
                else if (!count($methods['merchant']['payments'])) {
                    Sumo\Logger::warning('[Sisow] Merchant is valid, but no payment options are available');
                    return false;
                }
            }
            catch (\Exception $e) {
                Sumo\Logger::warning('[Sisow] Unexpected error occured while validating merchant id/key');
                return false;
            }
        }

        if (!count($data['status'])) {
            return false;
        }
        foreach ($data['status'] as $type => $id) {
            if ($id <= 0) {
                Sumo\Logger::warning('[Sisow] Status for ' . $type . ' is empty');
                return false;
            }
        }

        if (!count($data['purchaseid'])) {
            return false;
        }
        else if (empty($data['purchaseid'][$this->config->get('language_id')])) {
            Sumo\Logger::warning('[Sisow] Purchase description for default language is empty');
            return false;
        }

        if (empty($data['tax'])) {
            Sumo\Logger::warning('[Sisow] No tax has been set');
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
