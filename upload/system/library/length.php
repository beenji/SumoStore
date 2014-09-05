<?php
namespace Sumo;
class Length
{
    private $lengths = array();

    public function __construct($registry)
    {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');

        $lengths = Cache::find('lengths.' . $this->config->get('config_language_id'));
        if (!is_array($lengths)) {
            $lengths = Database::fetchAll("SELECT * FROM PREFIX_length_class AS lc LEFT JOIN PREFIX_length_class_description AS lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lcd.language_id = :lang", array('lang' => $this->config->get('config_language_id')));
            Cache::set('lengths.' . $this->config->get('config_language_id'), $lengths);
        }

        foreach ($lengths as $result) {
              $this->lengths[$result['length_class_id']] = array(
                'length_class_id' => $result['length_class_id'],
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

        if (isset($this->lengths[$from])) {
            $from = $this->lengths[$from]['value'];
        } else {
            $from = 0;
        }

        if (isset($this->lengths[$to])) {
            $to = $this->lengths[$to]['value'];
        } else {
            $to = 0;
        }

        return $value * ($to / $from);
    }

    public function format($value, $length_class_id, $decimal_point = '.', $thousand_point = ',')
    {
        if (isset($this->lengths[$length_class_id])) {
            return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
        } else {
            return number_format($value, 2, $decimal_point, $thousand_point);
        }
    }

    public function getUnit($length_class_id)
    {
        if (isset($this->lengths[$length_class_id])) {
            return $this->lengths[$length_class_id]['unit'];
        } else {
            return '';
        }
    }
}
