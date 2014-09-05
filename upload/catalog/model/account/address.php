<?php
namespace Sumo;
class ModelAccountAddress extends Model
{
    public function addAddress($data)
    {
        $this->query("
            INSERT INTO PREFIX_address
            SET customer_id = :id",
            array(
                'id' => $this->customer->getId()
            )
        );

        $address_id = $this->lastInsertId();
        return $this->editAddress($address_id, $data);
    }

    public function editAddress($address_id, $data)
    {
        $data['address_id']         = $address_id;
        $data['comp_id']            = $data['company_id'];
        $data['customer_id']        = $this->customer->getId();

        if (!empty($data['default'])) {
            $this->query("UPDATE PREFIX_customer SET address_id = :aid WHERE customer_id = :cid", array('aid' => $data['address_id'], 'cid' => $data['customer_id']));
        }
        unset($data['company_id']);
        unset($data['default']);

        $this->query(
            "UPDATE PREFIX_address
            SET firstname           = :firstname,
                middlename          = :middlename,
                lastname            = :lastname,
                company             = :company,
                company_id          = :comp_id,
                tax_id              = :tax_id,
                address_1           = :address_1,
                number              = :number,
                addon               = :addon,
                postcode            = :postcode,
                address_2           = :address_2,
                city                = :city,
                zone_id             = :zone_id,
                country_id          = :country_id
            WHERE address_id        = :address_id
                AND customer_id     = :customer_id",
            $data
        );
    }

    public function deleteAddress($address_id)
    {
        $this->query("DELETE FROM PREFIX_address WHERE address_id = :aid AND customer_id = :cid", array('aid' => $address_id, 'cid' => $this->customer->getId()));
    }

    public function getAddress($address_id, $customer_id = null)
    {
        $sql = "SELECT * FROM PREFIX_address WHERE address_id = :id";
        $values = array('id' => $address_id);
        if ($customer_id) {
            $sql .= "  AND customer_id = :cid";
            $values['cid'] = $customer_id;
        }
        $data = self::query($sql, $values)->fetch();
        if (!count($data)) {
            return false;
        }
        $country = self::query(
            "SELECT *
            FROM PREFIX_country
            WHERE country_id = :id",
            array(
                'id'    => $data['country_id']
            )
        )->fetch();

        if (isset($country['name'])) {
            $data['country'] = $country['name'];
            $data = array_merge($data, $country);
        }

        $zone = self::query(
            "SELECT *
            FROM PREFIX_zone
            WHERE zone_id = :id",
            array(
                'id'    => $data['zone_id']
            )
        )->fetch();

        if (isset($zone['name'])) {
            $data['zone'] = $zone['name'];
            $data = array_merge($data, $zone);
        }

        $geo = $this->query("SELECT geo_zone_id FROM PREFIX_zone_to_geo_zone WHERE country_id = :id ORDER BY geo_zone_id DESC", array('id' => $data['country_id']))->fetch();
        if (isset($geo['geo_zone_id'])) {
            $data['geo_zone_id'] = $geo['geo_zone_id'];
        }

        return $data;
    }

    public function getAddresses()
    {
        $address_data = array();

        $data = self::fetchAll(
            "SELECT address_id
            FROM PREFIX_address
            WHERE customer_id = :id",
            array(
                'id' => $this->customer->getId()
            )
        );

        foreach ($data as $list) {
            $address_data[$list['address_id']] = self::getAddress($list['address_id']);
        }

        return $address_data;
    }

    public function getTotalAddresses()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_address WHERE customer_id = :id", array('id' => $this->customer->getId()))->fetch();
        return $query['total'];
    }
}
