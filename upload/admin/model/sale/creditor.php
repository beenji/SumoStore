<?php 
namespace Sumo;
class ModelSaleCreditor extends Model
{
    public function addCreditor($creditorData)
    {
        $this->query('INSERT INTO PREFIX_creditor (creditor_id) VALUES (NULL)');
        $creditorID = $this->lastInsertId();

        $this->editCreditor($creditorID, $creditorData);
    }

    public function editCreditor($creditorID, $creditorData)
    {
        $this->query('UPDATE PREFIX_creditor SET 
            companyname = :companyName,
            contact_gender = :contactGender,
            contact_name = :contactName,
            contact_surname = :contactSurname,
            address = :address,
            city = :city,
            postcode = :postcode,
            country_id = :countryID,
            contact_email = :contactEmail,
            contact_phone = :contactPhone,
            contact_mobile = :contactMobile,
            contact_fax = :contactFax,
            customer_number = :customerNumber,
            bank_iban = :bankIBAN,
            bank_account = :bankAccount,
            bank_name = :bankName,
            bank_city = :bankCity,
            bank_bic = :bankBIC,
            term = :term,
            notes = :notes WHERE creditor_id = :creditorID LIMIT 1', array(
                'companyName'       => $creditorData['companyname'],
                'contactGender'     => $creditorData['contact_gender'],
                'contactName'       => $creditorData['contact_name'],
                'contactSurname'    => $creditorData['contact_surname'],
                'address'           => $creditorData['address'],
                'city'              => $creditorData['city'],
                'postcode'          => $creditorData['postcode'],
                'countryID'         => $creditorData['country_id'],
                'contactEmail'      => $creditorData['contact_email'],
                'contactPhone'      => $creditorData['contact_phone'],
                'contactMobile'     => $creditorData['contact_mobile'],
                'contactFax'        => $creditorData['contact_fax'],
                'customerNumber'    => $creditorData['customer_number'],
                'bankIBAN'          => $creditorData['bank_iban'],
                'bankAccount'       => $creditorData['bank_account'],
                'bankName'          => $creditorData['bank_name'],
                'bankCity'          => $creditorData['bank_city'],
                'bankBIC'           => $creditorData['bank_bic'],
                'term'              => $creditorData['term'],
                'notes'             => $creditorData['notes'],
                'creditorID'        => $creditorID
            ));
    }

    public function deleteCreditor($creditorID)
    {
        $this->query('DELETE FROM PREFIX_creditor WHERE creditor_id = :creditorID LIMIT 1', array(
            'creditorID'    => $creditorID
        ));
    }

    public function getCreditor($creditorID)
    {
        return $this->query('SELECT * 
            FROM PREFIX_creditor 
            WHERE creditor_id = :creditorID', array(
                'creditorID'        => $creditorID 
            ))->fetch();
    }

    public function getTotalCreditors()
    {
        $query = $this->query('SELECT COUNT(*) AS total FROM PREFIX_creditor')->fetch();

        return $query['total'];
    }

    public function getCreditors($data = array())
    {
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sqlLimit = " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        } else {
            $sqlLimit = '';
        }

        return $this->query('SELECT * FROM PREFIX_creditor ORDER BY creditor_id ASC ' . $sqlLimit)->fetchAll();
    }
}