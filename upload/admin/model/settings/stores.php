<?php
namespace Sumo;

class ModelSettingsStores extends Model
{
    private $settings, $stores;

    public function getStores($refresh = false)
    {
        if (is_array($this->stores) && count($this->stores) && !$refresh) {
            return $this->stores;
        }
        foreach (Database::fetchAll("SELECT store_id, name, base_http, base_https, base_default FROM PREFIX_stores ORDER BY store_id ASC") as $list) {
            $this->stores[$list['store_id']] = $list;
        }
        return $this->stores;
    }

    public function getStore($store_id)
    {
        if (!is_array($this->stores) || isset($this->stores[$store_id])) {
            $this->getStores(true);
        }
        return $this->stores[$store_id];
    }

    public function getSettings($store_id, $refresh = false)
    {
        if (is_array($this->settings) && count($this->settings) && !$refresh) {
            return $this->settings;
        }

        $this->settings[$store_id] = $this->getStore($store_id);

        foreach (Database::fetchAll("SELECT setting_name, setting_value, is_json FROM PREFIX_settings_stores WHERE store_id = :id", array('id' => $store_id)) as $list) {
            $this->settings[$store_id][$list['setting_name']] = $list['is_json'] ? json_decode($list['setting_value'], true) : $list['setting_value'];
        }
        return $this->settings[$store_id];
    }

    public function getSetting($store_id, $key)
    {
        if (empty($key)) {
            throw new \Exception('ModelSettingsGeneral->getSetting requires a non-empty $key');
        }

        if (!is_array($this->settings) || !isset($this->settings[$store_id][$key])) {
            $this->getSettings($store_id, true);
            if (!isset($this->settings[$store_id][$key])) {
                return false;
            }
        }
        return $this->settings[$store_id][$key];
    }

    public function setSetting($store_id, $key, $value)
    {
        if (empty($key)) {
            return false;
        }
        Database::query("DELETE FROM PREFIX_settings_stores WHERE setting_name = :name AND store_id = :store", array('name' => $key, 'store' => $store_id));

        if ($key == 'name' || $key == 'base_http' || $key == 'base_https' || $key == 'base_default') {
            Database::query("UPDATE PREFIX_stores SET " . $key . " = :value WHERE store_id = :store", array('store' => $store_id, 'value' => $value));
            return;
        }

        Database::query(
            "INSERT INTO PREFIX_settings_stores SET store_id = :store, setting_name = :key, setting_value = :value, is_json = :json",
            array(
                'store' => $store_id,
                'key'   => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'json'  => is_array($value) ? 1 : 0
            )
        );
    }

    public function setSettings($store_id, $array)
    {
        if (!is_numeric($store_id)) {
            Database::query("INSERT INTO PREFIX_stores SET name = :name", array('name' => crc32(microtime(true))));
            $store_id = Database::lastInsertId();
            return $this->setSettings($store_id, $array);
        }
        if (!is_array($array)) {
            return;
        }

        foreach ($array as $key => $value) {
            $this->setSetting($store_id, $key, $value);
        }
    }

    public function getTotalStoresByCountryId($country_id)
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_settings_stores WHERE setting_name = 'country_id' AND setting_value = :countryID AND store_id != '0'", array('couuntryID' => $country_id))->fetch();

        return $data['total'];
    }

    public function getTotalStoresByZoneId($zone_id)
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_settings_stores WHERE setting_name = 'zone_id' AND setting_value = :zoneID AND store_id != '0'", array('zoneID' => $zone_id))->fetch();

        return $data['total'];
    }

    public function getTotalStoresByCustomerGroupId($customer_group_id)
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_settings_stores WHERE setting_name = 'customer_group_id' AND setting_value = :customerGroupID AND store_id != '0'", array('customerGroupID' => $customer_group_id))->fetch();

        return $data['total'];
    }

    public function removeStore($store_id)
    {
        if (!$store_id) {
            return;
        }

        // Remove apps that are installed on the store
        $this->query("DELETE FROM PREFIX_apps_active WHERE store_id = :id", array('id' => $store_id));

        // Fetch blogs that are added for this store
        foreach ($this->fetchAll("SELECT blog_id FROM PREFIX_blog WHERE store_id = :id", array('id' => $store_id)) as $blog) {
            $this->query("DELETE FROM PREFIX_blog_description WHERE blog_id = :id", array('id' => $blog['blog_id']));
        }
        $this->query("DELETE FROM PREFIX_blog WHERE store_id = :id", array('id' => $store_id));


        // Fetch categories that are linked to this store
        foreach ($this->fetchAll("SELECT category_id FROM PREFIX_category_to_store WHERE store_id = :id", array('id' => $store_id)) as $cat) {
            $this->query("DELETE FROM PREFIX_category_to_store WHERE category_id = :id AND store_id = :sid", array('id' => $cat['category_id'], 'sid' => $store_id));
            $check = $this->query("SELECT COUNT(*) AS total FROM PREFIX_category_to_store WHERE category_id = :id", array('id' => $cat['category_id']))->fetch();
            if (!$check['total']) {
                // Prevent "floating" category, remove all references
                $this->query("DELETE FROM PREFIX_category WHERE category_id = :id", array('id' => $cat['category_id']));
                $this->query("DELETE FROM PREFIX_category_description WHERE category_id = :id", array('id' => $cat['category_id']));
                $this->query("DELETE FROM PREFIX_category_path WHERE category_id = :id OR path_id = :cid", array('id' => $cat['category_id'], 'cid' => $cat['category_id']));
                $this->query("DELETE FROM PREFIX_coupon_category WHERE category_id = :id", array('id' => $cat['category_id']));
                $this->query("DELETE FROM PREFIX_url_alias WHERE query = :query", array('query' => 'category_id=' . $cat['category_id']));

                // Fetch all products that are linked to that category
                foreach ($this->fetchAll("SELECT product_id FROM PREFIX_product_to_category WHERE category_id = :id", array('id' => $cat['category_id'])) as $product) {
                    $this->query("DELETE FROM PREFIX_product_to_category WHERE category_id = :id", array('id' => $cat['category_id']));
                    $check2 = $this->query("SELECT COUNT(*) AS total FROM PREFIX_product_to_category WHERE product_id = :id", array('id' => $product['product_id']))->fetch();
                    if (!$check2['total']) {
                        // Prevent "floating" products, remove all references
                        $this->query("DELETE FROM PREFIX_product WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_attribute WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_description WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_discount WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_image WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_option WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_option_description WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_related WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_special WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_to_category WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_to_download WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_product_to_store WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_review WHERE product_id = :product_id", $product);
                        $this->query("DELETE FROM PREFIX_url_alias WHERE query = :query", array('query' => 'product_id=' . $product['product_id']));
                    }
                }
            }
        }

        // Fetch all customers from that store
        /*
        foreach ($this->fetchAll("SELECT customer_id FROM PREFIX_customer WHERE store_id = :id", array('id' => $store_id)) as $customer) {
            $this->query("DELETE FROM PREFIX_customer_history WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_customer_ip WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_customer_login_history WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_customer_online WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_reward WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_customer_transaction WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_address WHERE customer_id = :id", array('id' => $customer['customer_id']));
            $this->query("DELETE FROM PREFIX_review WHERE customer_id = :id", array('id' => $customer['customer_id']));
        }
        */

        // Instead of removing customer data, update customers to main store
        $this->query("UPDATE PREFIX_customer SET store_id = 0 WHERE store_id = :id", array('id' => $store_id));

        // Fetch information pages that are added for this store
        foreach ($this->fetchAll("SELECT information_id FROM PREFIX_information_to_store WHERE store_id = :id", array('id' => $store_id)) as $information) {
            $this->query("DELETE FROM PREFIX_information_to_store WHERE store_id = :id", array('id' => $store_id));
            $check = $this->query("SELECT COUNT(*) AS total FROM PREFIX_information_to_store WHERE information_id = :id", array('id' => $information['information_id']));
            if (!count($check)) {
                $this->query("DELETE FROM PREFIX_information_description WHERE information_id = :id", array('id' => $information['information_id']));
                $this->query("DELETE FROM PREFIX_information WHERE information_id = :id", array('id' => $information['id']));
            }
        }

        // Remove all aliases
        $this->query("DELETE FROM PREFIX_url_alias WHERE store_id = :id", array('id' => $store_id));

        // Remove all settings
        $this->query("DELETE FROM PREFIX_settings_stores WHERE store_id = :id", array('id' => $store_id));

        // And finally...
        $this->query("DELETE FROM PREFIX_stores WHERE store_id = :id", array('id' => $store_id));

        Cache::removeAll();
    }
}
