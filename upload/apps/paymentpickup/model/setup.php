<?php
namespace Paymentpickup;
use Sumo;
use App;

class ModelSetup extends App\Model
{
    public function install()
    {
        // Create settings table
        Sumo\Database::query("
            CREATE TABLE IF NOT EXISTS PREFIX_app_paymentpickup (
                `setting_id` int(11) NOT NULL AUTO_INCREMENT,
                `store_id` int(11) NOT NULL,
                `setting_name` varchar(255) NOT NULL,
                `setting_value` text,
                `json` int(1) DEFAULT 0,
                PRIMARY KEY (`setting_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");

        // Insert necessary translations
        
        // First create a translation key
        Sumo\Database::query("INSERT INTO PREFIX_translations_keys SET name = 'APP_PAYMENTPICKUP_TITLE', date_added = :date", array('date' => date('Y-m-d H:i:s')));
        // Then insert the translation value
        Sumo\Database::query("INSERT INTO PREFIX_tanslations SET key_id = :id, value = 'Betalen bij afhalen', language_id = 1", array('id' => Sumo\Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_TITLE'")->fetchColumn()));

        Sumo\Database::query("INSERT INTO PREFIX_translations_keys SET name = 'APP_PAYMENTPICKUP_ENABLE', date_added = :date", array('date' => date('Y-m-d H:i:s')));
        Sumo\Database::query("INSERT INTO PREFIX_tanslations SET key_id = :id, value = 'Betalen bij afhalen activeren?', language_id = 1", array('id' => Sumo\Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_ENABLE'")->fetchColumn()));

        Sumo\Database::query("INSERT INTO PREFIX_translations_keys SET name = 'APP_PAYMENTPICKUP_PAYMENT_STATUS', date_added = :date", array('date' => date('Y-m-d H:i:s')));
        Sumo\Database::query("INSERT INTO PREFIX_tanslations SET key_id = :id, value = 'Betaalstatus', language_id = 1", array('id' => Sumo\Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_PAYMENT_STATUS'")->fetchColumn()));

        return true;
    }

    public function deinstall()
    {
        // Remove the settings table
        Sumo\Database::query("DROP TABLE IF EXISTS PREFIX_app_paymentpickup");

        // Remove translation values
        Sumo\Database::query("DELETE FROM PREFIX_tanslations WHERE key_id = :id", array('id' => Sumo\Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_TITLE'")->fetchColumn()));
        Sumo\Database::query("DELETE FROM PREFIX_tanslations WHERE key_id = :id", array('id' => Sumo\Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_ENABLE'")->fetchColumn()));
        Sumo\Database::query("DELETE FROM PREFIX_tanslations WHERE key_id = :id", array('id' => Sumo\Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_PAYMENT_STATUS'")->fetchColumn()));

        // Finally, remove translation keys
        Sumo\Database::query("DELETE FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_TITLE'");
        Sumo\Database::query("DELETE FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_ENABLE'");
        Sumo\Database::query("DELETE FROM PREFIX_translations_keys WHERE name = 'APP_PAYMENTPICKUP_PAYMENT_STATUS'");
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
