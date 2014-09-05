<?php
namespace Sumo;
class ModelCatalogData extends Model
{
	public function getProducts($data)
	{
		$columns = implode(',', $data['fields']);

		// Set limit
		if (mb_strlen($data['start']) > 0 && mb_strlen($data['limit']) > 0) {
			$sql_limit = ' LIMIT ' . (int)$data['start'] . ',' . (int)$data['limit']; 
		} elseif (mb_strlen($data['limit']) > 0) {
			$sql_limit = ' LIMIT ' . (int)$data['limit'];
		} else {
			$sql_limit = '';
		}

		// Set sort
		if (mb_strlen($data['sort']) > 0 && in_array($data['sort'], $data['fields'])) {
			$sql_sort = ' ORDER BY ' . $data['sort'];
		} else {
			$sql_sort = '';
		}

		return $this->query('SELECT ' . $columns . ' 
			FROM PREFIX_product p, PREFIX_product_description pd 
			WHERE p.product_id = pd.product_id 
				AND pd.language_id = ' . $this->config->get('config_language_id') . $sql_sort . $sql_limit)->fetchAll();
	}
}