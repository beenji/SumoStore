<?php
namespace Sumo;
class ModelDesignSumobuilder extends Model
{
    public function getThemes()
    {
        $return = array();
        $data = Database::fetchAll("SELECT theme_id, name FROM PREFIX_builder_themes");
        foreach ($data as $list) {
            $return[$list['theme_id']] = $list['name'];
        }
        return $return;
    }

    public function getTheme($theme_id)
    {
        $data   = Database::fetchAll("SELECT name, create_date FROM PREFIX_builder_themes WHERE theme_id = :id", array('id' => $theme_id));
        if (!is_array($data) || !count($data)) {
            return false;
        }

        $theme  = Database::fetchAll("SELECT setting_name, setting_value FROM PREFIX_builder_themes_settings WHERE theme_id = :id", array('id' => $theme_id));
        foreach ($theme as $list) {
            if (($tmp = @json_decode($list['setting_value'], true)) !== false) {
                $list['setting_value'] = $tmp;
            }
            $data[$list['setting_name']] = $list['setting_value'];
        }

        return $data;
    }

    public function saveTheme($theme_id, $data)
    {
        $dataCheck = Database::fetchAll("SELECT name, create_date FROM PREFIX_builder_themes WHERE theme_id = :id", array('id' => $theme_id));
        if (!is_array($data) || !count($data)) {
            $theme_id = $this->addTheme($data['name']);
        }

        unset($data['name']);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Database::query(
                "DELETE FROM PREFIX_builder_themes_settings WHERE setting_name = :key AND theme_id = :id",
                array(
                    'key'   => $key,
                    'id'    => $theme_id
                )
            );
            Database::insert(
                "PREFIX_builder_themes_settings",
                array(
                    'setting_name'  => $key,
                    'setting_value' => $value,
                    'theme_id'      => $theme_id
                )
            );
        }

        Cache::remove('builder');
    }

    public function addTheme($name)
    {
        $check = Database::query("SELECT theme_id FROM PREFIX_builder_themes WHERE name = :name", array('name' => $name))->fetch();
        if (is_array($check) && isset($check['theme_id'])) {
            return false;
        }
        return Database::insert("PREFIX_builder_themes", array('name' => $name, 'create_date' => date('Y-m-d H:i:s')));
    }

    public function removeTheme($theme_id)
    {
        if ($theme_id == 1) {
            return false;
        }
        Database::query("DELETE FROM PREFIX_builder_themes_settings WHERE theme_id = :id", array('id' => $theme_id));
        return Database::query("DELETE FROM PREFIX_builder_themes WHERE theme_id = :id", array('id' => $theme_id));
    }

    public function getIcons($inAdmin = false)
    {
        $path = '/';
        if ($inAdmin) {
            $path = '/../';
        }
        $path .= 'catalog/view/theme/sumobuilder/image/social/';

        $icons = array();

        $icons[] = array(
            'src'   => $path . 'facebook.png',
            'name'  => 'facebook',
            'url'   => 'https://www.facebook.com/'
        );

        $icons[] = array(
            'src'   => $path . 'twitter.png',
            'name'  => 'twitter',
            'url'   => 'https://twitter.com/'
        );

        $icons[] = array(
            'src'   => $path . 'pinterest.png',
            'name'  => 'pinterest',
            'url'   => 'https://www.pinterest.com/',
        );

        $icons[] = array(
            'src'   => $path . 'googleplus.png',
            'name'  => 'googleplus',
            'url'   => 'https://plus.google.com/',
        );

        $icons[] = array(
            'src'   => $path . 'linkedin.png',
            'name'  => 'linkedin',
            'url'   => 'https://www.linkedin.com/',
        );

        $icons[] = array(
            'src'   => $path . 'dribble.png',
            'name'  => 'dribble',
            'url'   => 'https://www.dribble.com/',
        );

        $icons[] = array(
            'src'   => $path . 'instagram.png',
            'name'  => 'instagram',
            'url'   => 'https://www.instagram.com/',
        );

        $icons[] = array(
            'src'   => $path . 'youtube.png',
            'name'  => 'youtube',
            'url'   => 'https://www.youtube.com/',
        );

        $icons[] = array(
            'src'   => $path . 'vimeo.png',
            'name'  => 'vimeo',
            'url'   => 'https://www.vimeo.com/',
        );

        $icons[] = array(
            'src'   => $path . 'flickr.png',
            'name'  => 'flickr',
            'url'   => 'https://www.flickr.com/',
        );

        return $icons;
    }

    public function getFonts($inAdmin = false)
    {
        $fonts = array(
            'Arial',
            'Helvetica',
            'Open Sans',
            'Oswald',
            'Droid Sans',
            'Roboto',
            'Lato',
            'Open Sans Condensed',
            'PT Sans',
            'Drois Serif',
            'Ubuntu',
            'PT Sans Narrow',
            'Roboto Condensed',
            'Source Sans Pro',
            'Yanone Kaffeesatz',
            'Lora',
            'Arvo'
        );
        sort($fonts);
        return $fonts;
    }
}
