<?php
namespace Sumo;
class ControllerLocalisationTranslation extends Controller
{
    private $error = array();

    public function index()
    {
        if (!isset($this->request->get['language_id'])) {
            $this->redirect($this->url->link('localisation/translation', 'language_id=' . $this->config->get('language_id') . '&token=' . $this->session->data['token'], 'SSL'));
        }
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_LOCALISATION_TRANSLATION_TITLE'));

        $breadcrumbs = array(
            'text'      => Language::getVar('SUMO_ADMIN_LOCALISATION_TRANSLATION_TITLE'),
            'href'      => $this->url->link('localisation/translation', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $this->document->setBreadcrumbs($breadcrumbs);

        $this->data['token'] = $this->session->data['token'];

        $return = array();
        $language = isset($this->request->get['language_id']) ? $this->request->get['language_id'] : $this->config->get('language_id');
        $this->data['language'] = $language;

        $translation_keys = Database::fetchAll('
            SELECT tk.id, tk.name, (SELECT value FROM PREFIX_translations WHERE key_id = tk.id AND language_id = :language) AS default_value
            FROM PREFIX_translations_keys AS tk
            ORDER BY name ASC
        ', array('language' => $language));

        foreach ($translation_keys as $list) {
            if (isset($list['default_value'][0])) {
                $return[strtolower($list['default_value'][0])][$list['default_value'] . $list['id']] = $list;
            }
            elseif (isset($list['name'][0])) {
                $return[strtolower($list['name'][0])][$list['name']] = $list;
            }
        }

        foreach ($return as $key => $list) {
            $this->data['translations'][$key] = $list;
        }

        $this->data['languages'] = array();
        $this->load->model('localisation/language');
        foreach ($this->model_localisation_language->getLanguages() as $list) {
            $this->data['languages'][] = array(
                'selected'  => $language == $list['language_id'],
                'name'      => $list['name'],
                'code'      => $list['code'],
                'url'       => $this->url->link('localisation/translation', 'language_id=' . $list['language_id'] . '&token=' . $this->session->data['token'], 'SSL')
            );
        }

        $this->template = 'localisation/translation.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        $action = isset($this->request->get['action']) ? $this->request->get['action'] : '';
        $lang   = isset($this->request->get['lang']) ? $this->request->get['lang'] : $this->config->get('language_id');
        switch ($action) {

            case 'save':
                if (!empty($this->request->post['key_id']) && !empty($this->request->post['value'])) {
                    Language::setVar($this->request->post['key_id'], $lang, html_entity_decode($this->request->post['value'], ENT_QUOTES, 'UTF-8'));
                }
                break;

            case 'fetch':
                if (isset($_POST['keys']) && is_array($_POST['keys']) && !empty($_POST['keys']) && !empty($lang)) {
                    foreach ($_POST['keys'] as $key) {
                        if (!ctype_digit($key)) {
                            return;
                        }
                    }
                    $keys = implode(',', $_POST['keys']);
                    $result = Database::fetchAll("
                        SELECT t.id, t.key_id, t.value,
                            (SELECT value FROM PREFIX_translations WHERE key_id = t.key_id AND language_id = " . $this->config->get('language_id') . ") AS default_name,
                            (SELECT name FROM PREFIX_translations_keys WHERE id = t.id) AS default_key
                        FROM PREFIX_translations AS t
                        WHERE language_id = " . $lang . "
                            AND key_id IN(" . $keys . ")");
                    $this->response->setOutput(json_encode($result));
                }
                break;

            case 'empty':
                $keys = array();
                $keys = Database::fetchAll("
                    SELECT id, name, (SELECT value FROM PREFIX_translations WHERE key_id = tk.id AND language_id = " . $this->config->get('language_id') . ") AS default_value FROM PREFIX_translations_keys AS tk ORDER BY id
                ");


                $translated = Database::fetchAll("
                    SELECT key_id AS id, value FROM PREFIX_translations WHERE language_id = " . $lang
                );

                foreach ($translated as $list) {
                    $list['value'] = trim($list['value']);
                    if (isset($keys[$list['id']]) && !empty($list['value'])) {
                        unset($keys[$list['id']]);
                    }
                }

                if (!$keys || count($keys) == 0) {
                    $keys['nothing_to_translate'] = 1;
                }
                $this->response->setOutput(json_encode($keys));
                break;

            default:
                $this->response->setOutput(json_encode(array('empty' => 'request')));
                break;
        }
    }
}
