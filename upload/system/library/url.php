<?php
class Url
{
    private $url;
    private $ssl;
    private $rewrite = array();

    public function __construct($url, $ssl = '')
    {
        $this->url = $url;
        $this->ssl = $ssl;
    }

    public function addRewrite($rewrite)
    {
        $this->rewrite[] = $rewrite;
    }

    public function link($route = '', $args = '', $connection = 'NONSSL')
    {
        $url = $route;

        if ($args) {
            $url .= str_replace('&', '&amp;', '?' . ltrim($args, '&'));
        }

        foreach ($this->rewrite as $rewrite) {
            $url = $rewrite->rewrite($url);
        }

        if (is_array($url)) {
            $url = '';
        }

        if ($connection == 'NONSSL') {
            return rtrim($this->url, '/') . '/' . ltrim($url, '/');
        }
        return rtrim($this->ssl, '/') . '/' . ltrim($url, '/');
    }

    public function strToURI($str)
    {
        $return = trim($str);

        // Strip spaces
        $return = str_replace(' ', '-', $return);
        $return = str_replace('.', '', $return);
        $return = str_replace(',', '', $return);
        $return = str_replace(array('&amp;', '&'), 'en', $return);

        $chars = array(
            "\xC5\xA0"  => "\x53",
            "\xC5\xBD"  => "\x5A",
            "\xC5\xA1"  => "\x73",
            "\xC5\xBE"  => "\x7A",
            "\xC5\xB8"  => "\x59",
            "\xC3\x80"  => "\x41",
            "\xC3\x81"  => "\x41",
            "\xC3\x82"  => "\x41",
            "\xC3\x83"  => "\x41",
            "\xC3\x84"  => "\x41",
            "\xC3\x85"  => "\x41",
            "\xC3\x87"  => "\x43",
            "\xC3\x88"  => "\x45",
            "\xC3\x89"  => "\x45",
            "\xC3\x8A"  => "\x45",
            "\xC3\x8B"  => "\x45",
            "\xC3\x8C"  => "\x49",
            "\xC3\x8D"  => "\x49",
            "\xC3\x8E"  => "\x49",
            "\xC3\x8F"  => "\x49",
            "\xC3\x91"  => "\x4E",
            "\xC3\x92"  => "\x4F",
            "\xC3\x93"  => "\x4F",
            "\xC3\x94"  => "\x4F",
            "\xC3\x95"  => "\x4F",
            "\xC3\x96"  => "\x4F",
            "\xC3\x98"  => "\x4F",
            "\xC3\x99"  => "\x55",
            "\xC3\x9A"  => "\x55",
            "\xC3\x9B"  => "\x55",
            "\xC3\x9C"  => "\x55",
            "\xC3\x9D"  => "\x59",
            "\xC3\xA0"  => "\x61",
            "\xC3\xA1"  => "\x61",
            "\xC3\xA2"  => "\x61",
            "\xC3\xA3"  => "\x61",
            "\xC3\xA4"  => "\x61",
            "\xC3\xA5"  => "\x61",
            "\xC3\xA7"  => "\x63",
            "\xC3\xA8"  => "\x65",
            "\xC3\xA9"  => "\x65",
            "\xC3\xAA"  => "\x65",
            "\xC3\xAB"  => "\x65",
            "\xC3\xAC"  => "\x69",
            "\xC3\xAD"  => "\x69",
            "\xC3\xAE"  => "\x69",
            "\xC3\xAF"  => "\x69",
            "\xC3\xB1"  => "\x6E",
            "\xC3\xB2"  => "\x6F",
            "\xC3\xB3"  => "\x6F",
            "\xC3\xB4"  => "\x6F",
            "\xC3\xB5"  => "\x6F",
            "\xC3\xB6"  => "\x6F",
            "\xC3\xB8"  => "\x6F",
            "\xC3\xB9"  => "\x75",
            "\xC3\xBA"  => "\x75",
            "\xC3\xBB"  => "\x75",
            "\xC3\xBC"  => "\x75",
            "\xC3\xBD"  => "\x79",
            "\xC3\xBF"  => "\x79",

            // Two-letter replacements
            "\xC3\xBE"  => "\x54"."\x48",
            "\xC3\xE9"  => "\x74"."\x68",
            "\xC3\x90"  => "\x44"."\x48",
            "\xC3\xB0"  => "\x64"."\x68",
            "\xC3\x9F"  => "\x73"."\x73",
            "\xC3\x92"  => "\x4F"."\x45",
            "\xC3\x93"  => "\x6F"."\x65",
            "\xC3\x86"  => "\x41"."\x45",
            "\xC3\xA6"  => "\x61"."\x65",
            "\xC2\xB5"  => "\x75"
        );

        $return = strtr($return, $chars);

        $return = preg_replace('/[^A-Za-z0-9_\+-]/', '-', $return);

        // Replace double-dashes with single dashes
        $return = preg_replace("/\-{2,}/", '-', $return);
        $return = trim($return, '-');
        $return = mb_strtolower($return);

        return $return;
    }
}
