<?php
namespace Sumo;
class ModelToolOnline extends Model
{
    public function whosonline($ip, $customer_id, $url, $referer)
    {
        $this->query("DELETE FROM PREFIX_customer_online WHERE (UNIX_TIMESTAMP(`date_added`) + 3600) < UNIX_TIMESTAMP(NOW())");

        $this->query(
            "REPLACE INTO PREFIX_customer_online
            SET ip          = :ip,
            customer_id     = :customer,
            url             = :url,
            referer         = :referer,
            date_added      = :date",
            array(
                'ip'        => $ip,
                'customer'  => $customer_id ? $customer_id : 0,
                'url'       => $url,
                'referer'   => $referer ? $referer : '',
                'date'      => date('Y-m-d H:i:s')
            )
        );
    }
}
