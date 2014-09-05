<?php
namespace Sumo;
class ModelCMSCMS extends Model
{
    public function getItems($store_id, $type = 'information')
    {
        $cache = 'cms_' . $type;
        $items = Cache::find($cache);
        if (is_array($items) && count($items)) {
            return $items;
        }

        if ($type == 'information') {
            $ids = $this->fetchAll(
                "SELECT i.information_id, i.parent_id
                FROM PREFIX_information AS i
                LEFT JOIN PREFIX_information_to_store AS its
                    ON its.information_id = i.information_id
                WHERE its.store_id = :id
                ORDER BY parent_id, sort_order ASC",
                array('id' => $store_id)
            );
            $return = array();
            foreach ($ids as $list) {
                if ($list['parent_id'] > 0) {
                    $return[$list['parent_id']]['children'][$list['information_id']] = array();
                }
                else {
                    $return[$list['information_id']] = array();
                }
            }

            foreach ($return as $id => $list) {
                $data = $this->getItem('information', $id);

                $kids = isset($list['children']) ? $list['children'] : array();

                $items[] = $data;
                if (count($kids)) {
                    foreach ($kids as $kid => $unused) {
                        $items[] = $this->getItem('information', $kid);
                    }
                }
            }
        }
        else {
            $items = $this->fetchAll(
                "SELECT b.*, bd.*
                FROM PREFIX_blog AS b
                LEFT JOIN PREFIX_blog_description AS bd
                    ON bd.blog_id = b.blog_id
                WHERE b.store_id        = :store
                    AND bd.language_id  = :lang",
                array(
                    'store'             => $store_id,
                    'lang'              => $this->config->get('language_id')
                )
            );
        }

        Cache::set($cache, $items);
        return $items;
    }

    public function getItem($type, $item_id)
    {
        $cache  = 'cms_' . $type . '.' . $item_id;
        $item   = Cache::find($cache);

        if (is_array($item) && count($item)) {
            return $item;
        }

        if ($type == 'information') {

            $item = $this->query(
                "SELECT i.*, id.*
                FROM PREFIX_information AS i
                LEFT JOIN PREFIX_information_description AS id
                    ON id.information_id = i.information_id
                LEFT JOIN PREFIX_information_to_store AS its
                    ON its.information_id = i.information_id
                WHERE id.language_id        = :lang
                    AND i.information_id    = :id",
                array(
                    'lang'                  => $this->config->get('language_id'),
                    'id'                    => $item_id
                )
            )->fetch();
        }
        else {
            $item = $this->query(
                "SELECT b.*, bd.*
                FROM PREFIX_blog AS b
                LEFT JOIN PREFIX_blog_description AS bd
                    ON bd.blog_id = b.blog_id
                WHERE b.blog_id         = :id
                    AND bd.language_id  = :lang",
                array(
                    'id'                => $item_id,
                    'lang'              => $this->config->get('language_id')
                )
            )->fetch();
        }

        Cache::set($cache, $item);
        return $item;
    }

    public function getEditorItem($type, $item_id)
    {
        $data = $this->query("SELECT * FROM PREFIX_" . $type . " WHERE " . $type . "_id = :id", array('id' => $item_id))->fetch();
        $lang = $this->fetchAll("SELECT * FROM PREFIX_" . $type . "_description WHERE " . $type . "_id = :id", array('id' => $item_id));
        foreach ($lang as $list) {
            $data['description'][$list['language_id']] = $list;
        }

        return $data;
    }

    public function saveInformation($store_id, $input, $information_id = 0)
    {
        $check = $this->query("SELECT MAX(sort_order) AS max_sort_order FROM PREFIX_information WHERE parent_id = :parent", array('parent' => isset($input['parent_id']) ? $input['parent_id'] : 0))->fetch();
        $sort = 0;
        if (!empty($check['max_sort_order'])) {
            $sort = $check['max_sort_order'] + 1;
        }
        if (!$information_id) {
            $this->query(
                "INSERT INTO PREFIX_information SET parent_id = :parent, status = 1, sort_order = :sort",
                array(
                    'parent'    => isset($input['parent_id']) ? $input['parent_id'] : 0,
                    'sort'      => $sort
                )
            );
            $information_id = $this->lastInsertId();
        }
        else {
            $this->query(
                "UPDATE PREFIX_information SET parent_id = :parent WHERE information_id = :id",
                array('parent' => isset($input['parent_id']) ? $input['parent_id'] : 0, 'id' => $information_id)
            );
        }

        $this->query("DELETE FROM PREFIX_information_description WHERE information_id = :id", array('id' => $information_id));
        $this->query("DELETE FROM PREFIX_information_to_store WHERE information_id = :id", array('id' => $information_id));
        $this->query("DELETE FROM PREFIX_url_alias WHERE query = :query", array('query' => 'information_id=' . $information_id));

        $this->query("INSERT INTO PREFIX_information_to_store SET information_id = :id, store_id = :store", array('id' => $information_id, 'store' => $store_id));
        foreach ($input['description'] as $lang => $list) {
            $this->query(
                "INSERT INTO PREFIX_information_description
                SET language_id     = :lang,
                    title           = :title,
                    description     = :desc,
                    meta_description= :meta,
                    meta_keywords   = :keys,
                    information_id  = :id",
                array(
                    'id'            => $information_id,
                    'lang'          => $lang,
                    'title'         => $list['title'],
                    'desc'          => $list['description'],
                    'meta'          => $list['meta_description'],
                    'keys'          => $list['meta_keywords']
                )
            );
            Formatter::generateSeoURL($list['title'], 'information_id', $information_id, $lang, $store_id);
        }
        Cache::removeAll();
        return $information_id;
    }

    public function saveBlog($store_id, $input, $blog_id = 0)
    {
        if (!$blog_id) {
            $this->query("INSERT INTO PREFIX_blog SET create_time = :date", array('date' => date('Y-m-d H:i:s')));
            $blog_id = $this->lastInsertId();
        }

        $this->query(
            "UPDATE PREFIX_blog
            SET store_id    = :store,
                author      = :author,
                update_time = :update,
                publish_date= :publish
            WHERE blog_id   = :id",
            array(
                'store'     => $store_id,
                'author'    => $input['author'],
                'update'    => date('Y-m-d H:i:s'),
                'publish'   => date('Y-m-d H:i:s', strtotime($input['publish_date'])),
                'id'        => $blog_id
            )
        );

        $this->query("DELETE FROM PREFIX_blog_description WHERE blog_id = :id", array('id' => $blog_id));
        $this->query("DELETE FROM PREFIX_url_alias WHERE query = :query", array('query' => 'blog_id=' . $blog_id));

        foreach ($input['description'] as $lang => $list) {
            $this->query(
                "INSERT INTO PREFIX_blog_description
                SET language_id     = :lang,
                    title           = :title,
                    intro_text      = :intro,
                    text            = :text,
                    meta_description= :meta,
                    meta_keyword    = :keys,
                    blog_id  = :id",
                array(
                    'id'            => $blog_id,
                    'lang'          => $lang,
                    'title'         => $list['title'],
                    'intro'         => $list['intro_text'],
                    'text'          => $list['text'],
                    'meta'          => $list['meta_description'],
                    'keys'          => $list['meta_keyword']
                )
            );
            Formatter::generateSeoURL($list['title'], 'blog_id', $blog_id, $lang, $store_id);
        }
        Cache::removeAll();
        return $blog_id;
    }

    public function setOrder($store_id, $information_id, $sort_order, $move)
    {
        $data = $this->getItem('information', $information_id);

        if ($move == 'up') {
            $update_self    = '-';
            $update_other   = '+';
            $sort_order     = $sort_order - 1;
        }
        else {
            $update_self    = '+';
            $update_other   = '-';
            $sort_order     = $sort_order + 1;
        }
        $this->query(
            "UPDATE PREFIX_information SET sort_order = sort_order " . $update_other . "1 WHERE parent_id = :parent AND sort_order = :order",
            array(
                'parent'    => $data['parent_id'],
                'store'     => $store_id,
                'order'     => $sort_order
            )
        );
        $this->query("UPDATE PREFIX_information SET sort_order = sort_order " . $update_self . "1 WHERE information_id = :id", array('id' => $information_id));

        $count = 1;
        foreach ($this->fetchAll("SELECT * FROM PREFIX_information WHERE parent_id = :parent ORDER BY sort_order ASC", array('parent' => $data['parent_id'])) as $list) {
            $this->query("UPDATE PREFIX_information SET sort_order = :order WHERE information_id = :id", array('order' => $count, 'id' => $list['information_id']));
            $count++;
        }

        Cache::removeAll();
    }

    public function setActive($type, $id)
    {
        $data = $this->getEditorItem($type, $id);
        $active = 1;
        if ($data['status']) {
            $active = 0;
        }
        $this->query("UPDATE PREFIX_" . $type . " SET status = :active WHERE " . $type . "_id = :id", array('active' => $active, 'id' => $id));
        Cache::removeAll();
    }

    public function getInformationParents($store_id)
    {
        $cache = 'cms_information.parents.' . $store_id;
        $items = Cache::find($cache);

        if (is_array($items) && count($items)) {
            return $items;
        }

        $items = array();

        $data = $this->fetchAll(
            "SELECT i.information_id, id.title
            FROM PREFIX_information AS i
            LEFT JOIN PREFIX_information_description AS id
                ON i.information_id = id.information_id
            LEFT JOIN PREFIX_information_to_store AS its
                ON i.information_id = its.information_id
            WHERE i.parent_id       = 0
                AND id.language_id  = :lang
                AND its.store_id    = :store",
            array(
                'lang'              => $this->config->get('language_id'),
                'store'             => $store_id
            )
        );

        foreach ($data as $list) {
            $items[$list['information_id']] = $list['title'];
        }

        Cache::set($cache, $items);
        return $items;
    }

    public function remove($type, $id)
    {
        if ($type == 'blog') {
            $this->query("DELETE FROM PREFIX_blog WHERE blog_id = :id", array('id' => $id));
        }
        else {
            $this->query("DELETE FROM PREFIX_information WHERE parent_id = :id", array('id' => $id));
            $this->query("DELETE FROM PREFIX_information WHERE information_id = :id", array('id' => $id));
        }
        Cache::removeAll();
    }
}
