<?php
namespace Sumo;
class Mailer extends Singleton
{
    public static $instance;

    private static $settings, $config, $order, $invoice, $return, $customer, $status;

    public static function setup($config)
    {
        self::$config = $config;
        self::$settings['protocol'] = $config->get('email_protocol');
        if (empty(self::$settings['protocol']) || self::$settings['protocol'] == 'mail') {
            $tmp = $config->get('mail');
            if (!empty($tmp['parameters'])) {
                self::$settings['parameters'] = $tmp['parameters'];
            }
        }
        else {
            self::$settings = $config->get('smtp');
        }
    }

    public static function setOrder($data)
    {
        self::$order = $data;
    }

    public static function setReturn($data)
    {
        self::$return = $data;
    }

    public static function setCustomer($data)
    {
        self::$customer = $data;
    }

    public static function setInvoice($data)
    {
        self::$invoice = $data;
    }

    public static function getTemplate($idOrKey, $language_id = null)
    {
        if ($language_id == null || !$language_id) {
            $language_id = self::$config->get('language_id');
        }

        if (is_string($idOrKey) && !is_numeric($idOrKey)) {
            $where = 'event_key';
        }
        else {
            $where = 'mail_id';
        }

        $content = Database::fetchAll(
            "SELECT language_id, title, content, event_key
            FROM PREFIX_mails_content AS mc
            LEFT JOIN PREFIX_mails_to_events AS mte
                ON mc.mail_id = mte.mail_id
            WHERE mte." . $where . " = :input",
            array(
                'input' => $idOrKey
            )
        );

        if (!$content || !count($content)) {
            Logger::warning('[Mailer] Template not found for ' . $where . ' ' . $idOrKey);
            return false;
        }

        $data = array();
        foreach ($content as $list) {
            $data[$list['language_id']] = $list;
        }

        if (isset($data[$language_id])) {
            $template = $data[$language_id];
        }
        else {
            $template = reset($data);
        }

        $template['content'] = html_entity_decode($template['content']);

        $status = null;
        $event  = explode('_', $template['event_key']);

        if (count($event) == 4) {
            if ($event[2] == 'status' && !empty($event[3])) {
                $table  = 'PREFIX_' . $event[1] . '_status';
                $where  = $event[1] . '_status_id';
                $id     = $event[3];

                try {
                    self::$status = Database::query(
                        "SELECT " . $where . " AS id, name FROM " . $table . " WHERE " . $where . " = :id AND language_id = :lang",
                        array(
                            'id'    => $id,
                            'lang'  => $language_id
                        )
                    )->fetch();
                    self::$status['type'] = strtoupper($event[1][0]) . 'ID.';
                }
                catch (\Exception $e) { }
            }
        }

        $toReplace = array();
        preg_match_all('/{+(.*?)}/', $template['content'], $toReplace);

        foreach ($toReplace[1] as $find) {
            $template['content'] = self::shortcodeReplacement($find, $template['content']);
        }

        $toReplace = array();
        preg_match_all('/{+(.*?)}/', $template['title'], $toReplace);
        foreach ($toReplace[1] as $find) {
            $template['title'] = self::shortcodeReplacement($find, $template['title']);
        }

        return $template;
    }

    public static function shortcodeReplacement($find, $text)
    {
        switch ($find) {
            case 'name':
                return str_replace('{name}', self::$config->get('name'), $text);
                break;

            case 'firstname':
            case 'lastname':
                return str_replace('{' . $find . '}', self::$customer[$find], $text);
                break;

            case 'base':
            case 'store_url':
                $default = self::$config->get('base_default');
                return str_replace('{' . $find . '}', $default . '://' . str_replace($default . '://', '', self::$config->get('base_' . $default)), $text);
                break;

            case 'store_id':
                return str_replace('{store_id}', self::$config->get('store_id'), $text);
                break;

            case 'store_logo':
                return str_replace('{store_logo}', 'image/' . self::$config->get('logo'),  $text);
                break;

            case 'status':
                if (isset(self::$status['name'])) {
                    return str_replace('{status}', self::$status['name'], $text);
                }
                break;

            case 'status_id':
                if (isset(self::$order['order_id'])) {
                    return str_replace('{status_id}', self::$status['type'] . str_pad(self::$order['order_id'], 6, 0, STR_PAD_LEFT), $text);
                }
                if (isset(self::$return['return_id'])) {
                    return str_replace('{status_id}', self::$status['type'] . str_pad(self::$return['return_id'], 5, 0, STR_PAD_LEFT), $text);
                }
                if (isset(self::$invoice['invoice_no'])) {
                    return str_replace('{status_id}', self::$invoice['invoice_no'], $text);
                }
                return str_replace('{status_id}', '', $text);
                break;
        }
        return $text;
    }
}
