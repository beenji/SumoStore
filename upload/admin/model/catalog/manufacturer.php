<?php
namespace Sumo;
class ModelCatalogManufacturer extends Model
{
    public function addManufacturer($data)
    {
        $this->query("INSERT INTO PREFIX_manufacturer SET name = :name", array('name' => $data['name']));
        $manufacturer_id = $this->lastInsertId();
        return $this->editManufacturer($manufacturer_id, $data);
    }

    public function editManufacturer($manufacturer_id, $data)
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $this->query(
            "UPDATE PREFIX_manufacturer
            SET name                = :name,
                sort_order          = :order
            WHERE manufacturer_id   = :id",
            array(
                'name'              => $data['name'],
                'order'             => $data['sort_order'],
                'id'                => $manufacturer_id
            )
        );

        if (isset($data['image'])) {
            $this->query(
                "UPDATE PREFIX_manufacturer
                SET image = :image
                WHERE manufacturer_id = :id",
                array(
                    'image' => $data['image'],
                    'id'    => $manufacturer_id
                )
            );
        }

        $this->query("DELETE FROM PREFIX_manufacturer_to_store WHERE manufacturer_id = " . (int)$manufacturer_id);
        $this->query("DELETE FROM PREFIX_url_alias WHERE query = :query", array('query' => "manufacturer_id=" . (int)$manufacturer_id));
        $uri = Formatter::strToURI($data['name']) . '-m' . (int)$manufacturer_id;
        if (isset($data['manufacturer_store'])) {
            foreach ($data['manufacturer_store'] as $store_id) {
                $this->query(
                    "INSERT INTO PREFIX_manufacturer_to_store
                    SET manufacturer_id = :mid,
                        store_id        = :sid",
                    array(
                        'mid'           => $manufacturer_id,
                        'sid'           => $store_id
                    )
                );
                //Formatter::generateSeoURL($data['name'], 'manufacturer_id', $manufacturer_id, 0, $store_id);
                $this->query("INSERT INTO PREFIX_url_alias SET query = :query, keyword = :keyword, language_id = 0, store_id = :sid", array('query' => 'manufacturer_id=' . (int)$manufacturer_id, 'keyword' => $uri, 'sid' => $store_id));
            }
        }
        else {
            //Formatter::generateSeoURL($data['name'], 'manufacturer_id', $manufacturer_id, 0);
            $this->query("INSERT INTO PREFIX_url_alias SET query = :query, keyword = :keyword, language_id = 0, store_id = 0", array('query' => 'manufacturer_id=' . (int)$manufacturer_id, 'keyword' => $uri));
        }
        Cache::removeAll();
    }

    public function deleteManufacturer($manufacturer_id)
    {
        $this->query("DELETE FROM PREFIX_manufacturer WHERE manufacturer_id = " . (int)$manufacturer_id);
        $this->query("DELETE FROM PREFIX_manufacturer_to_store WHERE manufacturer_id = " . (int)$manufacturer_id);
        $this->query("DELETE FROM PREFIX_url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

        Cache::removeAll();
    }

    public function getManufacturer($manufacturer_id)
    {
        return $this->query(
            "SELECT DISTINCT *, (SELECT keyword FROM PREFIX_url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "' LIMIT 1) AS keyword
            FROM PREFIX_manufacturer
            WHERE manufacturer_id = " . (int)$manufacturer_id)->fetch();
    }

    public function getManufacturers($data = array())
    {
        $sql = "SELECT * FROM PREFIX_manufacturer";
        $values = array();

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE :name";
            $values['name'] = $data['filter_name'];
        }

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
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
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $this->fetchAll($sql, $values);
    }

    public function getManufacturerStores($manufacturer_id)
    {
        $manufacturer_store_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_manufacturer_to_store WHERE manufacturer_id = " . (int)$manufacturer_id);

        foreach ($query as $result) {
            $manufacturer_store_data[] = $result['store_id'];
        }

        return $manufacturer_store_data;
    }

    public function getTotalManufacturersByImageId($image_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_manufacturer WHERE image_id = " . (int)$image_id)->fetch();

        return $query['total'];
    }

    public function getTotalManufacturers()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_manufacturer")->fetch();

        return $query['total'];
    }

    public function checkForImageUpload($manufacturer_id, $name)
    {
        $json = array();
        if (isset($this->request->files['image']) && $this->request->files['image']['tmp_name']) {
            $filename = basename(html_entity_decode($this->request->files['image']['name'], ENT_QUOTES, 'UTF-8'));

            if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
                $json['error'] = $this->language->get('error_filename');
            }

            $directory = rtrim(DIR_IMAGE, '/') . '/m';

            if (!is_dir($directory) && !mkdir($directory)) {
                $json['error'] = true;
            }

            if ($this->request->files['image']['size'] > 300000) {
                $json['error'] = true;
            }

            $allowed = array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
                'image/gif',
                'application/x-shockwave-flash'
            );

            if (!in_array($this->request->files['image']['type'], $allowed)) {
                $json['error'] = true;
            }

            $allowed = array(
                '.jpg',
                '.jpeg',
                '.gif',
                '.png',
                '.flv'
            );

            if (!in_array(strtolower(strrchr($filename, '.')), $allowed)) {
                $json['error'] = true;
            }

            if ($this->request->files['image']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = 'error_upload_' . $this->request->files['image']['error'];
            }
        } else {
            $json['error'] = true;
        }

        if (!isset($json['error'])) {
            $tmp = explode('.', $filename);
            $tmp[0] = $name . '-' . $manufacturer_id;
            $filename = implode('.', $tmp);
            if (@move_uploaded_file($this->request->files['image']['tmp_name'], $directory . '/' . $filename)) {
                return rtrim(str_replace(DIR_IMAGE, '', $directory), '/') . '/' . $filename;
            }
        }
    }
}
