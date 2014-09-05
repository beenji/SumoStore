<?php
namespace Sumo;
class ControllerSettingsStatus extends Controller
{

    private $types = array('order_status', 'stock_status', 'return_status', 'return_action', 'return_reason');

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_STATUS_SETTINGS'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_STATUS_SETTINGS')));
        $this->document->addScript('view/js/pages/status_form.js');

        $this->load->model('localisation/language');

        $this->data['languages']    = $this->model_localisation_language->getLanguages();
        $this->data['types']        = $this->types;

        $this->template = 'settings/status/list.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function ajaxGetList()
    {
        usleep(5000);
        $type = !empty($this->request->post['type']) ? $this->request->post['type'] : '';
        if (!empty($type) && in_array($type, $this->types)) {
            $data = array();
            $tmp = Database::fetchAll("SELECT name, " . $type . "_id AS id, language_id FROM PREFIX_" . $type);
            if (!count($tmp)) {
                $data['isEmpty'] = Language::getVar('SUMO_ADMIN_STATUS_SETTINGS_EMPTY');
            }
            else {
                // Default language first
                foreach ($tmp as $list) {
                    if ($list['language_id'] == $this->config->get('language_id')) {
                        $data['return'][$list['id']] = $list;
                    }
                }
                // Secondary language?
                foreach ($tmp as $list) {
                    if (!isset($data['return'][$list['id']])) {
                        if (!empty($list['name'])) {
                            $data['return'][$list['id']] = $list;
                        }
                    }
                }
                // Yeah well, we tried everything....
                foreach ($tmp as $list) {
                    if (!isset($data['return'][$list['id']])) {
                        $list['name'] = Language::getVar('SUMO_NOUN_EMPTY');
                        $data['return'][$list['id']] = $list;
                    }
                }
            }
            $this->response->setOutput(json_encode($data));
        }
    }

    public function ajaxGetData()
    {
        $type       = !empty($this->request->post['type']) ? $this->request->post['type'] : '';
        $id         = !empty($this->request->post['id']) ? $this->request->post['id'] : 0;
        if (!empty($type) && in_array($type, $this->types)) {
            $data   = array();
            $tmp    = Database::fetchAll("SELECT language_id, name FROM PREFIX_" . $type . " WHERE " . $type . "_id = :id", array('id' => $id));
            if (count($tmp)) {
                foreach ($tmp as $list) {
                    $data['names'][$list['language_id']] = $list['name'];
                }
            }
            $this->response->setOutput(json_encode($data));
        }
    }

    public function ajaxremove()
    {
        $type   = !empty($this->request->post['type']) ? $this->request->post['type'] : '';
        $id     = !empty($this->request->post['id']) ? $this->request->post['id'] : 0;
        if (!empty($type) && in_array($type, $this->types)) {
            Database::query("DELETE FROM PREFIX_" . $type . " WHERE " . $type . "_id = :id", array('id' => $id));
        }
        $this->response->setOutput('OK');
    }

    public function update()
    {
        Cache::removeAll();
        $type   = !empty($this->request->post['type']) ? $this->request->post['type'] : '';
        $id     = !empty($this->request->post['id']) ? $this->request->post['id'] : 0;
        if (!empty($this->request->get['new'])) {
            $names = $this->request->post['name'];
            if (is_array($names) && count($names) && !empty($type) && in_array($type, $this->types)) {
                $id = 0;
                foreach ($names as $lang => $name) {
                    Database::query(
                        "INSERT INTO PREFIX_" . $type . "
                        SET language_id = :lang,
                            name        = :name",
                        array(
                            'lang'  => $lang,
                            'name'  => $name
                        )
                    );
                    if (!$id) {
                        $id = Database::lastInsertId();
                    }
                    else {
                        Database::query(
                            "UPDATE PREFIX_" . $type . " SET " . $type . "_id = :new WHERE " . $type . "_id = :old",
                            array('old' => Database::lastInsertId(), 'new' => $id)
                        );
                    }
                }
            }
            $this->redirect('index', '', 'SSL');
            exit;
        }
        else {
            if (empty($type) || empty($id) || !in_array($type, $this->types)) {
                return;
            }
            $data = array();
            parse_str(str_replace('amp;', '', $this->request->post['data']), $data);

            if ($type == $data['type']) {
                foreach ($data['name'] as $lang => $name) {
                    Database::query(
                        "UPDATE PREFIX_" . $type . "
                        SET     name                    = :name
                        WHERE   language_id             = :lang
                        AND     " . $type . "_id = :id",
                        array(
                            'name'  => $name,
                            'lang'  => $lang,
                            'id'    => $id
                        )
                    );
                }
            }
            else if (in_array($data['type'], $this->types)) {
                $id = 0;
                foreach ($data['name'] as $lang => $name) {
                    Database::query(
                        "INSERT INTO PREFIX_" . $data['type'] . "
                        SET language_id = :lang,
                            name        = :name",
                        array(
                            'lang'  => $lang,
                            'name'  => $name
                        )
                    );
                    if (!$id) {
                        $id = Database::lastInsertId();
                    }
                    else {
                        Database::query(
                            "UPDATE PREFIX_" . $data['type'] . " SET " . $data['type'] . "_id = :new WHERE " . $data['type'] . "_id = :old",
                            array('old' => Database::lastInsertId(), 'new' => $id)
                        );
                    }
                }
            }

        }

        $this->response->setOutput('OK');
    }
}
