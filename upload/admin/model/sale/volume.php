<?php
namespace Sumo;
class ModelSaleVolume extends Model
{

    public function getVolumes()
    {
        $cache = $this->cache->get('volumes');
        if (is_array($cache) && count($cache)) {
            return $cache;
        }

        $query = $this->db->query("
            SELECT volume_id, name, type FROM " . DB_PREFIX . "volume ORDER BY name"
        );
        $return = array();
        foreach ($query->rows as $list) {
            $sub = array();
            $subQuery = $this->db->query("
                SELECT discount_id, quantity, discount FROM " . DB_PREFIX . "volume_discounts WHERE volume_id = " . $list['volume_id']
            );
            foreach ($subQuery->rows as $list2) {
                $sub[$list2['discount_id']] = $list2;
            }
            $list['discounts'] = $sub;
            $return[$list['volume_id']] = $list;
        }

        $this->cache->set('volumes', $return);

        return $return;
    }
}
