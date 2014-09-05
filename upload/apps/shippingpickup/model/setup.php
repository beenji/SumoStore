<?php
namespace Shippingpickup;
use Sumo;
use App;

class ModelSetup extends App\Model
{
    public function install()
    {
        Sumo\Database::query("
            CREATE TABLE IF NOT EXISTS PREFIX_app_shippingpickup (
                `setting_id` int(11) NOT NULL AUTO_INCREMENT,
                `store_id` int(11) NOT NULL,
                `setting_name` varchar(255) NOT NULL,
                `setting_value` text,
                `json` int(1) DEFAULT 0,
                PRIMARY KEY (`setting_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");
        return true;
    }

    public function deinstall()
    {
        Sumo\Database::query("DROP TABLE IF EXISTS PREFIX_app_shippingpickup");
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
