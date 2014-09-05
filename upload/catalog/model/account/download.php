<?php
namespace Sumo;
class ModelAccountDownload extends Model
{
    public function getDownload($orderDownloadID)
    {
        return $this->query("SELECT od.*, o.*
            FROM PREFIX_orders_download od, PREFIX_orders o, PREFIX_orders_data odd 
            WHERE od.order_id = o.order_id
                AND o.order_id = odd.order_id
                AND odd.customer LIKE :customerID
                AND o.order_status > 0 
                AND o.order_status = :completeStatusID 
                AND od.order_download_id = :orderDownloadID
                AND od.remaining > 0", array(
                'customerID'        => '%"customer_id":"' . (int)$this->customer->getId() . '"%',
                'completeStatusID'  => (int)$this->config->get('complete_status_id'),
                'orderDownloadID'   => $orderDownloadID))->fetch();
    }

    public function getDownloads($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        return $this->query("SELECT o.order_id, o.order_date, od.order_download_id, od.name, od.filename, od.remaining 
            FROM PREFIX_orders_download od, PREFIX_orders o, PREFIX_orders_data odd
            WHERE od.order_id = o.order_id
                AND o.order_id = odd.order_id 
                AND odd.customer LIKE :customerID 
                AND o.order_status > 0 
                AND o.order_status = :completeStatusID 
                AND od.remaining > 0 
            ORDER BY o.order_date DESC 
            LIMIT :start, :limit", array(
                'customerID'        => '%"customer_id":"' . (int)$this->customer->getId() . '"%',
                'completeStatusID'  => (int)$this->config->get('complete_status_id'),
                'start'             => $start,
                'limit'             => $limit))->fetchAll();
    }

    public function updateRemaining($orderDownloadID)
    {
        $this->query("UPDATE PREFIX_orders_download SET 
            remaining = (remaining - 1) 
            WHERE order_download_id = :orderDownloadID", array('orderDownloadID' => (int)$orderDownloadID));
    }

    public function getTotalDownloads()
    {
        $query = $this->query("SELECT COUNT(*) AS total 
            FROM PREFIX_orders_download od, PREFIX_orders o, PREFIX_orders_data odd
            WHERE od.order_id = o.order_id
                AND o.order_id = odd.order_id
                AND odd.customer LIKE :customerID 
                AND o.order_status > 0 
                AND o.order_status = :completeStatusID
                AND od.remaining > 0", array(
                    'customerID'        => '%"customer_id":"' . (int)$this->customer->getId() . '"%',
                    'completeStatusID'  => (int)$this->config->get('complete_status_id')))->fetch();

        return $query['total'];
    }
}
