<?php
namespace Sumo;
class Formatter extends Singleton
{
    public static $instance, $config, $currency_info;

    public static function setup($config)
    {
        self::getInstance();
        self::$config = $config;
    }

    public static function time($str)
    {
        // Check if $str is actually a timestamp
        if (preg_match('/^\d+$/', $str)) {
            // Seems legit ^^
            return strftime(self::$config->get('date_time'), $str);
        }

        // Lazy developer at work. Convert string to timestamp.
        return strftime(self::$config->get('date_time'), strtotime($str));
    }

    // Alias for dateShort
    public static function date($str)
    {
        return self::dateShort($str);
    }

    public static function dateShort($str)
    {
        // Check if $str is actually a timestamp
        if (preg_match('/^\d+$/', $str)) {
            // Seems legit ^^
            return strftime(self::$config->get('date_format_short'), $str);
        }

        // Lazy developer at work. Convert string to timestamp.
        return strftime(self::$config->get('date_format_short'), strtotime($str));
    }

    public static function dateLong($str)
    {
        // Check if $str is actually a timestamp
        if (preg_match('/^\d+$/', $str)) {
            // Seems legit ^^
            return strftime(self::$config->get('date_format_long'), $str);
        }

        // Lazy developer at work. Convert string to timestamp.
        return strftime(self::$config->get('date_format_long'), strtotime($str));
    }

    public static function dateTime($str, $short = true)
    {
        if ($short) {
            return self::dateShort($str) . ' ' . self::time($str);
        }
        return self::dateLong($str) . ' ' . self::time($str);
    }

    public static function dateReverse($str, $format = 'Y-m-d')
    {
        return self::dateShortReverse($str, $format);
    }

    public static function dateShortReverse($str, $format = 'Y-m-d')
    {
        $date = date_create_from_format(self::strfToDateFormat(self::$config->get('date_format_short')), $str);

        if ($date) {
            return date_format($date, $format);
        }
        return '';
    }

    public static function dateLongReverse($str, $format = 'Y-m-d H:i:s')
    {
        $date = date_create_from_format(self::strfToDateFormat(self::$config->get('date_format_long')), $str);

        if ($date) {
            return date_format($date, $format);
        }
        return '';
    }

    public static function dateFormatToJS($format = false)
    {
        $format = !$format ? self::$config->get('date_format_short') : $format;

        $translations = array(
            '%d'    => 'dd',
            '%m'    => 'mm',
            '%Y'    => 'yyyy',
            '%x'    => 'dd-mm-yy'
        );

        return strtr($format, $translations);
    }

    public static function currency($str, $showSymbol = true)
    {

        if (!self::$currency_info) {
            $currency = Cache::find('currency');
            if (!is_array($currency) || !count($currency)) {
                $currency = Database::query("SELECT *
                    FROM PREFIX_currency
                    WHERE currency_id = :id", array(
                        'id' => self::$config->get('currency_id')))->fetch();
                Cache::set('currency', $currency);
            }

            self::$currency_info = array(
                'symbol_left'    => $currency['symbol_left'],
                'symbol_right'   => $currency['symbol_right'],
                'decimal_place'  => $currency['decimal_place'],
                'decimal_point'  => ',',
                'thousand_point' => '.'
            );
        }

        $return = '';

        if ($showSymbol) {
            $return  = self::$currency_info['symbol_left'];
        }

        $return .= number_format($str, self::$currency_info['decimal_place'], self::$currency_info['decimal_point'], self::$currency_info['thousand_point']);

        if ($showSymbol) {
            $return .= self::$currency_info['symbol_right'];
        }

        return $return;
    }

    public static function bytes($amount)
    {
        $units  = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $power  = $amount > 0 ? floor(log($amount, 1024)) : 0;
        return number_format($amount / pow(1024, $power), 2, '.', ',') . $units[$power];
    }

    protected static function strfToDateFormat($format)
    {
        // Transform from locales?
        if ($format == '%x') {
            $format = nl_langinfo(D_FMT);
        }
        elseif ($format == '%X') {
            $format = nl_langinfo(T_FMT);
        }
        elseif ($format == '%c') {
            $format = nl_langinfo(D_T_FMT);
        }

        $chars = array(
            // Day - no strf eq : S
            '%d' => 'd', '%a' => 'D', '%e' => 'j', '%A' => 'l', '%u' => 'N', '%w' => 'w', '%j' => 'z',
            // Week - no date eq : %U, %W
            '%V' => 'W',
            // Month - no strf eq : n, t
            '%B' => 'F', '%m' => 'm', '%b' => 'M',
            // Year - no strf eq : L; no date eq : %C, %g
            '%G' => 'o', '%Y' => 'Y', '%y' => 'y',
            // Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
            '%P' => 'a', '%p' => 'A', '%l' => 'g', '%I' => 'h', '%H' => 'H', '%M' => 'i', '%S' => 's',
            // Timezone - no strf eq : e, I, P, Z
            '%z' => 'O', '%Z' => 'T',
            // Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
            '%s' => 'U'
        );

        return strtr($format, $chars);
    }

    public static function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
            } // [+]
        }

        return $headers;
    }

    public static function generateSeoURL($input, $type, $typeID = 0, $languageID = 0, $storeID = 0, $categoryID = 0, $urlAliasID = 0, $increment = 0)
    {
        if ($increment > 0) {
            $input .= '-' . (int)$increment;
        }

        $keyword = self::strToURI($input);
        if (empty($keyword)) {
            if (!empty($typeID)) {
                $keyword = $typeID;
            }
            else {
                $keyword = time();
            }
        }

        // Check if already exists
        if (!empty($urlAliasID)) {
            $query = Database::query('SELECT COUNT(*) AS existingAlias
                FROM PREFIX_url_alias
                WHERE keyword = :keyword
                    AND language_id = :languageID
                    AND url_alias_id <> :urlAliasID', array(
                        'keyword'    => $keyword,
                        'languageID' => $languageID,
                        'urlAliasID' => $urlAliasID))->fetch();

            if ($query['existingAlias'] > 0) {
                // Try again, with $type + $typeID suffix
                if ($increment == 0) {
                    $keyword .= '-' . mb_substr($type, 0, 1) . $typeID;

                    $queryWithID = Database::query('SELECT COUNT(*) AS existingAlias
                        FROM PREFIX_url_alias
                        WHERE keyword = :keyword
                            AND language_id = :languageID
                            AND url_alias_id <> :urlAliasID', array(
                                'keyword'    => $keyword,
                                'languageID' => $languageID,
                                'urlAliasID' => $urlAliasID))->fetch();
                }

                if ($increment > 0 || $queryWithID['existingAlias'] > 0) {
                    // No luck, go the other route
                    return self::generateSeoURL($input, $type, $typeID, $languageID, $storeID, $categoryID, $urlAliasID, $increment + 1);
                }
            }
        }

        $query = Database::query('SELECT COUNT(*) AS existingAlias
            FROM PREFIX_url_alias
            WHERE keyword = :keyword
                AND language_id = :languageID', array(
                    'keyword'    => $keyword,
                    'languageID' => $languageID))->fetch();

        if ($query['existingAlias'] > 0) {
            // Try again, with $type + $typeID suffix
            if ($increment == 0) {
                $keyword .= '-' . mb_substr($type, 0, 1) . $typeID;

                $queryWithID = Database::query('SELECT COUNT(*) AS existingAlias
                    FROM PREFIX_url_alias
                    WHERE keyword = :keyword
                        AND language_id = :languageID', array(
                            'keyword'    => $keyword,
                            'languageID' => $languageID))->fetch();
            }

            if ($increment > 0 || $queryWithID['existingAlias'] > 0) {
                // No luck, go the other route
                return self::generateSeoURL($input, $type, $typeID, $languageID, $storeID, $categoryID, $urlAliasID, $increment + 1);
            }
        }


        $query = rtrim($type, '=') . '=' . $typeID;

        // Insert keyword
        if ($urlAliasID > 0) {
            // Remove old record based on alias_id
            Database::query('DELETE FROM PREFIX_url_alias WHERE url_alias_id = :urlAliasID LIMIT 1', array('urlAliasID' => $urlAliasID));
        }
        else {
            if ($type == 'manufacturer_id') {
                Database::query("DELETE FROM PREFIX_url_alias WHERE query = :query AND store_id = :sid", array('query' => $query, 'sid' => $storeID));
            }
            else {
                // Remove old record based on query and language_id
                Database::query("DELETE FROM PREFIX_url_alias WHERE query = :query AND language_id = :lid", array('query' => $query, 'lid' => $languageID));
            }
        }

        Database::query('INSERT INTO PREFIX_url_alias (query, keyword, language_id, store_id, category_id) VALUES (
            :query,
            :keyword,
            :languageID,
            :storeID,
            :categoryID)', array(
            'query'      => $query,
            'keyword'    => $keyword,
            'languageID' => $languageID,
            'storeID'    => $storeID,
            'categoryID' => $categoryID));

        return $keyword;
    }

    public static function strToURI($str)
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
