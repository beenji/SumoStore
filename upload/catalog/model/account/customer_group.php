<?php
namespace Sumo;
class ModelAccountCustomerGroup extends Model
{
    public function getCustomerGroup($customer_group_id)
    {
        return $this->query(
            "SELECT DISTINCT *
            FROM PREFIX_customer_group cg
            LEFT JOIN PREFIX_customer_group_description cgd
                ON (cg.customer_group_id = cgd.customer_group_id)
            WHERE cg.customer_group_id = :id
            AND cgd.language_id = :lang",
            array(
                'id'    => $customer_group_id,
                'lang'  => $this->config->get('language_id')
            )
        )->fetch();
    }

    public function getCustomerGroups()
    {
        if ($this->config->get('customer_group_display')) {
            return $this->fetchAll(
                "SELECT *
                FROM PREFIX_customer_group cg
                LEFT JOIN PREFIX_customer_group_description cgd
                    ON cg.customer_group_id = cgd.customer_group_id
                WHERE cgd.language_id = :id
                    AND cg.customer_group_id IN (" . implode(',', $this->config->get('customer_group_display')) . ")
                ORDER BY cg.sort_order ASC, cgd.name ASC",
                array('id'  => $this->config->get('language_id'))
            );
        }
    }
}
