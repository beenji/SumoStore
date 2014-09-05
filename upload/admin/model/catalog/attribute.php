<?php
namespace Sumo;
class ModelCatalogAttribute extends Model
{
    public function addAttribute($data)
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $this->query("INSERT INTO PREFIX_attribute SET attribute_group_id = " . (int)$data['attribute_group_id'] . ", sort_order = " . (int)$data['sort_order']);

        $attribute_id = $this->lastInsertId();

        foreach ($data['attribute_description'] as $language_id => $value) {
            $this->query(
                "INSERT INTO PREFIX_attribute_description
                SET attribute_id    = :id,
                    language_id     = :lid,
                    name            = :name",
                array(
                    'id'            => $attribute_id,
                    'lid'           => $language_id,
                    'name'          => $value['name']
                )
            );
        }

        return $attribute_id;
    }

    public function addAttributeGroup($data)
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $this->query("INSERT INTO PREFIX_attribute_group SET sort_order = " . (int)$data['sort_order']);

        $attribute_group_id = $this->lastInsertId();

        foreach ($data['attribute_group_description'] as $language_id => $value) {
            $this->query(
                "INSERT INTO PREFIX_attribute_group_description
                SET attribute_group_id  = :id,
                    language_id         = :lid,
                    name                = :name",
                array(
                    'id'                => $attribute_group_id,
                    'lid'               => $language_id,
                    'name'              => $value['name']
                )
            );
        }

        // Add attributes
        $attributes = array();

        foreach ($data['attribute'] as $attribute_data) {
            $attribute_data['attribute_group_id'] = $attribute_group_id;
            $attribute_id = $this->addAttribute($attribute_data);

            $attributes[] = array(
                'attribute_id'  => $attribute_id,
                'name'          => $attribute_data['attribute_description'][$this->config->get('language_id')]['name']
            );
        }

        return array(
            'attribute_group_id' => $attribute_group_id,
            'group'              => $data['attribute_group_description'][$this->config->get('language_id')]['name'],
            'attributes'         => $attributes 
        );
    }

    public function editAttribute($attribute_id, $data)
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $this->query("UPDATE PREFIX_attribute SET attribute_group_id = " . (int)$data['attribute_group_id'] . ", sort_order = " . (int)$data['sort_order'] . " WHERE attribute_id = " . (int)$attribute_id);

        $this->query("DELETE FROM PREFIX_attribute_description WHERE attribute_id = " . (int)$attribute_id);

        foreach ($data['attribute_description'] as $language_id => $value) {
            $this->query(
                "INSERT INTO PREFIX_attribute_description
                SET attribute_id    = :id,
                    language_id     = :lid,
                    name            = :name",
                array(
                    'id'            => $attribute_id,
                    'lid'           => $language_id,
                    'name'          => $value['name']
                )
            );
        }
    }

    public function editAttributeGroup($attribute_group_id, $data)
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $this->query("UPDATE PREFIX_attribute_group SET sort_order = " . (int)$data['sort_order'] . " WHERE attribute_group_id = " . (int)$attribute_group_id);

        $this->query("DELETE FROM PREFIX_attribute_group_description WHERE attribute_group_id = " . (int)$attribute_group_id);

        foreach ($data['attribute_group_description'] as $language_id => $value) {
            $this->query(
                "INSERT INTO PREFIX_attribute_group_description
                SET attribute_group_id  = :id,
                    language_id         = :lid,
                    name                = :name",
                array(
                    'id'                => $attribute_group_id,
                    'lid'               => $language_id,
                    'name'              => $value['name']
                )
            );
        }

        $group_attributes = array();

        // Edit attributes
        foreach ($data['attribute'] as $attribute_data) {
            $attribute_data['attribute_group_id'] = $attribute_group_id;
            if (isset($attribute_data['attribute_id']) && !empty($attribute_data['attribute_id'])) {
                $group_attributes[] = $attribute_data['attribute_id'];
                $this->editAttribute($attribute_data['attribute_id'], $attribute_data);
            }
            else {
                $group_attributes[] = $this->addAttribute($attribute_data);
            }
        }

        // Delete floating attributes
        $this->query("DELETE FROM PREFIX_product_attribute WHERE attribute_id IN (
            SELECT attribute_id
            FROM PREFIX_attribute
            WHERE attribute_group_id = " . (int)$attribute_group_id . "
                AND attribute_id NOT IN (" . implode(',', $group_attributes) . "))");
        $this->query("DELETE FROM PREFIX_attribute_description WHERE attribute_id IN (
            SELECT attribute_id
            FROM PREFIX_attribute
            WHERE attribute_group_id = " . (int)$attribute_group_id . "
                AND attribute_id NOT IN (" . implode(',', $group_attributes) . "))");
        $this->query("DELETE FROM PREFIX_attribute
            WHERE attribute_group_id = " . (int)$attribute_group_id . "
                AND attribute_id NOT IN (" . implode(',', $group_attributes) . ")");
    }

    public function deleteAttribute($attribute_id)
    {
        $this->query("DELETE FROM PREFIX_attribute WHERE attribute_id = " . (int)$attribute_id);
        $this->query("DELETE FROM PREFIX_attribute_description WHERE attribute_id = " . (int)$attribute_id);
    }

    public function deleteAttributeGroup($attribute_group_id)
    {
        $this->query("DELETE FROM PREFIX_attribute_description
            WHERE attribute_id IN (
                SELECT attribute_id
                FROM PREFIX_attribute
                WHERE attribute_group_id = " . (int)$attribute_group_id . ")");

        $this->query("DELETE FROM PREFIX_attribute WHERE attribute_group_id = " . (int)$attribute_group_id);
        $this->query("DELETE FROM PREFIX_attribute_group WHERE attribute_group_id = " . (int)$attribute_group_id);
        $this->query("DELETE FROM PREFIX_attribute_group_description WHERE attribute_group_id = " . (int)$attribute_group_id);
    }

    public function getAttribute($attribute_id)
    {
        return $this->query(
            "SELECT *
            FROM PREFIX_attribute AS a
            LEFT JOIN PREFIX_attribute_description AS ad
                ON (a.attribute_id = ad.attribute_id)
            WHERE a.attribute_id = :id
                AND ad.language_id = :lid",
            array(
                'id'    => $attribute_id,
                'lid'   => $this->config->get('language_id')
            )
        );
    }

    public function getAttributes($data = array())
    {
        $values = array();
        $sql = "SELECT *, (SELECT agd.name FROM PREFIX_attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = " . (int)$this->config->get('language_id') . ") AS attribute_group FROM PREFIX_attribute a LEFT JOIN PREFIX_attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = " . (int)$this->config->get('language_id');

        if (!empty($data['filter_name'])) {
            $sql .= " AND ad.name LIKE :filter";
            $values['filter'] = $data['filter_name'] . '%';
        }

        if (!empty($data['filter_attribute_group_id'])) {
            $sql .= " AND a.attribute_group_id = :group_id";
            $values['group_id'] = $data['filter_attribute_group_id'];
        }

        $sort_data = array(
            'ad.name',
            'attribute_group',
            'a.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        }
        else {
            $sql .= " ORDER BY attribute_group, ad.name";
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

    public function getAttributeDescriptions($attribute_id)
    {
        $attribute_data = array();

        $query = $this->fetchAll("SELECT * FROM PREFIX_attribute_description WHERE attribute_id = " . (int)$attribute_id);

        foreach ($query as $result) {
            $attribute_data[$result['language_id']] = array('attribute_id' => $attribute_id, 'name' => $result['name']);
        }

        return $attribute_data;
    }

    public function getAttributeGroups($data = array())
    {
        $sql = "SELECT *
        FROM PREFIX_attribute_group ag
        LEFT JOIN PREFIX_attribute_group_description agd
            ON (ag.attribute_group_id = agd.attribute_group_id)
        WHERE agd.language_id = " . (int)$this->config->get('language_id');

        $sort_data = array(
            'agd.name',
            'ag.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        }
        else {
            $sql .= " ORDER BY agd.name";
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

        return $this->fetchAll($sql);
    }

    public function getAttributeGroupDescriptions($attribute_group_id)
    {
        $attribute_group_data = array();

        $query = $this->fetchAll("SELECT language_id, name FROM PREFIX_attribute_group_description WHERE attribute_group_id = :id", array('id' => $attribute_group_id));

        foreach ($query as $result) {
            $attribute_group_data[$result['language_id']] = $result;
        }

        return $attribute_group_data;
    }

    public function getTotalAttributeGroups()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_attribute_group")->fetch();

        return $query['total'];
    }

    public function getTotalAttributesByAttributeGroupId($attribute_group_id)
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_attribute WHERE attribute_group_id = :id", array('id' => $attribute_group_id))->fetch();

        return $query['total'];
    }
}
