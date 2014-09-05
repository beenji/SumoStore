<?php
namespace Sumo;
class Apps extends Singleton
{
    public $instance;

    public static function getAvailable($storeID, $categoryID)
    {
        return Database::fetchAll(
            "SELECT a.*
            FROM PREFIX_apps a, PREFIX_apps_active aa 
            WHERE a.app_id = aa.app_id
                AND aa.active = 1
                AND aa.store_id = :store
                AND a.category  = :cat
                AND a.installed = 1",
            array(
                'store' => $storeID,
                'cat'   => $categoryID
            )
        );
    }
}
