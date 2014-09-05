<?php
namespace Widgetsimplesidebar;
use App;
use Sumo;
class ModelCategory extends App\Model
{
    public function getCategories($active = '')
    {
        $cache = 'wss_category';
        $data = Sumo\Cache::find($cache);
        if (is_array($data) && count($data)) {
            return $data;
        }

        $return = array();

        $categories = $this->fetchAll(
            "SELECT c.category_id, c.image, c.parent_id, c.sort_order, cd.name, cd.title, cd.description, cd.meta_description, cd.meta_keyword
            FROM PREFIX_category AS c
            LEFT JOIN PREFIX_category_description AS cd
                ON cd.category_id = c.category_id
            LEFT JOIN PREFIX_category_to_store AS cts
                ON cts.category_id = c.category_id
            WHERE cts.store_id = :store
                AND cd.language_id = :language
                AND status = 1
            ORDER BY c.parent_id, c.sort_order",
            array(
                'store'     => $this->config->get('store_id'),
                'language'  => $this->config->get('language_id')
            )
        );

        // First level, head categories
        foreach ($categories as $id => $list) {
            if ($active == $list['category_id']) {
                //$list['active'] = true;
            }
            if ($list['parent_id'] == 0) {
                $list['href'] = $this->url->link('product/category', 'path=' . $list['category_id']);
                $return[$list['category_id']] = $list;
                unset($categories[$id]);
            }
        }

        // Second level, subcategories
        foreach ($categories as $id => $list) {
            if (isset($return[$list['parent_id']])) {
                $list['href'] = $this->url->link('product/category', 'path=' . $list['parent_id'] . '_' . $list['category_id']);
                $return[$list['parent_id']]['children'][$list['category_id']] = $list;
                unset($categories[$id]);
            }
        }

        // Third level, sub-subcategories
        foreach ($categories as $id => $list) {
            foreach ($return as $key => $test) {
                if (!isset($test['children'])) {
                    continue;
                }
                if (isset($test['children'][$list['parent_id']])) {
                    $list['href'] = $this->url->link('product/category', 'path=' . $key . '_' . $list['parent_id'] . '_' . $list['category_id']);
                    $return[$key]['children'][$list['parent_id']]['children'][$list['category_id']] = $list;
                    unset($categories[$id]);
                }
            }
        }

        // Fourth level, sub-sub-subcategories
        foreach ($categories as $id => $list) {
            foreach ($return as $head => $main) {
                if (!isset($main['children'])) {
                    continue;
                }
                foreach ($main['children'] as $mid => $kids) {
                    if ($kids['category_id'] == $list['parent_id']) {
                        $list['href'] = $this->url->link('product/category', 'path=' . $head . '_' . $mid . '_' . $list['parent_id'] . '_' . $list['category_id']);
                        $return[$head]['children'][$mid]['children'][$list['category_id']] = $list;
                        continue;
                    }
                    if (!isset($kids['children'])) {
                        continue;
                    }

                    foreach ($kids['children'] as $id => $kid) {
                        if ($kid['category_id'] == $list['parent_id']) {
                            $list['href'] = $this->url->link('product/category', 'path=' . $head . '_' . $mid . '_' . $list['parent_id'] . '_' . $list['category_id']);
                            $return[$head]['children'][$mid]['children'][$id]['children'][$list['category_id']] = $list;
                            continue;
                        }
                    }
                }
            }
        }

        Sumo\Cache::set($cache, $return);
        return $return;
    }
}
