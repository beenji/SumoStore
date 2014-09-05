<?php
namespace Sumo;
class ModelCatalogInformation extends Model
{
    public function addInformation($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "information SET status = '" . (int)$data['status'] . "'");

        $information_id = $this->db->getLastId();
        return $this->editInformation($information_id, $data);
    }

    public function copyPage($information_id)
    {
        $data = $this->getInformation($information_id);
        $data['status'] = 0;

        $data['information_store'] = $this->getInformationStores($information_id);
        $data['information_description'] = $this->getInformationDescriptions($information_id);
        return $this->addInformation($data);
    }

    public function editInformation($information_id, $data)
    {
        $this->db->query("
            UPDATE " . DB_PREFIX . "information
            SET bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "',
                status = '" . (int)$data['status'] . "',
                parent_id = '" . (int)$data['parent_id'] . "'
            WHERE information_id = '" . (int)$information_id . "'"
        );

        $this->db->query("
            DELETE
            FROM " . DB_PREFIX . "information_description
            WHERE information_id = '" . (int)$information_id . "'"
        );

        foreach ($data['information_description'] as $language_id => $value) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "information_description
                SET information_id = '" . (int)$information_id . "',
                    language_id = '" . (int)$language_id . "',
                    title = '" . $this->db->escape($value['title']) . "',
                    description = '" . $this->db->escape($value['description']) . "',
                    meta_description = '" . $this->db->escape($value['meta_description']) . "',
                    meta_keywords = '" . $this->db->escape($value['meta_keywords']) . "'"
            );
            if (isset($value['keyword'])) {
                $this->db->query("
                    DELETE
                    FROM " . DB_PREFIX . "url_alias
                    WHERE query = 'information_id=" . (int)$information_id. "'
                        AND language_id = " . (int)$language_id
                );
                $this->db->query("
                    INSERT INTO " . DB_PREFIX . "url_alias
                    SET query = 'information_id=" . (int)$information_id . "',
                        keyword = '" . $this->db->escape($value['keyword']) . "',
                        language_id = " . (int)$language_id . ",
                        store_id = " . (int)$data['information_store']
                    );

            }
        }

        $this->db->query("
            DELETE
            FROM " . DB_PREFIX . "information_to_store
            WHERE information_id = '" . (int)$information_id . "'");

        if (isset($data['information_store'])) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "information_to_store
                SET information_id = '" . (int)$information_id . "',
                    store_id = '" . (int)$data['information_store'] . "'");
        }

        $this->db->query("
            DELETE
            FROM " . DB_PREFIX . "information_to_layout
            WHERE information_id = '" . (int)$information_id . "'");

        if (isset($data['information_layout'])) {
            foreach ($data['information_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("
                        INSERT INTO " . DB_PREFIX . "information_to_layout
                        SET information_id = '" . (int)$information_id . "',
                            store_id = '" . (int)$store_id . "',
                            layout_id = '" . (int)$layout['layout_id'] . "'"
                    );
                }
            }
        }

        $this->cache->delete('information');
    }

    public function deleteInformation($information_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'");

        $this->cache->delete('information');
    }

    public function deleteBlog($blog_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "blog WHERE blog_id = '" . (int)$blog_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "blog_description WHERE blog_id = '" . (int)$blog_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'blog_id=" . (int)$blog_id . "'");

        $this->cache->delete('blog');
    }

    public function getInformation($information_id)
    {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "information
            WHERE information_id = '" . (int)$information_id . "'
        ");

        return $query->row;
    }

    public function getInformations($data = array())
    {
        if ($data) {
            $sql = "
            SELECT *
            FROM " . DB_PREFIX . "information i
            LEFT JOIN " . DB_PREFIX . "information_description id
                ON (i.information_id = id.information_id)
            LEFT JOIN " . DB_PREFIX . "information_to_store its
                        ON (i.information_id = its.information_id)
            WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sort_data = array(
                'id.title',
                'i.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY id.title";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        }
        else {
            /** @todo Possible caching improvement **/
            return Database::fetchAll(
                "SELECT *
                FROM PREFIX_information AS i
                LEFT JOIN PREFIX_information_description AS id
                    ON (i.information_id = id.information_id)
                LEFT JOIN PREFIX_information_to_store AS its
                    ON (i.information_id = its.information_id)
                WHERE id.language_id = :lang
                ORDER BY id.information_id DESC",
                array(
                    'lang'  => $this->config->get('language_id')
                )
            );
        }
    }

    public function getInformationDescriptions($information_id)
    {
        $information_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");

        foreach ($query->rows as $result) {
            $information_description_data[$result['language_id']] = $result;
        }

        return $information_description_data;
    }

    public function getInformationStores($information_id)
    {
        $query = $this->db->query("
            SELECT store_id
            FROM " . DB_PREFIX . "information_to_store
            WHERE information_id = '" . (int)$information_id . "'"
        );

        $result = $query->row;

        return $result['store_id'];
    }

    public function getInformationLayouts($information_id)
    {
        $information_layout_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");

        foreach ($query->rows as $result) {
            $information_layout_data[$result['store_id']] = $result['layout_id'];
        }

        return $information_layout_data;
    }

    public function getTotalInformations()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information");

        return $query->row['total'];
    }

    public function getTotalInformationsByLayoutId($layout_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

        return $query->row['total'];
    }


    public function getBlogs()
    {

        $blog_data = $this->cache->get('blog.' . (int)$this->config->get('config_language_id'));

        if (!$blog_data) {
            $query = $this->db->query("
                SELECT *
                FROM " . DB_PREFIX . "blog b
                LEFT JOIN " . DB_PREFIX . "blog_description bd
                    ON (b.blog_id = bd.blog_id)
                WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                ORDER BY bd.blog_id DESC"
            );

            $blog_data = $query->rows;

            $this->cache->set('blog', $blog_data);
        }
        return $blog_data;

    }

    public function getBlog($blog_id)
    {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "blog
            WHERE blog_id = '" . (int)$blog_id . "'
        ");

        return $query->row;
    }


    public function getBlogDescriptions($blog_id)
    {
        $blog_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_description WHERE blog_id = '" . (int)$blog_id . "'");

        foreach ($query->rows as $result) {
            $blog_description_data[$result['language_id']] = $result;
        }

        return $blog_description_data;
    }

    public function addBlog($data)
    {
        $this->db->query("
            INSERT INTO " . DB_PREFIX . "blog
            SET status = '" . (int)$data['status'] . "',
                create_time = NOW()"
        );

        $blog_id = $this->db->getLastId();
        return $this->editBlog($blog_id, $data);
    }

    public function editBlog($blog_id, $data)
    {
        if (empty($blog_id) || !is_numeric($blog_id)) {
            return $this->addBlog($data);
        }
        $this->db->query("
            UPDATE " . DB_PREFIX . "blog
            SET bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "',
                status = '" . (int)$data['status'] . "',
                store_id = '" . (int)$data['store_id'] . "',
                update_time = NOW(),
                author = '" . $this->db->escape($data['author']) . "',
                publish_date = '" . $this->db->escape($data['publish_date']) . "'
            WHERE blog_id = '" . (int)$blog_id . "'"
        );

        $this->db->query("
            DELETE
            FROM " . DB_PREFIX . "blog_description
            WHERE blog_id = '" . (int)$blog_id . "'"
        );

        foreach ($data['blog_description'] as $language_id => $value) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "blog_description
                SET blog_id = '" . (int)$blog_id . "',
                    language_id = '" . (int)$language_id . "',
                    title = '" . $this->db->escape($value['title']) . "',
                    intro_text = '" . $this->db->escape($value['intro_text']) . "',
                    text = '" . $this->db->escape($value['text']) . "',
                    meta_description = '" . $this->db->escape($value['meta_description']) . "',
                    meta_keyword = '" . $this->db->escape($value['meta_keywords']) . "'"
            );
            if (isset($value['keyword'])) {
                $this->db->query("
                    DELETE
                    FROM " . DB_PREFIX . "url_alias
                    WHERE query = 'blog_id=" . (int)$blog_id. "'
                        AND language_id = " . (int)$language_id
                );
                $this->db->query("
                    INSERT INTO " . DB_PREFIX . "url_alias
                    SET query = 'blog_id=" . (int)$blog_id . "',
                        keyword = '" . $this->db->escape($value['keyword']) . "',
                        language_id = " . (int)$language_id . ",
                        store_id = " . (int)$data['store_id']
                    );
            }
        }
        $this->cache->delete('blog');
    }
}
