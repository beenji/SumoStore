<?php
namespace Sumo;
class ModelSaleCustomerGroup extends Model
{
    public function addCustomerGroup($data)
    {
        $description = $data['customer_group_description'];
        unset($data['customer_group_description']);
        $this->query("INSERT INTO PREFIX_customer_group SET
            approval = :approval,
            company_id_display = 1,
            company_id_required = :company_id_required,
            tax_id_display = 1,
            tax_id_required = :tax_id_required", $data);

        $customerGroupID = $this->lastInsertId();

        foreach ($description as $languageID => $value) {
            $this->query("INSERT INTO PREFIX_customer_group_description SET
                customer_group_id = :customerGroupID,
                language_id = :languageID,
                name = :name,
                description = :description", array(
                    'customerGroupID'   => $customerGroupID,
                    'languageID'        => $languageID,
                    'name'              => $value['name'],
                    'description'       => $value['description']));
        }

        return $customerGroupID;
    }

    public function editCustomerGroup($customerGroupID, $data)
    {
        $data['customerGroupID'] = $customerGroupID;
        if (!isset($data['company_id_display'])) {
            $data['company_id_display'] = $data['company_id_required'];
        }
        if (!isset($data['tax_id_display'])) {
            $data['tax_id_display'] = $data['tax_id_required'];
        }
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }
        $this->query("UPDATE PREFIX_customer_group SET
            approval                = :approval,
            company_id_display      = :company_id_display,
            company_id_required     = :company_id_required,
            tax_id_display          = :tax_id_display,
            tax_id_required         = :tax_id_required,
            sort_order              = :sort_order
            WHERE customer_group_id = :customerGroupID", $data);

        $this->query("DELETE FROM PREFIX_customer_group_description WHERE customer_group_id = :customerGroupID", array('customerGroupID' => (int)$customerGroupID));

        foreach ($data['customer_group_description'] as $languageID => $value) {
            $this->query("INSERT INTO PREFIX_customer_group_description SET
                customer_group_id = :customerGroupID,
                language_id = :languageID,
                name = :name,
                description = :description", array(
                    'customerGroupID'   => $customerGroupID,
                    'languageID'        => $languageID,
                    'name'              => $value['name'],
                    'description'       => $value['description']));
        }

        return true;
    }

    public function deleteCustomerGroup($customerGroupID)
    {
        $this->query("DELETE FROM PREFIX_customer_group WHERE customer_group_id = :customerGroupID", array('customerGroupID' => (int)$customerGroupID));
        $this->query("DELETE FROM PREFIX_customer_group_description WHERE customer_group_id = :customerGroupID", array('customerGroupID' => (int)$customerGroupID));
        $this->query("DELETE FROM PREFIX_product_discount WHERE customer_group_id = :customerGroupID", array('customerGroupID' => (int)$customerGroupID));
        $this->query("DELETE FROM PREFIX_product_special WHERE customer_group_id = :customerGroupID", array('customerGroupID' => (int)$customerGroupID));
    }

    public function getCustomerGroup($customer_group_id)
    {
        return $this->query(
            "SELECT DISTINCT *
            FROM PREFIX_customer_group AS cg
            LEFT JOIN PREFIX_customer_group_description AS cgd
                ON (cg.customer_group_id = cgd.customer_group_id)
            WHERE cg.customer_group_id = " . (int)$customer_group_id . "
                AND cgd.language_id = " . (int)$this->config->get('language_id'))->fetch();
    }

    public function getCustomerGroups($data = array())
    {

        $sql = 'SELECT * FROM PREFIX_customer_group';

        $sortData = array(
            //'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sortData)) {
            $sql .= " ORDER BY " . $data['sort'];
        }
        else {
            $sql .= " ORDER BY sort_order";
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
        $return = array();
        foreach ($this->fetchAll($sql) as $list) {
            $description = $this->query(
                "SELECT *
                FROM PREFIX_customer_group_description
                WHERE language_id = :lang
                    AND customer_group_id = :group",
                array(
                    'lang' => $this->config->get('language_id'),
                    'group' => $list['customer_group_id']
                )
            )->fetch();
            if (is_array($description)) {
                $list = array_merge($description, $list);
            }
            else {
                $list['name'] = Language::getVar('SUMO_NOUN_UNKNOWN');
            }
            $return[$list['customer_group_id']] = $list;
        }

        return $return;
    }

    public function getCustomerGroupDescriptions($customerGroupID)
    {
        $customerGroupData = array();

        $query = $this->query("SELECT *
            FROM PREFIX_customer_group_description
            WHERE customer_group_id = :customerGroupID", array(
                'customerGroupID' => (int)$customerGroupID))->fetchAll();

        foreach ($query as $result) {
            $customerGroupData[$result['language_id']] = array(
                'name'        => $result['name'],
                'description' => $result['description']
            );
        }

        return $customerGroupData;
    }

    public function getTotalCustomerGroups()
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer_group")->fetch();
        return $data['total'];
    }
}
