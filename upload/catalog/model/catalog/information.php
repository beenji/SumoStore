<?php
namespace Sumo;
class ModelCatalogInformation extends Model
{
    public function getInformation($information_id)
    {
        return self::query(
            "SELECT DISTINCT *
            FROM PREFIX_information i
            LEFT JOIN PREFIX_information_description id
                ON (i.information_id = id.information_id)
            LEFT JOIN PREFIX_information_to_store i2s
                ON (i.information_id = i2s.information_id)
            WHERE i.information_id = :id
                AND id.language_id = " . (int)$this->config->get('language_id') . "
                AND i2s.store_id = " . (int)$this->config->get('store_id') . "
                AND i.status = 1",
            array('id' => $information_id))->fetch();
    }

    public function getInformations()
    {
        $data = Cache::find('informations');
        if (count($data)) {
            return $data;
        }
        $data = self::fetchAll("SELECT * FROM PREFIX_information i LEFT JOIN PREFIX_information_description id ON (i.information_id = id.information_id) LEFT JOIN PREFIX_information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('store_id') . "' AND i.status = '1' ORDER BY i.sort_order, i.parent_id, LCASE(id.title) ASC");
        Cache::set('informations', $data);
        return $data;
    }

    public function getBlog($blog_id)
    {
        $query = $this->query(
            "SELECT DISTINCT *
            FROM PREFIX_blog b
            LEFT JOIN PREFIX_blog_description bd ON (b.blog_id = bd.blog_id)
            WHERE b.blog_id = '" . (int)$blog_id . "'
                AND bd.language_id = '" . (int)$this->config->get('language_id') . "'
                AND b.store_id = '" . (int)$this->config->get('store_id') . "'
                AND b.status = '1'
                AND b.publish_date < NOW()")->fetch();

        return $query;
    }

    public function getBlogs()
    {
        $data = Cache::find('blogs');
        if (count($data)) {
            return $data;
        }

        $query = $this->fetchAll("SELECT * FROM PREFIX_blog i LEFT JOIN PREFIX_blog_description id ON (i.blog_id = id.blog_id)  WHERE id.language_id = '" . (int)$this->config->get('language_id') . "' AND i.store_id = '" . (int)$this->config->get('store_id') . "' AND i.status = '1' AND i.publish_date < NOW() ORDER BY i.publish_date DESC");
        Cache::set('blogs', $query);
        return $query->rows;
    }
}
