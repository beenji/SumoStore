<?php
namespace Sumo;
class ModelCatalogManufacturer extends Model
{
    public function getManufacturer($manufacturer_id)
    {
        $cacheFile = 'manufacturer.' . $manufacturer_id;
        $data = Cache::find($cacheFile);
        if (!$data || !is_array($data) || empty($data)) {
            $data = Database::query("SELECT * FROM PREFIX_manufacturer m LEFT JOIN PREFIX_manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = :id AND m2s.store_id = :store", array('id' => $manufacturer_id, 'store' => $this->config->get('store_id')))->fetch();
            Cache::set($cacheFile, $data);
        }
        return $data;
    }

    public function getManufacturers($data = array())
    {
        if ($data) {
            $sql = "SELECT * FROM PREFIX_manufacturer m LEFT JOIN PREFIX_manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = :store";

            $values = array();
            $values['store'] = $this->config->get('store_id');

            $sort_data = array(
                'name',
                'sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY :sort";
                $values['sort'] =  $data['sort'];
            }
            else {
                $sql .= " ORDER BY name";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            }
            else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }
                $values['start'] = $data['start'];
                $values['limit'] = $data['limit'];
                $sql .= " LIMIT :start,:limit";
            }

            $result = Database::fetchAll($sql, $values);

            return $result;
        }
        else {
            $manufacturer_data = Cache::find('manufacturer.' . (int)$this->config->get('store_id'));

            if (!$manufacturer_data) {
                $manufacturer_data = Database::fetchAll("SELECT * FROM PREFIX_manufacturer m LEFT JOIN PREFIX_manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = :store ORDER BY name", array('store' => $this->config->get('store_id')));

                Cache::set('manufacturer.' . (int)$this->config->get('store_id'), $manufacturer_data);
            }

            return $manufacturer_data;
        }
    }
}
