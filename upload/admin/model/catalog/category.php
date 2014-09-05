<?php
namespace Sumo;
class ModelCatalogCategory extends Model
{
    public function addCategory($data)
    {
        $order = $this->query(
            "SELECT MAX(sort_order) AS max_sort_order
            FROM PREFIX_category AS c
            LEFT JOIN PREFIX_category_to_store AS cts
                ON cts.category_id = c.category_id
            WHERE parent_id = :parent_id
                AND store_id = :store_id",
            array(
                'parent_id' => $data['parent_id'],
                'store_id'  => $data['store_id']
            )
        )->fetch();
        if (empty($order['max_sort_order'])) {
            $order['max_sort_order'] = 0;
        }
        $this->query("INSERT INTO PREFIX_category SET sort_order = :order, date_added = :date", array('order' => $order['max_sort_order'] + 1, 'date' => date('Y-m-d H:i:s')));
        $data['sort_order'] = $order['max_sort_order'] + 1;
        $category_id = $this->lastInsertId();

        return $this->editCategory($category_id, $data);
    }

    public function editCategory($category_id, $data)
    {
        $this->query(
            "UPDATE PREFIX_category
            SET parent_id       = :parent_id,
                image           = :image,
                date_modified   = :date
            WHERE category_id   = :id",
            array(
                'parent_id'     => $data['parent_id'],
                'image'         => $data['image'],
                'date'          => date('Y-m-d H:i:s'),
                'id'            => $category_id
            )
        );

        $this->query("DELETE FROM PREFIX_category_to_store WHERE category_id = :id", array('id' => $category_id));
        $this->query("DELETE FROM PREFIX_category_description WHERE category_id = :id", array('id' => $category_id));
        $this->query("DELETE FROM PREFIX_url_alias WHERE query = :query", array('query' => 'category_id=' . $data['category_store']));

        $this->query("INSERT INTO PREFIX_category_to_store SET category_id = :id, store_id = :store", array('id' => $category_id, 'store' => $data['category_store']));

        foreach ($data['category_description'] as $lid => $list) {
            $this->query(
                "INSERT INTO PREFIX_category_description
                SET category_id         = :id,
                    language_id         = :lid,
                    name                = :name,
                    title               = :title,
                    meta_keyword        = :keyword,
                    meta_description    = :meta,
                    description         = :description",
                array(
                    'id'                => $category_id,
                    'lid'               => $lid,
                    'name'              => $list['name'],
                    'title'             => $list['title'],
                    'keyword'           => $list['meta_keyword'],
                    'meta'              => $list['meta_description'],
                    'description'       => $list['description']
                )
            );
            Formatter::generateSeoUrl($list['name'], 'category_id', $category_id, $lid, $data['category_store']);
        }

        $paths = $this->fetchAll(
            "SELECT *
            FROM PREFIX_category_path
            WHERE path_id = :id
            ORDER BY level ASC",
            array('id' => $category_id)
        );
        if (count($paths)) {
            foreach ($paths as $category_path) {
                $this->query(
                    "DELETE FROM PREFIX_category_path
                    WHERE category_id   = :cid
                        AND level       < :level",
                    array(
                        'cid'           => $category_path['category_id'],
                        'level'         => $category_path['level'],
                    )
                );

                $path = array();
                $nodes = $this->fetchAll(
                    "SELECT *
                    FROM PREFIX_category_path
                    WHERE category_id = :id
                    ORDER BY level ASC",
                    array('id' => $data['parent_id'])
                );
                foreach ($nodes as $result) {
                    $path[] = $result['path_id'];
                }

                $nodes = $this->fetchAll(
                    "SELECT *
                    FROM PREFIX_category_path
                    WHERE category_id = :id
                    ORDER BY level ASC",
                    array('id' => $category_path['category_id'])
                );
                foreach ($nodes as $result) {
                    $path[] = $result['path_id'];
                }

                $level = 0;
                foreach ($path as $path_id) {
                    $this->query(
                        "REPLACE INTO PREFIX_category_path
                        SET category_id = :id,
                            path_id     = :path,
                            level       = :level",
                        array(
                            'id'        => $category_path['category_id'],
                            'path'      => $path_id,
                            'level'     => $level
                        )
                    );
                    $level++;
                }
            }
        }
        else {
            $this->query("DELETE FROM PREFIX_category_path WHERE category_id = :id", array('id' => $category_id));
            $level = 0;
            $query = $this->fetchAll("SELECT * FROM PREFIX_category_path WHERE category_id = :id ORDER BY level ASC", array('id' => $data['parent_id']));
            foreach ($query as $result) {
                $this->query(
                    "INSERT INTO PREFIX_category_path
                    SET category_id = :id,
                        path_id     = :path,
                        level       = :level",
                    array(
                        'id'        => $category_id,
                        'path'      => $result['path_id'],
                        'level'     => $level
                    )
                );
                $level++;
            }

            $this->query(
                "REPLACE INTO PREFIX_category_path
                SET category_id = :id,
                    path_id     = :path,
                    level       = :level",
                array(
                    'id'        => $category_id,
                    'path'      => $category_id,
                    'level'     => $level
                )
            );
        }

        Cache::removeAll();
    }

    public function deleteCategory($category_id)
    {
        // Reset sort_order
        $category_info = $this->getCategory($category_id);
        $this->query(
            "UPDATE PREFIX_category
            SET sort_order      = sort_order - 1
            WHERE sort_order    > :order
                AND parent_id   = :parent_id
                AND category_id IN (
                    SELECT category_id
                    FROM PREFIX_category_to_store
                    WHERE store_id = :store_id
                )",
            array(
                'order'     => $category_info['sort_order'],
                'parent_id' => (int)$category_info['parent_id'],
                'store_id'  => !empty($category_info['store_id']) ? (int)$category_info['store_id'] : 0
            )
        );

        $this->query("DELETE FROM PREFIX_category_path WHERE category_id = :id", array('id' => $category_id));

        $query = $this->fetchAll("SELECT * FROM PREFIX_category_path WHERE path_id = :id", array('id' => $category_id));

        foreach ($query as $result) {
            $this->deleteCategory($result['category_id']);
        }

        $this->query("DELETE FROM PREFIX_category               WHERE category_id = :id", array('id' => $category_id));
        $this->query("DELETE FROM PREFIX_category_description   WHERE category_id = :id", array('id' => $category_id));
        $this->query("DELETE FROM PREFIX_category_to_store      WHERE category_id = :id", array('id' => $category_id));
        $this->query("DELETE FROM PREFIX_product_to_category    WHERE category_id = :id", array('id' => $category_id));
        $this->query("DELETE FROM PREFIX_url_alias              WHERE query = :query", array('query' => 'category_id='. (int)$category_id));

        // Delete lonely products
        $this->load->model('catalog/product');

        $lonelyProducts = $this->query("SELECT product_id
            FROM PREFIX_product
            WHERE product_id NOT IN (SELECT product_id
                FROM PREFIX_product_to_category)")->fetchAll();

        foreach ($lonelyProducts as $product) {
            $this->model_catalog_product->deleteProduct($product['product_id']);
        }

        Cache::removeAll();
    }

    public function moveCategoryUp($category_id)
    {
        $category_info = $this->getCategory($category_id);



        /*
        $this->query("UPDATE PREFIX_category SET sort_order = sort_order+1 WHERE parent_id = :parent AND sort_order = :order", array('order' => $category_info['sort_order'] + 1, 'parent' => $category_info['parent_id']));
        $this->query("UPDATE PREFIX_category SET sort_order = sort_order-1, date_modified = :date WHERE category_id = :id", array('id' => $category_id, 'date' => date('Y-m-d H:i:s')));
        */

        if ($category_info['sort_order'] <= 1) {
            return;
        }

        $this->query(
            "UPDATE PREFIX_category
            SET sort_order      = :sort_order,
                date_modified   = :date
            WHERE sort_order    = :old_order
                AND parent_id   = :parent_id
                AND category_id IN (
                    SELECT category_id
                    FROM PREFIX_category_to_store
                    WHERE store_id = :store_id
                )",
            array(
                'sort_order'    => $category_info['sort_order'],
                'old_order'     => $category_info['sort_order'] -1,
                'parent_id'     => (int)$category_info['parent_id'],
                'store_id'      => !empty($category_info['store_id']) ? (int)$category_info['store_id'] : 0,
                'date'          => date('Y-m-d H:i:s')
            )
        );
        $this->query(
            "UPDATE PREFIX_category
            SET sort_order      = :new_order
            WHERE category_id   = :category",
            array(
                'new_order'     => $category_info['sort_order'] -1,
                'category'      => $category_id
            )
        );

        Cache::removeAll();
    }

    public function moveCategoryDown($category_id)
    {
        $category_info = $this->getCategory($category_id);

        $check = $this->query(
            "SELECT MAX(sort_order) AS max_sort_order
            FROM PREFIX_category AS c, PREFIX_category_to_store AS cts
            WHERE c.category_id     = cts.category_id
                AND cts.store_id    = :store
                AND c.parent_id     = :parent",
            array(
                'store'             => !empty($category_info['store_id']) ? (int)$category_info['store_id'] : 0,
                'parent'            => $category_info['parent_id']
            )
        )->fetch();
        if ($category_info['sort_order'] == $check['max_sort_order']) {
            return;
        }

        $this->query(
            "UPDATE PREFIX_category
            SET sort_order      = :sort_order,
                date_modified   = :date
            WHERE sort_order    = :old_order
                AND parent_id   = :parent_id
                AND category_id IN (
                    SELECT category_id
                    FROM PREFIX_category_to_store
                    WHERE store_id = :store_id
                )",
            array(
                'sort_order'    => $category_info['sort_order'],
                'old_order'     => $category_info['sort_order'] +1,
                'parent_id'     => (int)$category_info['parent_id'],
                'store_id'      => !empty($category_info['store_id']) ? (int)$category_info['store_id'] : 0,
                'date'          => date('Y-m-d H:i:s')
            )
        );
        $this->query(
            "UPDATE PREFIX_category
            SET sort_order      = :new_order
            WHERE category_id   = :category",
            array(
                'new_order'     => $category_info['sort_order'] +1,
                'category'      => $category_id
            )
        );

        Cache::removeAll();
    }

    public function updateStatus($category_id, $status = 1)
    {
        if ($status) {
            $check = $this->query("SELECT parent_id FROM PREFIX_category WHERE category_id = :id", array('id' => $category_id))->fetch();
            if ($check['parent_id']) {
                $check = $this->query("SELECT status FROM PREFIX_category WHERE category_id = :id", array('id' => $check['parent_id']))->fetch();
                if (!$check['status']) {
                    return false;
                }
            }
        }
        $this->query(
            "UPDATE PREFIX_category
            SET status          = :status,
                date_modified   = :date
            WHERE category_id   = :id",
            array(
                'status'        => $status,
                'date'          => date('Y-m-d H:i:s'),
                'id'            => $category_id
            )
        );
        if (!$status) {
            foreach ($this->fetchAll("SELECT category_id FROM PREFIX_category WHERE parent_id = :id", array('id' => $category_id)) as $cat) {
                $this->updateStatus($cat['category_id'], 0);
            }
        }
        Cache::removeAll();
        return true;
    }

    public function getCategory($category_id)
    {
        $query = $this->query(
            "SELECT
                DISTINCT *,
                (
                    SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR ' &gt; ')
                    FROM PREFIX_category_path cp
                    LEFT JOIN PREFIX_category_description cd1
                        ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id)
                    WHERE cp.category_id = c.category_id
                        AND cd1.language_id = '" . (int)$this->config->get('language_id') . "'
                    GROUP BY cp.category_id
                ) AS path
            FROM PREFIX_category AS c
            LEFT JOIN PREFIX_category_description AS cd2
                ON (c.category_id = cd2.category_id)
            LEFT JOIN PREFIX_category_to_store AS cts
                ON (c.category_id = cts.store_id)
            WHERE c.category_id = '" . (int)$category_id . "'
                AND cd2.language_id = '" . (int)$this->config->get('language_id') . "'");

        return $query->fetch();
    }

    public function getCategories($data = array(), $total = true)
    {
        $cache = Cache::find('category');
        if (is_array($cache) && count($cache)) {
            return $cache;
        }

        // Step 1: Select base/head categories from prefix_category
        $sql = "
            SELECT c.category_id, cts.store_id, cd.name, c.sort_order, parent_id, c.status, cd.meta_description, cd.description, cd.meta_keyword, s.name AS store_name
            FROM PREFIX_category AS c
            LEFT JOIN PREFIX_category_to_store AS cts
                ON cts.category_id = c.category_id
            LEFT JOIN PREFIX_category_description AS cd
                ON cd.category_id = c.category_id
            LEFT JOIN PREFIX_stores AS s
                ON s.store_id = cts.store_id
            WHERE parent_id = 0
                AND cd.language_id = '" . (int) $this->config->get('language_id') . "'
            ORDER BY parent_id, sort_order";
        $query = $this->fetchAll($sql);

        $cats = $data = array();
        foreach ($query as $base) {
            // Step 2: Select parent categories
            $cats[$base['store_id']][$base['category_id']][0] = $base;
            $query2 = $this->fetchAll(str_replace('parent_id = 0', 'parent_id = ' . $base['category_id'], $sql));

            foreach ($query2 as $list) {
                $cats[$base['store_id']][$base['category_id']][$list['category_id']][$list['category_id']] = $list;
                if ($total) {
                    $query3 = $this->fetchAll(str_replace('parent_id = 0', 'parent_id = ' . $list['category_id'], $sql));
                    foreach ($query3 as $list2) {
                        $cats[$base['store_id']][$base['category_id']][$list['category_id']][$list2['category_id']] = $list2;
                    }
                }
            }
            //ksort($cats[$base['store_id']][$base['category_id']]);
        }
        //ksort($cats);

        foreach ($cats as $store_id => $store_cats) {
            $tmp = array();
            foreach ($store_cats as $cat_id => $categories) {
                foreach ($categories as $path_id => $levels) {
                    $tmp[] = $levels;
                }
            }
            $data[$store_id] = $tmp;
        }

        Cache::set('category', $data);

        return $data;
    }

    public function getCategoriesAsList($parentID = 0, $level = 0)
    {
        if ($parentID == 0) {
            $cache = Cache::find('category_as_list');
            if (is_array($cache) && count($cache)) {
                return $cache;
            }
        }

        $sql = "
            SELECT c.category_id, cts.store_id, cd.name, c.sort_order, parent_id, c.status, cd.meta_description, cd.description, cd.meta_keyword
            FROM PREFIX_category AS c
            LEFT JOIN PREFIX_category_to_store AS cts
                ON cts.category_id = c.category_id
            LEFT JOIN PREFIX_category_description AS cd
                ON cd.category_id = c.category_id
            WHERE parent_id = " . (int)$parentID . "
                AND cd.language_id = '" . (int) $this->config->get('language_id') . "'
            ORDER BY parent_id, sort_order";
        $query = $this->query($sql);
        $return = array();

        foreach ($query->fetchAll() as $row) {
            $row['level'] = $level;

            $return[] = $row;

            if (($subCategories = $this->getCategoriesAsList($row['category_id'], $level + 1)) !== false) {
                $return = array_merge($return, $subCategories);
            }
        }

        if ($parentID == 0) {
            Cache::set('category_as_list', $return);

            return $return;
        }

        if (empty($return)) {
            return false;
        }
        return $return;

    }

    public function getCategoryDescriptions($category_id)
    {
        $data = $this->fetchAll(
            "SELECT cd.*, (
                SELECT keyword
                FROM PREFIX_url_alias AS au
                WHERE au.query = :query
                    AND au.language_id = cd.language_id
                ) AS keyword
            FROM PREFIX_category_description AS cd
            WHERE category_id = :id",
            array(
                'query'     => 'category_id=' . $category_id,
                'id'        => $category_id
            )
        );

        $return = array();
        foreach ($data as $list) {
            $return[$list['language_id']] = $list;
        }

        return $return;
    }

    public function getCategoryStores($category_id)
    {
        $query = $this->query("SELECT store_id FROM PREFIX_category_to_store WHERE category_id = :id", array('id' => $category_id))->fetch();
        return $query['store_id'];
    }

    public function getTotalCategories()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_category")->fetch();
        return $query['total'];
    }
}
