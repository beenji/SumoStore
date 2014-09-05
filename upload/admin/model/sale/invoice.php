<?php
namespace Sumo;
class ModelSaleInvoice extends Model
{
    public function addInvoice($data)
    {
        $invoiceID = $this->insert('PREFIX_invoice', array(
            'invoice_date' => date('Y-m-d'),
            'status'       => 'CONCEPT'
        ));

        $data['status'] = 'CONCEPT';

        self::updateInvoice($invoiceID, $data);

        return $invoiceID;
    }

    public function addPartialPayment($invoiceID, $amount)
    {
        $invoiceData = $this->getInvoice($invoiceID);

        if (!empty($invoiceData)) {
            // Maybe, maybe not...
            if ($amount > ($invoiceData['total_amount'] - $invoiceData['total_amount_paid'])) {
                $amount = $invoiceData['total_amount'] - $invoiceData['total_amount_paid'];
            }

            $this->query('UPDATE PREFIX_invoice SET total_amount_paid = total_amount_paid + :amountPaid WHERE invoice_id = :invoiceID', array(
                'invoiceID'  => $invoiceID,
                'amountPaid' => $amount
            ));

            return ($invoiceData['total_amount'] - $invoiceData['amount_paid'] - $amount);
        }

        return false;
    }

    public function updateInvoice($invoiceID, $data)
    {
        // Delete invoice-lines and add 'em later again
        $this->query('DELETE FROM PREFIX_invoice_line WHERE invoice_id = :invoiceID', array(
            'invoiceID' => $invoiceID
        ));

        $this->query('DELETE FROM PREFIX_invoice_total WHERE invoice_id = :invoiceID', array(
            'invoiceID' => $invoiceID
        ));

        $totalTax = $total = 0;

        foreach ($data['amount'] as $line => $amount) {
            // Recalculate tax-amount based on the tax-percentage
            if ($data['tax_percentage'][$line] > 0) {
                $data['tax'][$line] = round($data['amount'][$line] * $data['quantity'][$line] * ($data['tax_percentage'][$line] / 100), 4);
                $totalTax += $data['tax'][$line];
            } else {
                $data['tax'][$line] = 0;
            }

            $total += $amount * $data['quantity'][$line];

            $this->query('INSERT INTO PREFIX_invoice_line (invoice_id, product_id, product, quantity, amount, tax_percentage, description) VALUES (
                :invoiceID,
                :productID,
                :product,
                :quantity,
                :amount,
                :taxPercentage,
                :description)', array(
                'invoiceID'     => $invoiceID,
                'productID'     => $data['product_id'][$line],
                'product'       => $data['product'][$line],
                'quantity'      => $data['quantity'][$line],
                'amount'        => $data['amount'][$line],
                'taxPercentage' => $data['tax_percentage'][$line],
                'description'   => $data['description'][$line]
            ));
        }

        foreach ($data['totals'] as $sortOrder => $productTotal) {
            $labelInject = isset($productTotal['label_inject']) ? $productTotal['label_inject'] : '';

            $this->query("INSERT INTO PREFIX_invoice_total
                SET invoice_id        = :id,
                    sort_order      = :sortOrder,
                    label           = :label,
                    label_inject    = :labelInject,
                    value           = :value,
                    value_hr        = :valueHR", 
                array(
                    'id'            => $invoiceID,
                    'sortOrder'     => $sortOrder,
                    'label'         => $productTotal['label'],
                    'labelInject'   => $labelInject,
                    'value'         => $productTotal['value'],
                    'valueHR'       => Formatter::currency($productTotal['value'])
                )
            );
        }

        // Totalamount is always the last line.
        $total = $productTotal['value'];

        $invoicePrefix = $this->config->get('invoice_prefix');

        // Customer country numeric? Change to plain text
        if (preg_match("/^\d+$/", $data['customer_country'])) {
            $countryData = $this->query("SELECT name FROM PREFIX_country WHERE country_id = :countryID", array('countryID' => $data['customer_country']))->fetch();
            $data['customer_country'] = !empty($countryData) ? $countryData['name'] : '-';
        }

        $this->query('UPDATE PREFIX_invoice SET
            invoice_no = :invoiceNO,
            customer_id = :customerID,
            customer_no = :customerNo,
            customer_name = :customerName,
            customer_address = :customerAddress,
            customer_postcode = :customerPostcode,
            customer_city = :customerCity,
            customer_country = :customerCountry,
            customer_email = :customerEmail,
            payment_amount = :paymentAmount,
            payment_tax_percentage = :paymentTax,
            shipping_amount = :shippingAmount,
            shipping_tax_percentage = :shippingTax,
            discount = :discount,
            total_amount = :amount,
            sent_date = :sentDate,
            notes = :notes,
            template = :template,
            term = :term,
            auto = :auto,
            reference = :reference
            WHERE invoice_id = :invoiceID', array(
                'invoiceNO'        => $invoicePrefix . str_pad($invoiceID, 5, 0, STR_PAD_LEFT),
                'customerNo'       => $customerNo,
                'customerID'       => $data['customer_id'],
                'customerName'     => $data['customer_name'],
                'customerAddress'  => $data['customer_address'],
                'customerPostcode' => $data['customer_postcode'],
                'customerCity'     => $data['customer_city'],
                'customerCountry'  => $data['customer_country'],
                'customerEmail'    => $data['customer_email'],
                'paymentAmount'    => $data['payment_amount'],
                'paymentTax'       => $data['payment_tax'],
                'shippingAmount'   => $data['shipping_amount'],
                'shippingTax'      => $data['shipping_tax'],
                'discount'         => json_encode($data['discount']),
                'amount'           => $total,
                'sentDate'         => Formatter::dateReverse($data['sent_date']),
                'notes'            => $data['notes'],
                'template'         => $data['template'],
                'term'             => $data['term'],
                'auto'             => $data['auto'],
                'reference'        => $data['reference'],
                'invoiceID'        => $invoiceID
        ));
    }

    public function deleteInvoice($invoiceID)
    {
        // We do not actually delete the invoice, we set its status to removed
        $this->query('UPDATE PREFIX_invoice SET `status` = :status WHERE invoice_id = :invoiceID', array(
            'status'    => 'REMOVED',
            'invoiceID' => $invoiceID
        ));
    }

    public function getInvoice($invoiceID)
    {
        $invoiceData = $this->query('SELECT * FROM PREFIX_invoice WHERE invoice_id = :invoiceID', array('invoiceID' => $invoiceID))->fetch();

        if ($invoiceData) {
            // Get records
            $invoiceLines = $this->query('SELECT * FROM PREFIX_invoice_line WHERE invoice_id = :invoiceID', array('invoiceID' => $invoiceID))->fetchAll();

            foreach ($invoiceLines as $invoiceLine) {
                foreach (array_keys($invoiceLine) as $invoiceLineKey) {
                    if (!preg_match('/^\d+$/', $invoiceLineKey) && (!isset($invoiceData[$invoiceLineKey]) || !is_string($invoiceData[$invoiceLineKey]))) {
                        $invoiceData[$invoiceLineKey][] = $invoiceLine[$invoiceLineKey];
                    }
                }
            }

            $invoiceData['discount']    = json_decode($invoiceData['discount'], true);
            $invoiceData['customer']    = $invoiceData['customer_name'];
            $invoiceData['customer_no'] = 'CID.' . str_pad($invoiceData['customer_id'], 5, 0, STR_PAD_LEFT);

            // Get totals
            $invoiceTotals = $this->query('SELECT * FROM PREFIX_invoice_total WHERE invoice_id = :invoiceID', array('invoiceID' => $invoiceID))->fetchAll();

            foreach ($invoiceTotals as $i => $total) {
                if (!empty($total['label_inject'])) {
                    $total['label'] = sprintf(Language::getVar($total['label'] . '_INJ'), $total['label_inject']);
                } else {
                    $total['label'] = Language::getVar($total['label']);
                }

                $invoiceData['totals'][] = $total;
            }
        } else {
            return false;
        }

        return $invoiceData;
    }

    public function getNextInvoiceNo()
    {
        $lastInvoice   = $this->query('SELECT MAX(invoice_id) AS last_invoice_id FROM PREFIX_invoice')->fetch();
        $invoicePrefix = $this->config->get('invoice_prefix');

        return $invoicePrefix . str_pad($lastInvoice['last_invoice_id'] + 1, 5, 0, STR_PAD_LEFT);
    }

    public function getInvoices($filter = array())
    {
        $sql_where = array();

        // Transform filter to SQL-filter
        foreach ($filter as $column => $value) {
            if ($column != 'start' && $column != 'limit') {
                if (is_array($value)) {
                    $value = array_map('addslashes', $value);
                    $sql_where[] = "`" . $column . "` IN ('" . implode("', '", $value) . "')";
                } else {
                    $sql_where[] = "`" . $column . "` LIKE '%" . addslashes($value) . "%'";
                }
            }
        }

        if (!empty($sql_where)) {
            $sql_where = ' WHERE ' . implode(' AND ', $sql_where);
        } else {
            $sql_where = '';
        }

        return $this->query('SELECT * FROM PREFIX_invoice' . $sql_where . ' ORDER BY invoice_id DESC')->fetchAll();
    }

    public function getTotalInvoices($filter = array())
    {
        $sql_where = array();

        // Transform filter to SQL-filter
        foreach ($filter as $column => $value) {
            if ($column != 'start' && $column != 'limit') {
                if (is_array($value)) {
                    $value = array_map('addslashes', $value);
                    $sql_where[] = "`" . $column . "` IN ('" . implode("', '", $value) . "')";
                } else {
                    $sql_where[] = "`" . $column . "` LIKE '%" . addslashes($value) . "%'";
                }
            }
        }

        if (!empty($sql_where)) {
            $sql_where = ' WHERE ' . implode(' AND ', $sql_where);
        } else {
            $sql_where = '';
        }

        $invoices = $this->query('SELECT COUNT(*) AS total_invoices FROM PREFIX_invoice' . $sql_where)->fetch();

        return $invoices['total_invoices'];
    }

    public function markSent($invoiceID)
    {
        $this->query('UPDATE PREFIX_invoice SET sent = sent + 1, sent_date = CURDATE() WHERE invoice_id = :invoiceID', array('invoiceID' => $invoiceID));
    }

    public function changeStatus($invoiceID, $status)
    {
        // Check if status is valid
        $validStatusList = array(
            'concept',
            'sent',
            'partially_paid',
            'paid',
            'credit',
            'expired'
        );

        if (!in_array(mb_strtolower($status), $validStatusList)) {
            return;
        }

        $this->query('UPDATE PREFIX_invoice SET status = :status WHERE invoice_id = :invoiceID', array(
            'invoiceID' => $invoiceID,
            'status'    => mb_strtoupper($status)
        ));
    }
}
