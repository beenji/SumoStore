<?php
class ModelTotalMultisafepayPayafterFee extends Model 
{
    public function getTotal(&$total_data, &$total, &$taxes) 
    {
        $this->session->data['multisafepaypayafterfee']['fee'] = false;
        $this->session->data['multisafepaypayafterfee']['feetax'] = false;
        $this->load->language('payment/multisafepay');
        
        
        if (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == 'multisafepay_payafter' && ($fee = $this->config->get('multisafepay_payafter_paymentfee'))) {
            if ($fee < 0) {
                $fee = round($total * -$fee / 100.0, 2);
            }
    
            
            $total += $fee;
        
            $total_data[] = array(
                'code'       => 'multisafepaypayafterfee',
                'title'      => $this->language->get('entry_paymentfee'),
                'text'       => $this->currency->format($fee),
                'value'      => $fee,
                'sort_order' => $this->config->get('multisafepaypayafterfee_sort_order')
            );
            
            
            
            $feetax = 0;
            $rate = 0;
            if (($tax = $this->config->get('multisafepay_payafter_tax'))) {
                if (method_exists($this->tax, 'getRate')) {
                    $rate = $this->tax->getRate($tax);
                    if (!isset($taxes[$tax])) {
                        $taxes[$tax] = $feetax = $fee * $rate / 100;
                    }
                    else {
                        $taxes[$tax] += $feetax = $fee * $rate / 100;
                    }
                }
                else {
                    $tax_rates = $this->tax->getRates($fee, $tax);
                    foreach ($tax_rates as $tax_rate) {
                        if (!isset($taxes[$tax_rate['tax_rate_id']])) {
                            $taxes[$tax_rate['tax_rate_id']] = $feetax = $tax_rate['amount'];
                        }
                        else {
                            $taxes[$tax_rate['tax_rate_id']] += $feetax = $tax_rate['amount'];
                        }
                    }
                }
            }
            $this->session->data['multisafepaypayafterfee']['fee'] = $fee;
            $this->session->data['multisafepaypayafterfee']['feetax'] = $feetax;

        }
    }
}
