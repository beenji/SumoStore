<?php
namespace Sumo;
class ModelCatalogCategory extends Model
{
    public function getCategory($category_id)
    {
        $cacheFile = 'categories.' . $category_id . '-' . (int)$this->config->get('language-id') . '-' . (int)$this->config->get('store_id');
        $cache = Cache::find($cacheFile, 'data');
        if (is_array($cache) && count($cache)) {
            return $cache;
        }

        $result = self::query(
            "SELECT DISTINCT *
            FROM PREFIX_category c
            LEFT JOIN PREFIX_category_description cd
                ON (c.category_id = cd.category_id)
            LEFT JOIN PREFIX_category_to_store c2s
                ON (c.category_id = c2s.category_id)
            WHERE c.category_id = :category
                AND cd.language_id = :language
                AND c2s.store_id = :store_id
                AND c.status = 1",
            array(
                'category'  => $category_id,
                'language'  => $this->config->get('language_id'),
                'store_id'  => $this->config->get('store_id')
            )
        )->fetch();

        Cache::set($cacheFile, 'data', $result);

        return $result;
    }

    public function getCategories($parent_id = 0)
    {
        $cacheFile = 'categories.parent-' . $parent_id . '-' . (int)$this->config->get('language-id') . '-' . (int)$this->config->get('store_id');
        $cache = Cache::find($cacheFile, 'total');
        if (is_array($cache) && count($cache)) {
            //return $cache;
        }

        $data = self::fetchAll(
            "SELECT *
            FROM PREFIX_category c
            LEFT JOIN PREFIX_category_description cd
                ON (c.category_id = cd.category_id)
            LEFT JOIN PREFIX_category_to_store c2s
                ON (c.category_id = c2s.category_id)
            WHERE c.parent_id = :parent
                AND cd.language_id = :language
                AND c2s.store_id = :store_id
                AND c.status = 1
            ORDER BY c.sort_order ASC, c.parent_id ASC",
            array(
                'parent'    => $parent_id,
                'language'  => $this->config->get('language_id'),
                'store_id'  => $this->config->get('store_id')
            )
        );

        Cache::set($cacheFile, 'total', $data);
        return $data;
    }

    public function getCategoryFilters($category_id)
    {
        $cacheFile = 'categories.filters-' . $category_id . '-' . (int)$this->config->get('language-id') . '-' . (int)$this->config->get('store_id');
        $cache = Cache::find($cacheFile);
        if (is_array($cache) && count($cache)) {
            return $cache;
        }

        $implode = array();

        $data = self::fetchAll("SELECT filter_id FROM PREFIX_category_filter WHERE category_id = :id", array('id' => $category_id));
        foreach ($data as $result) {
            $implode[] = (int)$result['filter_id'];
        }

        $filter_group_data = array();

        if ($implode) {
            $filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM PREFIX_filter f LEFT JOIN PREFIX_filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN PREFIX_filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

            foreach ($filter_group_query->rows as $filter_group) {
                $filter_data = array();

                $filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM PREFIX_filter f LEFT JOIN PREFIX_filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

                foreach ($filter_query->rows as $filter) {
                    $filter_data[] = array(
                        'filter_id' => $filter['filter_id'],
                        'name'      => $filter['name']
                    );
                }

                if ($filter_data) {
                    $filter_group_data[] = array(
                        'filter_group_id' => $filter_group['filter_group_id'],
                        'name'            => $filter_group['name'],
                        'filter'          => $filter_data
                    );
                }
            }
        }
        Cache::set($cacheFile, $filter_group_data);
        return $filter_group_data;
    }

    public function getTotalCategoriesByCategoryId($parent_id = 0)
    {
        $cacheFile = 'categories.totals-' . $parent_id . '-' . (int)$this->config->get('language-id') . '-' . (int)$this->config->get('store_id');
        $cache = Cache::find($cacheFile);
        if (is_array($cache) && count($cache)) {
            return $cache;
        }
        $result = self::query("SELECT COUNT(*) AS total FROM PREFIX_category c LEFT JOIN PREFIX_category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('store_id') . "' AND c.status = '1'")->fetch();

        Cache::set($cacheFile, $result);
        return $result;
    }
}
