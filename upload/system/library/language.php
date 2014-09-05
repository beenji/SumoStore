<?php
namespace Sumo;
class Language extends Singleton
{
    public static $instance, $vars, $language_id, $language;

    public static function setup($language)
    {
        self::getInstance();
        self::$language_id = $language['language_id'];
        self::$language = $language;
    }

    public static function getVar($key, $extra = array(), $updateTime = true)
    {
        // If, for instance, an error occures before the setup has fully loaded...
        if (isset(self::$language_id) && class_exists('Sumo\Cache')) {
            $cache_string = 'language_' . self::$language_id;
            $cache = Cache::find($cache_string, $key);
            if ($cache) {
                return self::parse($cache, $extra);
            }

            $list = Database::query("
                SELECT value, key_id
                FROM PREFIX_translations
                RIGHT JOIN PREFIX_translations_keys
                ON PREFIX_translations.key_id = PREFIX_translations_keys.id
                WHERE language_id = " . self::$language_id . "
                AND name = :key
            ", array('key' => $key))->fetch();

            if (isset($list['value'])) {
                if (empty($list['value'])) {
                    $list['value'] = $key;
                }
                if ($updateTime) {
                    Database::query("UPDATE PREFIX_translations_keys SET date_used = :date WHERE id = :id", array('id' => $list['key_id'], 'date' => date('Y-m-d H:i:s')));
                }
                Cache::set($cache_string, $key, $list['value']);
                return self::parse($list['value'], $extra);
            }
            else {
                // Set the key as value
                $value = $key;

                // Check if the key is in the database
                $list2 = Database::query("SELECT id FROM PREFIX_translations_keys WHERE name = :key", array('key' => $key))->fetch();

                // Totally new translation_key should be added
                if (!isset($list2['id'])) {
                    Database::query(
                        "INSERT INTO PREFIX_translations_keys
                        SET name        = :key,
                            date_added  = :date",
                        array(
                            'key'       => $key,
                            'date'      => date('Y-m-d H:i:s')
                        )
                    );
                }
                // But if it exists, there's a fair chance the default translation is available
                else {
                    // Fallback language
                    $list3 = Database::query("SELECT value FROM PREFIX_translations WHERE key_id = :id AND language_id = :lid", array('id' => $list2['id'], 'lid' => self::$language['fallback']))->fetch();
                    if (!empty($list3['value'])) {
                        $value = $list3['value'];
                    }
                    else {
                        // Absolute fallback, any translation that is not empty?
                        $list3 = Database::query("SELECT value FROM PREFIX_translations WHERE key_id = :id AND value != '' LIMIT 1", array('id' => $list2['id']))->fetch();
                        if (isset($list3['value'])) {
                            $value = $list3['value'];
                        }
                    }
                }
                if ($value != $key) {
                    Cache::set($cache_string, $key, $value);
                }
            }
        }
        else {
            $value = $key;
        }
        return self::parse($value, $extra);
    }

    public static function setVar($key_id, $lang_id, $value)
    {
        Database::query("DELETE FROM PREFIX_translations WHERE key_id = " . $key_id . " AND language_id = " . $lang_id);
        self::rebuildCacheFor($lang_id);
        return Database::query("INSERT INTO PREFIX_translations (key_id, language_id, value) VALUES(" . (int)$key_id . ", " . (int)$lang_id . ", :value)", array('value' => $value));
    }

    public static function deleteVar($key_id)
    {
        Database::query("DELETE FROM PREFIX_translations WHERE key_id = " . $key_id);
        Database::query("DELETE FROM PREFIX_translations_keys WHERE id = " . $key_id);
        return true;
    }

    public static function getCurrent($type)
    {
        return self::$language[$type];
    }

    public static function getTranslations($lang_id)
    {
        return Database::fetchAll("
            SELECT
                t.id,
                name,
                t.value AS default_value,
                (
                    SELECT
                        t2.value
                    FROM
                        PREFIX_translations AS t2
                    WHERE
                        t2.language_id = " . $lang_id . "
                    AND
                        t2.key_id = t.key_id
                ) AS value
            FROM
                PREFIX_translations AS t
            RIGHT JOIN
                PREFIX_translations_keys
                ON
                    t.key_id = PREFIX_translations_keys.id
            WHERE
                language_id = ". self::$language_id
        );
    }

    public static function rebuildCacheFor($lang_id)
    {
        Cache::remove('language_' . $lang_id, true);
        Logger::warning('Cache for language ' . $lang_id . ' removed');

        Database::query("OPTIMIZE TABLE PREFIX_translations");
        $keys = Database::fetchAll(
                "SELECT
                    id,
                    name,
                    (
                        SELECT value
                        FROM PREFIX_translations
                        WHERE language_id = :lang
                        AND key_id = tk.id
                    ) AS value
                FROM PREFIX_translations_keys AS tk
                WHERE DATE(date_used) > :date",
            array(
                'lang'  => $lang_id,
                'date'  => date('Y-m-d', strtotime('7 days ago'))
            )
        );
        Logger::warning('Translations fetched');

        foreach ($keys as $list) {
            if (!empty($list['value'])) {
                Cache::set('language_' . $lang_id, $list['name'], $list['value']);
            }
        }
        Logger::warning('Translations set');
    }

    public static function parse($string, $extra = array())
    {
        if (is_string($extra)) {
            $extra = array($extra);
        }
        if (!count($extra)) {
            return $string;
        }
        return vsprintf($string, $extra);
    }
}
