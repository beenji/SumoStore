<?php
namespace Sumo;
class ModelLocalisationTranslation extends Model
{
    public function getTranslationsByTranslation($letter = '', $language_id = 0)
    {
        if (!$language_id) {
            return false;
        }

        if (strlen($letter) == 1) {
            return Database::fetchAll("
                SELECT t.id, t.key_id, t.value, tk.name
                FROM PREFIX_translations AS t
                LEFT JOIN PREFIX_translations_keys AS tk
                ON tk.id = t.key_id
                WHERE t.language_id = :lid
                AND LOWER(t.value) LIKE :search
                ORDER BY t.value ASC",
                array(
                    'lid' => $language_id,
                    'search' => '' . $letter . '%'
                )
            );
        }
        else if ($letter == 'empty') {
            $ignore = '';
            $ignoreList = Database::fetchAll("
                SELECT key_id AS id
                FROM PREFIX_translations
                WHERE language_id = :lang
                ",
                array(
                    'lang' => $language_id
                )
            );
            foreach ($ignoreList as $list) {
                if (empty($ignore)) {
                    $ignore = $list['id'];
                }
                else {
                    $ignore .= ',' . $list['id'];
                }
            }
            if (!empty($ignore)) {
                $ignore = 'WHERE tk.id NOT IN(' . $ignore . ')';
            }
            return Database::fetchAll("
                SELECT id, name, (SELECT value FROM PREFIX_translations WHERE key_id = tk.id AND language_id = :default) AS default_value
                FROM PREFIX_translations_keys AS tk
                " . $ignore . "
                ORDER BY default_value ASC",
                array(
                    'default' => $this->config->get('config_language_id')
                )
            );
        }
        else {
            return Database::fetchAll("
                SELECT t.id, t.key_id, t.value, tk.name
                FROM PREFIX_translations AS t
                LEFT JOIN PREFIX_translations_keys AS tk
                ON tk.id = t.key_id
                WHERE t.language_id = :lid
                AND (
                    t.value LIKE '<%'
                    OR t.value LIKE '\%%'
                    OR t.value LIKE '(%'
                    OR t.value LIKE '^%'
                    OR t.value LIKE '!%'
                    OR t.value LIKE '$%'
                    OR t.value LIKE '*%'
                    OR t.value LIKE ''
                )
                ORDER BY t.value ASC",
                array(
                    'lid' => $language_id,
                )
            );
        }
    }
}
