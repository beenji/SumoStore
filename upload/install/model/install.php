<?php
namespace Sumo;
class ModelInstall extends Model
{
    private $data;
    public function start($data)
    {
        try {
            $this->data = $data;
            $this->dropTables();
            $this->createTables();
            $this->fillTablesWithDefaults();
            $this->finalize();
        }
        catch (\Exception $e) {
            exit(print_r($e,true));
        }
    }

    private function createTables()
    {
        $file = DIR_SUMOSTORE . 'install/sumostore.sql';
        $lines = file($file);

        if ($lines) {
            $sql = '';
            Database::query("SET NAMES 'utf8'");
            Database::query("SET CHARACTER SET utf8");
            foreach($lines as $line) {
                if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
                    $sql .= $line;
                    if (preg_match('/;\s*$/', $line)) {
                        Database::query($sql);
                        $sql = '';
                    }
                }
            }
        }
        else {
            exit('WARNING: DOWNLOAD CORRUPT. SQL FILE NOT FOUND!');
        }
    }

    private function fillTablesWithDefaults()
    {
        $host = HTTP_HOST;
        $host = str_replace('install/', '', $host);
        Database::query(
            "INSERT INTO PREFIX_stores SET store_id = 0, name = :name, base_http = :url_1, base_https = :url_2, base_default = :default",
            array(
                'name'      => $this->data['store_name'],
                'url_1'     => $host,
                'url_2'     => $host,
                'default'   => HTTP_DEFAULT
            )
        );

        $store_id = Database::lastInsertId();
        Database::query("UPDATE PREFIX_stores SET store_id = 0 WHERE store_id = :id", array('id' => $store_id));
        Database::query("ALTER TABLE PREFIX_stores AUTO_INCREMENT = 1");

        $salt = substr(md5(uniqid(rand(), true)), 0, 9);
        Database::query(
            "INSERT INTO PREFIX_user SET
                username        = :username,
                salt            = :salt,
                password        = :password,
                firstname       = :firstname,
                lastname        = '',
                email           = :email,
                user_group_id   = 1,
                status          = 1,
                date_added      = NOW()",
            array(
                'username'      => $this->request->post['username'],
                'salt'          => $salt,
                'password'      => sha1($salt . sha1($salt . sha1($this->request->post['password']))),
                'firstname'     => $this->request->post['username'],
                'email'         => $this->request->post['email']
            )
        );

        Database::query(
            "INSERT INTO PREFIX_settings SET setting_name = 'email', setting_value = :value, is_json = 0",
            array(
                'value' => $this->request->post['store_email'],
            )
        );
        Database::query(
            "INSERT INTO PREFIX_settings SET setting_name = 'country_id', setting_value = :value, is_json = 0",
            array(
                'value' => $this->request->post['country_id'],
            )
        );
        if (!empty($this->request->post['zone_id'])) {
            Database::query(
                "INSERT INTO PREFIX_settings SET setting_name = 'zone_id', setting_value = :value, is_json = 0",
                array(
                    'value' => $this->request->post['zone_id'],
                )
            );
        }
        if ($this->session->data['language'] == 'nl') {
            $language_id = 1;
            Database::query("UPDATE PREFIX_language SET status = 0 WHERE language_id = 5");
        }
        else {
            $language_id = 5;
            Database::query("UPDATE PREFIX_language SET status = 0 WHERE language_id = 1");
        }
        Database::query("UPDATE PREFIX_language SET status = 0 WHERE language_id = 6");
        Database::query(
            "UPDATE PREFIX_settings SET setting_name = 'language_id', setting_value = :value, is_json = 0 WHERE setting_name = 'language_id'",
            array(
                'value' => $language_id,
            )
        );
                $sql = <<<SQL
INSERT INTO `sumo_settings_stores` (`id`, `store_id`, `setting_name`, `setting_value`, `is_json`) VALUES
(5036, 0, 'template', 'base', 0),
(5037, 0, 'logo', 'data/0cb6ebd7ebfedbee13.png', 0),
(5038, 0, 'icon', 'data/0ccaf64b2e1dbee13.png', 0),
(5039, 0, 'header_base', '{"1":"logo","2":"search","3":"cart"}', 1),
(5040, 0, 'footer_base', '{"blocks":{"amount":"2","blocks":[{"title":{"5":"","1":"","6":""},"type":"","links":{"1":{"url":"","name":{"5":"","1":"","6":""}}},"content":{"5":"","1":"","6":""}},{"title":{"5":"Test","1":"Test","6":"Test"},"type":"content","links":{"1":{"url":"","name":{"5":"","1":"","6":""}}},"content":{"5":"&lt;p&gt;Test&lt;\/p&gt;","1":"&lt;p&gt;Test content&lt;\/p&gt;","6":"&lt;p&gt;test&lt;\/p&gt;"}},{"title":{"5":"Linkjes","1":"Linkjes","6":"Linkjes"},"type":"links","links":{"1":{"url":"\/information\/information\/?information_id=1","name":{"5":"","1":"Algemene voorwaarden","6":""}}},"content":{"5":"","1":"","6":""}},{"title":{"5":"","1":"","6":""},"type":"","links":{"1":{"url":"","name":{"5":"","1":"","6":""}}},"content":{"5":"","1":"","6":""}},{"title":{"5":"","1":"","6":""},"type":"","links":{"1":{"url":"","name":{"5":"","1":"","6":""}}},"content":{"5":"","1":"","6":""}}]},"copyright":{"notice":{"5":"&lt;p&gt;&amp;copy; Copyright [websitename], 2013-[currentyear]&lt;\/p&gt;","1":"&lt;p&gt;&amp;copy; Copyright [websitename], 2013-[currentyear]&lt;\/p&gt;","6":"&lt;p&gt;&amp;copy; Copyright [websitename], 2013-[currentyear]&lt;\/p&gt;"},"powered_by":"1"}}', 1);
SQL;
        Database::query($sql);
        Database::query(
            "INSERT INTO PREFIX_settings_stores SET store_id = 0, setting_name = 'category', setting_value = :value, is_json = 0",
            array(
                'value' => $this->request->post['category'],
            )
        );
        Database::query(
            "INSERT INTO PREFIX_settings_stores SET store_id = 0, setting_name = 'title', setting_value = :value, is_json = 0",
            array(
                'value' => $this->request->post['store_name'],
            )
        );
    }

    private function finalize()
    {
        if ($this->session->data['language'] == 'nl') {
            $language_id = 1;
        }
        else {
            $language_id = 5;
        }
        $default = array('inventory', 'banktransfer', 'cashondelivery', 'sumoguardbasic', 'shippingfree', 'widgetsimplesidebar', 'newsletterbasic');
        foreach ($default as $appname) {
            if (file_exists(DIR_HOME . 'apps/' . $appname . '/model/setup.php')) {
                include(DIR_HOME . 'apps/' . $appname . '/model/setup.php');
                include(DIR_HOME . 'apps/' . $appname . '/information.php');

                if (!isset($app)) {
                    exit($appname . ' does not have information');
                }
                else if (!isset($app[$appname])) {
                    exit($appname . ' does not seem to be the correct name');
                }
                else if (empty($app[$appname]['app_id'])) {
                    exit($appname . ' does not seem to have an app_id');
                }

                $call = $appname . '\ModelSetup';
                $call = new $call($this->registry);

                try {
                    $call->install();
                    Database::query("
                        INSERT INTO PREFIX_apps
                        SET app_id          = :app_id,
                            name            = :name,
                            list_name       = :list_name,
                            description     = :description,
                            category        = :category,
                            installed       = 1",
                        array(
                            'app_id'        => $app[$appname]['app_id'],
                            'name'          => isset($app[$appname]['name'][$language_id]) ? $app[$appname]['name'][$language_id] : reset($app[$appname]['name']),
                            'list_name'     => $appname,
                            'description'   => isset($app[$appname]['description'][$language_id]) ? $app[$appname]['description'][$language_id] : reset($app[$appname]['description'][$language_id]),
                            'category'      => $app[$appname]['category']
                        )
                    );
                    Database::query("
                        INSERT INTO PREFIX_apps_active
                        SET app_id      = :app_id,
                            store_id    = 0,
                            active      = 1",
                        array(
                            'app_id'    => $app[$appname]['app_id']
                        )
                    );
                }
                catch (\Exception $e) { }
            }
        }
    }

    private function dropTables()
    {
        $tables = array(
            'address', 'admin_menu', 'apps_active', 'app_widgetsimplefooter', 'app_widgetsimpleheader', 'app_widgetsimpleproduct', 'app_widgetsimplesidebar', 'app_widgetsocialmedia', 'attribute', 'attribute_description', 'attribute_group', 'attribute_group_description', 'blog', 'blog_description', 'category', 'category_description', 'category_path', 'category_to_store', 'country', 'coupon', 'coupon_category', 'coupon_history', 'coupon_product', 'creditor', 'currency', 'customer', 'customer_ban_ip', 'customer_group', 'customer_group_description', 'customer_history', 'customer_ip', 'customer_login_history', 'customer_online', 'customer_reward', 'customer_transaction', 'download', 'download_description', 'geo_zone', 'information', 'information_description', 'information_to_store', 'invoice', 'invoice_line', 'invoice_total', 'language', 'length_class', 'length_class_description', 'mails', 'mails_content', 'mails_to_events', 'manufacturer', 'manufacturer_to_store', 'orders', 'orders_data', 'orders_download', 'orders_history', 'orders_lines', 'orders_totals', 'orders_to_invoice', 'order_status', 'product', 'product_attribute', 'product_description', 'product_discount', 'product_image', 'product_option', 'product_option_description', 'product_option_value', 'product_option_value_description', 'product_related', 'product_special', 'product_to_category', 'product_to_download', 'product_to_store', 'register', 'register_data', 'return', 'return_action', 'return_history', 'return_reason', 'return_status', 'review', 'settings', 'settings_stores', 'stock_status', 'stores', 'todo', 'translations', 'translations_keys', 'url_alias', 'user', 'user_group', 'volume', 'volume_discounts', 'voucher', 'voucher_history', 'voucher_theme', 'voucher_theme_description', 'weight_class', 'weight_class_description', 'zone', 'zone_to_geo_zone', 'apps'
        );
        foreach ($tables as $table) {
            Database::query("DROP TABLE IF EXISTS PREFIX_" . $table);
        }
    }
}
