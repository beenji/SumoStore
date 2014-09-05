<?php
namespace Mollie;
use Sumo;
use App;

class ModelPayments extends App\Model
{
    public function create($order_id, $transaction_id, $data)
    {
        $this->query("INSERT INTO PREFIX_app_mollie_payments SET order_id = :id, transaction_id = :tid", array('id' => $order_id, 'tid' => $transaction_id));
        $payment_id = $this->lastInsertId();

        $this->update($payment_id, $data);
        return $this->lastInsertId();
    }

    public function getPaymentId($transaction_id)
    {
        $result = $this->query("SELECT payment_id FROM PREFIX_app_mollie_payments WHERE transaction_id = :id", array('id' => $transaction_id))->fetch();
        return $result['payment_id'];
    }

    public function getOrderId($transaction_id)
    {
        $result = $this->query("SELECT order_id FROM PREFIX_app_mollie_payments WHERE transaction_id = :id", array('id' => $transaction_id))->fetch();
        return $result['order_id'];
    }

    public function getLastTransactionIdByOrder($order_id)
    {
        $result = $this->query("SELECT transaction_id FROM PREFIX_app_mollie_payments WHERE order_id = :id ORDER BY payment_id DESC LIMIT 1", array('id' => $order_id))->fetch();
        return $result['transaction_id'];
    }

    public function update($payment_id, $data)
    {
        //$this->query("DELETE FROM PREFIX_app_mollie_payments_info WHERE payment_id = :id", array('id' => $payment_id));
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $check = $this->query("SELECT info_name FROM PREFIX_app_mollie_payments_info WHERE info_name = :key", array('key' => $key))->fetch();
            if (is_array($check) && isset($check['info_name'])) {
                $this->query("UPDATE PREFIX_app_mollie_payments_info SET info_value = :value WHERE info_name = :key AND payment_id = :pid", array('pid' => $payment_id, 'key' => $key, 'value' => $value));
            }
            else {
                $this->query("INSERT INTO PREFIX_app_mollie_payments_info SET payment_id = :pid, info_name = :key, info_value = :value", array('pid' => $payment_id, 'key' => $key, 'value' => $value));
            }
        }
    }
}
