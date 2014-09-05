<?php
namespace Sumo;
class ModelCatalogDownload extends Model
{
    public function addDownload($data)
    {
        $this->query("INSERT INTO PREFIX_download SET date_added = :date", array('date' => date('Y-m-d H:i:s')));
        $download_id = $this->lastInsertId();
        return $this->editDownload($download_id, $data);
    }

    public function editDownload($download_id, $data)
    {
        if (!empty($data['update'])) {
            $download_info = $this->getDownload($download_id);

            if ($download_info) {
                $this->query(
                    "UPDATE PREFIX_order_download
                    SET filename    = :filename,
                        mask        = :mask,
                        remaining   = :remaining
                    WHERE filename  = :old",
                    array(
                        'filename'  => $data['filename'],
                        'mask'      => $data['mask'],
                        'remaining' => $data['remaining'],
                        'old'       => $download_info['filename']
                    )
                );
            }
        }

        $this->query(
            "UPDATE PREFIX_download
            SET filename    = :filename,
                mask        = :mask,
                remaining   = :remaining
            WHERE download_id = :id",
            array(
                'filename'  => $data['filename'],
                'mask'      => $data['mask'],
                'remaining' => $data['remaining'],
                'id'        => $download_id
            )
        );

        $this->query("DELETE FROM PREFIX_download_description WHERE download_id = " . (int)$download_id);

        foreach ($data['download_description'] as $language_id => $value) {
            $this->query(
                "INSERT INTO PREFIX_download_description
                SET download_id     = :id,
                    name            = :name,
                    language_id     = :language",
                array(
                    'id'            => $download_id,
                    'name'          => $value['name'],
                    'language'      => $language_id,
                )
            );
        }
    }

    public function deleteDownload($download_id)
    {
        $this->query("DELETE FROM PREFIX_download WHERE download_id = " . (int)$download_id);
        $this->query("DELETE FROM PREFIX_download_description WHERE download_id = " . (int)$download_id);
    }

    public function getDownload($download_id)
    {
        $query = $this->query("SELECT DISTINCT * FROM PREFIX_download d LEFT JOIN PREFIX_download_description dd ON (d.download_id = dd.download_id) WHERE d.download_id = " . (int)$download_id . " AND dd.language_id = " . (int)$this->config->get('language_id'));
        return $query->fetch();
    }

    public function getDownloads($data = array())
    {
        $sql = "SELECT * FROM PREFIX_download d LEFT JOIN PREFIX_download_description dd ON (d.download_id = dd.download_id) WHERE dd.language_id = " . (int)$this->config->get('language_id');
        $values = array();
        if (!empty($data['filter_name'])) {
            $sql .= " AND dd.name LIKE :filter";
            $values['filter'] = $data['filter_name'] . '%';
        }

        $sort_data = array(
            'dd.name',
            'd.remaining'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        }
        else {
            $sql .= " ORDER BY dd.name";
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

    public function getDownloadDescriptions($download_id)
    {
        $download_description_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_download_description WHERE download_id = " . (int)$download_id);

        foreach ($query as $result) {
            $download_description_data[$result['language_id']] = array('name' => $result['name']);
        }

        return $download_description_data;
    }

    public function getTotalDownloads()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_download")->fetch();
        return $query['total'];
    }
}
