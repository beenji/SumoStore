<?php
namespace Widgetsimplesidebar;
use App;
use Sumo;
class ModelUSP extends App\Model
{
    public function count($store_id = 0)
    {
        $data = $this->query("SELECT setting_value FROM PREFIX_app_widgetsimplesidebar WHERE setting_name = 'usp' AND store_id = :id", array('id' => $store_id))->fetch();
        if (empty($data) || !is_array($data)) {
            return 0;
        }
        return count(json_decode($data['setting_value'], true));
    }

    public function getUsps($store_id = 0)
    {
        $data = $this->query("SELECT setting_value FROM PREFIX_app_widgetsimplesidebar WHERE setting_name = 'usp' AND store_id = :id", array('id' => $store_id))->fetch();
        if (empty($data) || !is_array($data)) {
            return array();
        }
        return json_decode($data['setting_value'], true);
    }

    public function setUsps($store_id, $data)
    {
        $this->query("DELETE FROM PREFIX_app_widgetsimplesidebar WHERE setting_name = 'usp' AND store_id = :id", array('id' => $store_id));
        $this->query("INSERT INTO PREFIX_app_widgetsimplesidebar SET setting_name = 'usp', json = 1, setting_value = :data, store_id = :id", array('data' => json_encode($data), 'id' => $store_id));
    }
}
