<?php
namespace Sumo;
class ModelLocalisationOrderStatus extends Model
{
    public function getOrderStatuses($data = array())
    {
        $cache = 'order_status.' . $this->config->get('language_id');
        $data = Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $data = $this->fetchAll("SELECT order_status_id, name FROM PREFIX_order_status WHERE language_id = :id ORDER BY name", array('id' => $this->config->get('language_id')));
        Cache::set($cache, $data);

        return $data;

    }
}
