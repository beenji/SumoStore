<?php
namespace Sumo;
class ModelSettingsMenu extends Model
{
    private $active;

    public function getActiveMenu()
    {
        if (!empty($this->active)) {
            return $this->active;
        }
        $active = 'common/home';
        if (isset($this->request->get['route'])) {
            $tmp = explode('/', $this->request->get['route']);
            $active = $tmp[0] . '/' . $tmp[1];
            if (!empty($tmp[2])) {
                $active .= '/' . $tmp[2];
            }
        }

        $this->active = $active;
        return $active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getParentId($active = '')
    {
        if ($active == '') {
            $active = $this->getActiveMenu();
        }
        $check = Database::query("SELECT menu_id FROM PREFIX_admin_menu WHERE url = :url", array('url' => $active))->fetch();
        if (is_array($check) && isset($check['menu_id'])) {
            return $check['menu_id'];
        }
        return false;
    }

    public function getDashboardItems()
    {
        $active = $this->getActiveMenu();
        $return = Cache::find('adi_' . md5($active));
        if (!is_array($return)) {
            $return = Database::fetchAll("
                SELECT *
                FROM PREFIX_admin_menu
                WHERE parent_id = (SELECT menu_id FROM PREFIX_admin_menu WHERE url = :url)
                    OR url = :url2
                ORDER BY sort_order ASC, parent_id ASC",
                array(
                    'url' => $active, 'url2' => $active
                )
            );
            Cache::set('admin_menu.adi_' . md5($active), $return);
        }
        return $return;
    }

    public function generateMenu($active = '', $ignorePermissions = false)
    {
        if ($ignorePermissions) {
            $file = 'admin_menu';
        }
        else {
            $file = 'admin_menu.' . $this->user->getUserGroup();
        }

        $items = Cache::tempFind($file);

        if (!is_array($items)) {
            $itemList = Database::fetchAll("SELECT *  FROM PREFIX_admin_menu ORDER BY sort_order ASC");
            $items = $children = array();

            foreach ($itemList as $list) {
                if (empty($list['url'])) {
                    $list['url'] = 'error/not_found';
                }
                if ($list['parent_id'] >= 1) {
                    $children[$list['parent_id']][$list['menu_id']] = $list;
                }
                else {
                    $items[$list['menu_id']] = $list;
                }
            }
            foreach ($children as $parent_id => $kids) {
                foreach ($kids as $list) {
                    $items[$parent_id]['children'][$list['menu_id']] = $list;
                }
            }

            Cache::set($file, $items);
        }

        if ($ignorePermissions) {
            return $items;
        }

        $activated = array();

        foreach ($items as $id => $list) {

            if ($list['url'] == $active) {
                $items[$id]['active'] = true;
                $activated['parent'] = true;
            }
            if (isset($list['children'])) {
                foreach ($list['children'] as $kid => $kidlist) {
                    $check = array();
                    $check = explode('/', $kidlist['url']);
                    $check = $check[0] . '/' . $check[1];

                    if (!$this->user->hasPermission('access', $check)) {
                        unset($items[$id]['children'][$kid]);
                    }
                    if ($kidlist['url'] == $active) {
                        $items[$id]['active'] = true;
                        $items[$id]['children'][$kid]['active'] = true;
                        $activated['child'] = true;
                    }
                }
            }
        }

        if (!count($activated)) {
            foreach ($items as $id => $list) {
                if (isset($list['children'])) {
                    foreach ($list['children'] as $kid => $kidlist) {
                        $check = array();
                        $check = explode('/', $kidlist['url']);
                        $check = $check[0] . '/' . $check[1];

                        $active = explode('/', $active);
                        $active = $active[0] . '/' . $active[1];
                        if ($check == $active) {
                            $activated['parent'] = $activated['child'] = true;
                            $items[$id]['active'] = true;
                            $items[$id]['children'][$kid]['active'] = true;
                        }
                    }
                }
            }
        }

        if (!count($activated)) {
            $active = explode('/', $active);
            if ($active[0] == 'app') {
                $app = preg_replace('/^[a-z]$/', '', $active[1]);
                $check = Database::query("SELECT category FROM PREFIX_apps WHERE list_name = :name", array('name' => $app))->fetch();
                if (!isset($check['category'])) {
                    $check['category'] = 99;
                }
                $items[27]['active'] = true;
                switch ($check['category']) {
                    case 1:
                        $items[27]['children'][29]['active'] = true;
                        break;

                    case 2:
                        $items[27]['children'][30]['active'] = true;
                        break;

                    case 99:
                        $items[27]['children'][31]['active'] = true;
                        break;
                }
            }
        }

        return $items;
    }

    public function getParentItems()
    {
        return Database::fetchAll("SELECT * FROM PREFIX_admin_menu WHERE parent_id = 0 ORDER BY sort_order ASC");
    }

    public function getChildItems($parent_id)
    {
        return Database::fetchAll("SELECT * FROM PREFIX_admin_menu WHERE parent_id = :id ORDER BY sort_order ASC", array('id' => $parent_id));
    }

    public function addMenuItem($data)
    {
        $sort = Database::query("SELECT MAX(sort_order) AS sort_order FROM PREFIX_admin_menu WHERE parent_id = :parent_id", array('parent_id' => $data['parent_id']))->fetch();
        if (is_array($sort)) {
            $sort_order = $sort['sort_order'] + 1;
        }
        else {
            $sort_order = 1;
        }
        $data['sort_order'] = $sort_order;
        Database::query("INSERT INTO PREFIX_admin_menu SET url = :url, name = :name, description = :description, icon = :icon, parent_id = :parent_id, sort_order = :sort_order", $data);

        Cache::remove('admin_menu', true);

        return Database::lastInsertId();
    }

    public function editMenuItem($data, $id)
    {
        $data['id'] = $id;
        Database::query("UPDATE PREFIX_admin_menu SET url = :url, name = :name, description = :description, icon = :icon, parent_id = :parent_id WHERE menu_id = :id", $data);

        Cache::remove('admin_menu', true);
    }

    public function getMenuItem($id)
    {
        return Database::query("SELECT * FROM PREFIX_admin_menu WHERE menu_id = :id", array('id' => $id))->fetch();
    }

    public function removeMenuItem($id)
    {
        $min = Database::query("SELECT MIN(menu_id) AS menu_id FROM PREFIX_admin_menu")->fetch();
        if (!is_array($min) || empty($menu_id)) {
            $menu_id = 0;
        }
        else {
            $menu_id = $min['menu_id'];
        }
        Database::query("UPDATE PREFIX_admin_menu SET parent_id = :parent WHERE parent_id = :id", array('id' => $id, 'parent' => $menu_id));
        Database::query("DELETE FROM PREFIX_admin_menu WHERE menu_id = :id", array('id' => $id));

        Cache::remove('admin_menu', true);
    }

    public function saveMenuOrder($data)
    {
        #exit(print_r($data,true));
        #Database::query("UPDATE PREFIX_admin_menu SET sort_order = 0, parent_id = 0");
        $so = $co = 0;
        foreach ($data as $list) {
            $so++;
            Database::query("UPDATE PREFIX_admin_menu SET sort_order = :order, parent_id = 0 WHERE menu_id = :id", array('order' => $so, 'id' => $list['id']));
            if (isset($list['children']) && count($list['children'])) {
                #$co = 0;
                foreach ($list['children'] as $child) {
                    #$co++;
                    $so++;
                    Database::query("UPDATE PREFIX_admin_menu SET sort_order = :order, parent_id = :parent WHERE menu_id = :id", array('order' => $so, 'parent' => $list['id'], 'id' => $child['id']));
                }
            }
        }

        Cache::remove('admin_menu', true);

        return true;
    }
}
