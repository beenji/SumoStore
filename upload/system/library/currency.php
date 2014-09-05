<?php
namespace Sumo;
class Currency
{
    private $id;
    private $currencies = array();

    public function __construct($registry)
    {
        $this->config = $registry->get('config');
        $this->language = $registry->get('language');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');

        $currencies = Cache::find('currencies');
        if (!is_array($currencies)) {
            $currencies = Database::fetchAll("SELECT * FROM PREFIX_currency");
            Cache::set('currencies', $currencies);
        }
        foreach ($currencies as $result) {
              $this->currencies[$result['currency_id']] = $result;
        }

        if (isset($this->request->get['currency']) && (array_key_exists($this->request->get['currency'], $this->currencies))) {
            $this->set($this->request->get['currency']);
        }
        elseif ((isset($this->session->data['currency'])) && (array_key_exists($this->session->data['currency'], $this->currencies))) {
             $this->set($this->session->data['currency']);
        }
        elseif ((isset($this->request->cookie['currency'])) && (array_key_exists($this->request->cookie['currency'], $this->currencies))) {
            $this->set($this->request->cookie['currency']);
        }
        else {
            $this->set($this->config->get('currency_id'));
        }
    }

    public function set($currency)
    {
        $this->id = $currency;

        if (!isset($this->session->data['currency']) || ($this->session->data['currency'] != $currency)) {
              $this->session->data['currency'] = $currency;
        }

        if (!isset($this->request->cookie['currency']) || ($this->request->cookie['currency'] != $currency)) {
              setcookie('currency', $currency, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
        }
    }

    public function format($number, $currency = '', $value = '', $format = true)
    {
        if ($currency && $this->has($currency)) {
            $symbol_left   = $this->currencies[$currency]['symbol_left'];
            $symbol_right  = $this->currencies[$currency]['symbol_right'];
            $decimal_place = $this->currencies[$currency]['decimal_place'];
        }
        else {
            $symbol_left   = $this->currencies[$this->id]['symbol_left'];
            $symbol_right  = $this->currencies[$this->id]['symbol_right'];
            $decimal_place = $this->currencies[$this->id]['decimal_place'];

            $currency = $this->id;
        }

        if ($value) {
            $value = $value;
        }
        else {
            $value = $this->currencies[$currency]['value'];
        }

        if ($value) {
            $value = (float)$number * $value;
        }
        else {
            $value = $number;
        }

        $string = '';

        if (($symbol_left) && ($format)) {
              $string .= $symbol_left;
        }

        if ($format) {
            $decimal_point = Language::getVar('SUMO_CURRENCY_DECIMAL_POINT');
        }
        else {
            $decimal_point = ',';
        }

        if ($format) {
            $thousand_point = Language::getVar('SUMO_CURRENCY_THOUSAND_POINT');
        }
        else {
            $thousand_point = '.';
        }

        $string .= number_format(round($value, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);

        if (($symbol_right) && ($format)) {
            $string .= $symbol_right;
        }

        return $string;
    }

    public function convert($value, $from, $to)
    {
        if (isset($this->currencies[$from])) {
            $from = $this->currencies[$from]['value'];
        }
        else {
            $from = 0;
        }

        if (isset($this->currencies[$to])) {
            $to = $this->currencies[$to]['value'];
        }
        else {
            $to = 0;
        }

        return $value * ($to / $from);
    }

    public function getSymbolLeft($currency = '')
    {
        if (!$currency) {
            return $this->currencies[$this->id]['symbol_left'];
        }
        elseif ($currency && isset($this->currencies[$currency])) {
            return $this->currencies[$currency]['symbol_left'];
        }
        return '';
    }

    public function getSymbolRight($currency = '') {
        if (!$currency) {
            return $this->currencies[$this->id]['symbol_right'];
        }
        elseif ($currency && isset($this->currencies[$currency])) {
            return $this->currencies[$currency]['symbol_right'];
        }
        return '';

    }

    public function getDecimalPlace($currency = '')
    {
        if (!$currency) {
            return $this->currencies[$this->id]['decimal_place'];
        }
        elseif ($currency && isset($this->currencies[$currency])) {
            return $this->currencies[$currency]['decimal_place'];
        }
        return 0;
    }

    public function getid()
    {
        return $this->id;
    }

    public function getValue($currency = '')
    {
        if (!$currency) {
            return $this->currencies[$this->id]['value'];
        }
        elseif ($currency && isset($this->currencies[$currency])) {
            return $this->currencies[$currency]['value'];
        }
        return 0;
    }

    public function has($currency)
    {
        return isset($this->currencies[$currency]);
    }
}
