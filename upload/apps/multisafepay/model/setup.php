<?php
namespace Multisafepay;
use Sumo;
use App;

class ModelSetup extends App\Model
{
    public function install()
    {
        Sumo\Database::query("
            CREATE TABLE IF NOT EXISTS PREFIX_app_multisafepay (
                `setting_id` int(11) NOT NULL AUTO_INCREMENT,
                `store_id` int(11) NOT NULL,
                `setting_name` varchar(255) NOT NULL,
                `setting_value` text,
                `json` int(1) DEFAULT 0,
                PRIMARY KEY (`setting_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");
        Sumo\Database::query("
            CREATE TABLE IF NOT EXISTS PREFIX_app_multisafepay_payments (
                `payment_id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` int(11) NOT NULL,
                `transaction_id` varchar(32) NOT NULL,
                PRIMARY KEY (`payment_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");
        Sumo\Database::query("
            CREATE TABLE IF NOT EXISTS PREFIX_app_multisafepay_payments_info (
                `info_id` int(11) NOT NULL AUTO_INCREMENT,
                `payment_id` int(11) NOT NULL,
                `info_name` varchar(255) NOT NULL,
                `info_value` text,
                PRIMARY KEY (`info_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");
        return true;
    }

    public function deinstall()
    {
        Sumo\Database::query("DROP TABLE IF EXISTS PREFIX_app_multisafepay");
        Sumo\Database::query("DROP TABLE IF EXISTS PREFIX_app_multisafepay_payments");
        Sumo\Database::query("DROP TABLE IF EXISTS PREFIX_app_multisafepay_payments_info");
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
