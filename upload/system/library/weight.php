<?php
namespace Sumo;
class Weight
{
    private $weights = array();

    public function __construct($registry)
    {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');

        $weights = Cache::find('weights.' . $this->config->get('config_language_id'));
        if (!is_array($weights)) {
            $weights = Database::fetchAll("SELECT * FROM PREFIX_weight_class AS wc LEFT JOIN PREFIX_weight_class_description AS wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = :lang", array('lang' => $this->config->get('config_language_id')));
            Cache::set('weights.' . $this->config->get('config_language_id'), $weights);
        }

        foreach ($weights as $result) {
              $this->weights[$result['weight_class_id']] = array(
                'weight_class_id' => $result['weight_class_id'],
                'title'           => $result['title'],
                'unit'            => $result['unit'],
                'value'           => $result['value']
              );
        }
    }

    public function convert($value, $from, $to)
    {
        if ($from == $to) {
              return $value;
        }

        if (isset($this->weights[$from])) {
            $from = $this->weights[$from]['value'];
        } else {
            $from = 0;
        }

        if (isset($this->weights[$to])) {
            $to = $this->weights[$to]['value'];
        } else {
            $to = 0;
        }

        return $value * ($to / $from);
    }

    public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',')
    {
        if (isset($this->weights[$weight_class_id])) {
            return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$weight_class_id]['unit'];
        } else {
            return number_format($value, 2, $decimal_point, $thousand_point);
        }
    }

    public function getUnit($weight_class_id)
    {
        if (isset($this->weights[$weight_class_id])) {
            return $this->weights[$weight_class_id]['unit'];
        } else {
            return '';
        }
    }
}
