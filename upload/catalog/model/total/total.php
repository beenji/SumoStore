<?php
namespace Sumo;
class ModelTotalTotal extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes)
    {
        $this->language->load('total/total');

        $total_data[] = array(
            'code'       => 'total',
            'title'      => Language::getVar('SUMO_CHECKOUT_TOTAL'),
            'text'       => $this->currency->format(max(0, $total)),
            'value'      => max(0, $total),
            'sort_order' => $this->config->get('total_sort_order')
        );
    }
}
