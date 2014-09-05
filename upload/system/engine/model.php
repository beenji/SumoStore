<?php
namespace Sumo;
abstract class Model extends Database
{
    protected $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }
}

namespace App;
use Sumo;

class Model extends Sumo\Model
{
    protected $baseTable, $app_id;

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->baseTable = 'PREFIX_app_' . strtolower($this->registry->get('currentApp'));
    }

    public function setBaseTable($name)
    {
        $this->baseTable = $name;
    }

    private function getAppId()
    {
        if (!$this->app_id) {
            $check = Sumo\Database::query('SELECT app_id FROM PREFIX_apps WHERE list_name = :name', array('name' => strtolower($this->registry->get('currentApp'))))->fetch();
            if (is_array($check)) {
                $this->app_id = $check['app_id'];
                return $check['app_id'];
            }
            return false;
        }
        return $this->app_id;
    }

    public function setAppStatus($store_id, $active = 0)
    {
        try {
            $list = array();
            $list['app']    = $this->getAppId();
            $list['store']  = $store_id;
            Sumo\Database::query("DELETE FROM PREFIX_apps_active WHERE app_id = :app AND store_id = :store", $list);
            $list['active'] = $active;
            Sumo\Database::query("INSERT INTO PREFIX_apps_active SET app_id = :app, store_id = :store, active = :active", $list);
        }
        catch (\Exception $e) {
            Sumo\Logger::error('Changing app status failed: ' . print_r($e,true));
        }
    }

    public function select($fields = '*', $table = 'default', $where = '', $order = '')
    {
        if ($fields == '*' || empty($fields)) {
            $fields = '*';
        }
        $sql = 'SELECT ' . $fields . ' FROM ';
        if ($table == 'default' || empty($table)) {
            $table = $this->baseTable;
        }
        $sql .= $table;
        if (!empty($where)) {
            $sql .= ' WHERE ' . $where;
        }
        if (!empty($order)) {
            $sql .= ' ORDER BY ' . $order;
        }

        return Sumo\Database::fetchAll($sql);
    }

    public function checkSettings()
    {
        return true;
    }

}
