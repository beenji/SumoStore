<?php
namespace Sumo;
class ControllerCommonHeader extends Controller
{
    private $errors = array();

    protected function index()
    {
        $this->data['base'] = $this->url->link('', '', 'SSL');

        $this->validate();
        $this->data['errors'] = $this->errors;
        $this->data['breadcrumbs'] = array();

        if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
            // Do nothing!
        } else {
            // Fetch menu
            $this->load->model('settings/menu');
            $active = $this->model_settings_menu->getActiveMenu();
            $this->data['active'] = $active;
            $checkDashboard = explode('/', $active);
            if (end($checkDashboard) == 'dashboard') {
                $this->document->setTitle('Dashboard');
            }
            $this->data['menu'] = $this->model_settings_menu->generateMenu($active);

            if ($active != 'common/home') {
                $this->data['breadcrumbs'][] = array('text' => 'Home', 'href' => $this->url->link('common/home', '', 'SSL'));
            }
        }

        $this->data['token'] = $this->session->data['token'];
        $this->data['title'] = $this->document->getTitle();
        $this->data['description'] = $this->document->getDescription();
        $this->data['keywords'] = $this->document->getKeywords();
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['breadcrumbs'] = array_merge($this->data['breadcrumbs'], $this->document->getBreadcrumbs());
        $this->data['stores'] = $this->model_settings_stores->getStores();

        $this->template = 'common/header.tpl';

        $this->render();
    }

    private function validate()
    {
        if (phpversion() < '5.3') {
            $this->errors[] = 'Warning: You need to use PHP5 or above for SumoStore to work!';
        }

        if (!ini_get('file_uploads')) {
            $this->errors[] = 'Warning: file_uploads needs to be enabled!';
        }

        if (ini_get('session.auto_start')) {
            $this->errors[] = 'Warning: SumoStore will not work with session.auto_start enabled!';
        }

        if (!extension_loaded('mysql')) {
            $this->errors[] = 'Warning: MySQL extension needs to be loaded for SumoStore to work!';
        }

        if (!extension_loaded('gd')) {
            $this->errors[] = 'Warning: GD extension needs to be loaded for SumoStore to work!';
        }

        if (!extension_loaded('curl')) {
            $this->errors[] = 'Warning: CURL extension needs to be loaded for SumoStore to work!';
        }

        if (!function_exists('mcrypt_encrypt')) {
            $this->errors[] = 'Warning: mCrypt extension needs to be loaded for SumoStore to work!';
        }

        if (!extension_loaded('zlib')) {
            $this->errors[] = 'Warning: ZLIB extension needs to be loaded for SumoStore to work!';
        }

        if (!is_writable(DIR_CACHE)) {
            $this->errors[] = 'Warning: Cache directory needs to be writable for SumoStore to work!';
        }

        if (!is_writable(DIR_LOGS)) {
            $this->errors[] = 'Warning: Logs directory needs to be writable for SumoStore to work!';
        }

        if (!is_writable(DIR_IMAGE)) {
            $this->errors[] = 'Warning: Image directory needs to be writable for SumoStore to work!';
        }

        if (!is_writable(DIR_IMAGE . '/cache')) {
            $this->errors[] = 'Warning: Image cache directory needs to be writable for SumoStore to work!';
        }

        if (!is_writable(DIR_IMAGE . '/data')) {
            $this->errors[] = 'Warning: Image data directory needs to be writable for SumoStore to work!';
        }

        if (!is_writable(DIR_DOWNLOAD)) {
            $this->errors[] = 'Warning: Download directory needs to be writable for SumoStore to work!';
        }

        if (is_dir(DIR_SYSTEM . '../install/') && !defined('DEVELOPMENT')) {
            $this->errors[] = Language::getVar('SUMO_ADMIN_WARNING_INSTALLATION_DIRECTORY');
        }

        if (file_exists(DIR_SYSTEM . '../404.shtml')) {
            $this->errors[] = 'Warning: You should remove the 404.shtml file in your / directory to prevent "ugly" 404 pages. SumoStore handles a 404 in it\'s own way, to keep track of your visitors and to show them a nicer page';
        }
    }
}
