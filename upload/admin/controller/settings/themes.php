<?php
namespace Sumo;

class ControllerSettingsThemes extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_THEMES_SETTINGS'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_THEMES_SETTINGS')));

        $templates = glob(DIR_HOME . 'catalog/view/theme/*');
        foreach ($templates as $dir) {
            $tmp = explode('/', $dir);
            $name = end($tmp);
            if (file_exists($dir . '/information.php')) {
                include $dir . '/information.php';
                $template[$name]['edit'] = $this->url->link('settings/themes/builder', 'token=' . $this->session->data['token'] . '&theme=' . $name, 'SSL');
            }
            else {
                Logger::warning('Template ' . $name . ' does not have information.php');
                $template[$name] = array('error' => true);
            }
            $template[$name]['active'] = array();
            $stores = Database::fetchAll("SELECT store_id FROM PREFIX_settings_stores WHERE setting_name = 'template' AND setting_value = :tmpl", array('tmpl' => $name));
            foreach ($stores as $check) {
                $store = Database::query("SELECT setting_value AS name FROM PREFIX_settings_stores WHERE store_id = :id AND setting_name = 'title'", array('id' => $check['store_id']))->fetch();
                if (is_array($store)) {
                    $template[$name]['active'][] = $store['name'];
                }
            }

            $this->data['themes'][$name] = $template[$name];
        }

        $this->template = 'settings/themes/list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function builder()
    {
        if (empty($this->request->get['theme'])) {
            $this->redirect($this->url->link('settings/themes', '', 'SSL'));
            exit;
        }

        if (empty($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0;
        }

        $theme = $this->request->get['theme'];

        $this->document->setTitle(Language::getVar('SUMO_ADMIN_THEMES_BUILDER'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_THEMES_SETTINGS'), 'href' => $this->url->link('settings/themes/', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_THEMES_BUILDER')));
        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');

        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['current_store'] = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        if (empty($this->request->get['action'])) {
            $this->request->get['action'] = 'colors';
        }

        $action = $this->request->get['action'];
        if (!method_exists($this, $action)) {
            Logger::warning('It seems ' . $action . ' is not callable?');
            $action = 'colors';
        }

        $this->data['action']   = $action;
        $this->data['theme']    = $theme;
        $this->data['content']  = $this->$action($this->data['current_store'], $this->data['theme']);

        $this->template = 'settings/themes/builder.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );

        //$this->data['content'] = $this->data[$action];

        $this->response->setOutput($this->render());
    }

    public function colorsExport()
    {
        $store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        $theme = $this->request->get['theme'];

        if (empty($theme)) {
            $this->redirect($this->url->link('settings/themes', '', 'SSL'));
        }

        $colors = $this->model_settings_stores->getSetting($store_id, 'colors_' . $theme);

        $this->response->addHeader('Content-Disposition: attachment; filename="' . $theme . '.sumostyle');
        $this->response->addHeader('Content-Type: application/force-download');
        $this->response->addHeader('Content-Type: application/ocret-stream');
        $this->response->addHeader('Content-Type: application/download');
        $this->response->addHeader('Content-Description: SumoStyle Export');
        $this->response->addHeader('Content-Length: ' . strlen(json_encode($colors)));
        $this->response->setOutput(json_encode($colors));
        $this->response->output();
        exit;
    }

    public function colorsImport()
    {
        $store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        $theme = $this->request->get['theme'];

        if (empty($theme) || !isset($this->request->files['upload']['tmp_name'])) {
            exit;
        }

        $data       = file_get_contents($this->request->files['upload']['tmp_name']);
        $name       = $this->request->files['upload']['name'];
        $extension  = substr(strrchr($name, '.'), 1);

        if ($extension != 'sumostyle' || empty($data)) {
            exit;
        }

        // json_decode the data
        if (!preg_match('/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/', preg_replace('/"(\\.|[^"\\])*"/g', '', $data))) {
            $new    = json_decode($data, true);
            $this->model_settings_stores->setSetting($store_id, 'colors_' . $theme, $new);
            exit(json_encode(array('result' => 'OK')));
        }
        else {
            exit(json_encode(array('result' => 'NOT_OK')));
        }

        exit;
    }

    public function reset()
    {
        $store_id = 0;
        if (!empty($this->request->get['store_id'])) {
            $store_id = (int)$this->request->get['store_id'];
        }

        if (!empty($this->request->get['theme'])) {
            $this->model_settings_stores->setSetting($store_id, 'colors_' . $this->request->get['theme'], array());
        }

        $this->redirect($this->url->link('settings/themes'));
    }

    private function display($store_id)
    {
        $this->data['widgets'] = Apps::getAvailable($store_id, 3);
        $this->template = 'settings/themes/builder/display.tpl';
        return $this->render();
    }

    private function colors($store_id, $theme)
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_settings_stores->setSetting($store_id, 'colors_' . $theme, $this->request->post['colors']);
        }

        $this->data['colors'] = $this->model_settings_stores->getSetting($store_id, 'colors_' . $theme);

        $this->template = 'settings/themes/builder/colors.tpl';
        return $this->render();
    }

    private function css($store_id, $theme)
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_settings_stores->setSetting($store_id, 'stylesheet_' . $theme, $this->request->post['css']);
        }

        $this->data['css'] = $this->model_settings_stores->getSetting($store_id, 'stylesheet_' . $theme);

        $this->template = 'settings/themes/builder/css.tpl';
        return $this->render();
    }

    private function header($store_id, $theme)
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            Logger::error(print_r($this->request->post,true));
            $this->model_settings_stores->setSetting($store_id, 'header_' . $theme, $this->request->post['header']);
        }

        $this->data['settings'] = $this->model_settings_stores->getSetting($store_id, 'header_' . $theme);

        $this->template = 'settings/themes/builder/header.tpl';
        return $this->render();
    }

    private function footer($store_id, $theme)
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_settings_stores->setSetting($store_id, 'footer_' . $theme, $this->request->post['footer']);
        }

        $this->load->model('localisation/language');
        $this->load->model('catalog/information');

        $this->data['settings'] = $this->model_settings_stores->getSetting($store_id, 'footer_' . $theme);

        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        $pages = $this->model_catalog_information->getInformations();
        foreach ($pages as $list) {
            if ($list['store_id'] != $store_id || $list['status'] == 0) {
                continue;
            }
            $this->data['pages'][] = $list;
        }

        $this->template = 'settings/themes/builder/footer.tpl';
        return $this->render();
    }
}
