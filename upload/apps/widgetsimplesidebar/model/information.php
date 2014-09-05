<?php
namespace Widgetsimplesidebar;
use App;
use Sumo;
class ModelInformation extends App\Model
{
    public function getItems($active = '')
    {
        $cache = 'wss_information';
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && count($cache)) {
            return $result;
        }

        $result = array();
        $data = $this->fetchAll(
            "SELECT i.information_id
            FROM PREFIX_information AS i
            LEFT JOIN PREFIX_information_to_store AS its
                ON its.information_id = i.information_id
            WHERE status = 1
                AND parent_id = 0
                AND store_id = " . $this->config->get('store_id') . "
            ORDER BY sort_order"
        );

        foreach ($data as $item) {
            $children = array();
            $check = $this->fetchAll("SELECT information_id FROM PREFIX_information WHERE parent_id = :id AND status = 1 ORDER BY sort_order", array('id' => $item['information_id']));
            if (is_array($check)) {
                foreach ($check as $list) {
                    $children[] = $this->getInformation($list['information_id']);
                }
            }

            $item = $this->getInformation($item['information_id']);
            $item['children'] = $children;

            $result[] = $item;
        }

        Sumo\Cache::set($cache, $result);
        return $result;

    }

    public function getInformation($item_id)
    {
        $cache = 'wss_information.' . $item_id;
        $result = Sumo\Cache::find($cache);
        if (is_array($result) && count($result)) {
            return $result;
        }

        $result = $this->query(
            "SELECT * FROM PREFIX_information_description WHERE information_id = :id AND language_id = :lid",
            array('id' => $item_id, 'lid' => $this->config->get('language_id'))
        )->fetch();

        Sumo\Cache::set($cache, $result);
        return $result;
    }
}
