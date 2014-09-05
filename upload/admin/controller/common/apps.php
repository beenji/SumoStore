<?php
namespace Sumo;
class ControllerCommonApps extends Controller
{
    private $types = array(
        'shipping'  => 1,
        'payment'   => 2,
        'themes'    => 3,
        'other'     => 99
    );

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_APPS_DASHBOARD'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')));

        $this->template = 'dashboard/index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    /** Magic function to action-ize app-types :) **/
    public function __call($name, $arguments = '')
    {
        if (isset($this->types[$name])) {
            $this->action($name);
        }
    }

    public function install()
    {
        $appname = isset($this->request->get['list_name']) ? preg_replace('/^[a-z]$/', '', $this->request->get['list_name']) : false;
        $category = isset($this->request->get['category']) ? $this->request->get['category'] : 'other';
        if (!$appname) {
            $this->redirect($this->url->link('common/apps/' . $category, '', 'SSL'));
        }

        if (file_exists(DIR_HOME . 'apps/' . $appname . '/model/setup.php')) {
            include(DIR_HOME . 'apps/' . $appname . '/model/setup.php');
            include(DIR_HOME . 'apps/' . $appname . '/information.php');
            $call = $appname . '\ModelSetup';
            $call = new $call($this->registry);

            try {
                $call->install();
                Database::query("
                    INSERT INTO PREFIX_apps
                    SET app_id  = :app_id,
                        name    = :name,
                        list_name = :list_name,
                        description = :description,
                        category    = :category,
                        installed   = 1",
                    array(
                        'app_id'    => $app[$appname]['app_id'],
                        'name'      => $app[$appname]['name'][$this->config->get('language_id')],
                        'list_name' => $appname,
                        'description'=>$app[$appname]['description'][$this->config->get('language_id')],
                        'category'  => $app[$appname]['category']
                    )
                );
                $this->session->data['success'] = Language::getVar('SUMO_ADMIN_APP_INSTALLATION_SUCCESS');
            }
            catch (\Exception $e) {
                $this->session->data['warning'] = Language::getVar('SUMO_ADMIN_APP_INSTALLATION_ERROR', $e->getMessage());
            }
        }
        else {
            $this->session->data['warning'] = Language::getVar('SUMO_ADMIN_APP_INSTALLATION_ERROR', Language::getVar('SUMO_NOUN_FILE_NOT_FOUND'));
        }
        $this->redirect($this->url->link('common/apps/' . $category, '', 'SSL'));
    }

    public function deinstall()
    {
        $appname = isset($this->request->get['list_name']) ? preg_replace('/^[a-z]$/', '', $this->request->get['list_name']) : false;
        $category = isset($this->request->get['category']) ? $this->request->get['category'] : 'other';
        if (!$appname) {
            $this->redirect($this->url->link('common/apps/' . $category, '', 'SSL'));
        }

        if (file_exists(DIR_HOME . 'apps/' . $appname . '/model/setup.php')) {
            include(DIR_HOME . 'apps/' . $appname . '/model/setup.php');
            include(DIR_HOME . 'apps/' . $appname . '/information.php');
            $call = $appname . '\ModelSetup';
            $call = new $call($this->registry);

            try {
                $call->deinstall();
                Database::query("DELETE FROM PREFIX_apps_active WHERE app_id = (SELECT app_id FROM PREFIX_apps WHERE list_name = :name)", array('name' => $appname));
                Database::query("
                    DELETE FROM PREFIX_apps
                    WHERE list_name = :list_name",
                    array(
                        'list_name' => $appname,
                    )
                );
                $this->session->data['success'] = Language::getVar('SUMO_ADMIN_APP_DEINSTALLATION_SUCCESS');
            }
            catch (\Exception $e) {
                $this->session->data['warning'] = Language::getVar('SUMO_ADMIN_APP_DEINSTALLATION_ERROR', $e->getMessage());
            }
        }
        else {
            $this->session->data['warning'] = Language::getVar('SUMO_ADMIN_APP_DEINSTALLATION_ERROR', DIR_HOME . 'apps/' . $appname . '/model/setup.php?');
        }
        $this->redirect($this->url->link('common/apps/' . $category, '', 'SSL'));
    }

    private function action($type)
    {
        $this->data['action']   = $type;
        $this->data['stores']   = $this->model_settings_stores->getStores();
        $this->data['store_id'] = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;

        $installed = Database::fetchAll(
            "SELECT * FROM PREFIX_apps WHERE category = :cat AND installed = 1",
            array(
                'cat'   => $this->types[$type]
            )
        );
        foreach ($installed as $list) {
            $file = DIR_HOME . 'apps/' . $list['list_name'] . '/information.php';
            if (file_exists($file)) {
                include($file);
                if (isset($app[$list['name']]['hidden'])) {
                    continue;
                }
                $list['info'] = $app[$list['list_name']];
                $check = Database::query(
                    "SELECT active FROM PREFIX_apps_active WHERE app_id = (SELECT app_id FROM PREFIX_apps WHERE list_name = :name) AND store_id = :store",
                    array(
                        'name'  => $list['list_name'],
                        'store' => $this->data['store_id']
                    )
                )->fetch();
                $list['active'] = $check['active'];
                if (!$list['active']) {
                    $list = array_merge($list, $list['info']);
                    $this->data['available'][$list['list_name']] = $list;
                    continue;
                }
                $settings = DIR_HOME . 'apps/' . $list['list_name'] . '/model/settings.php';
                if (file_exists($settings)) {
                    include($settings);
                    $call = $list['list_name'] . '\ModelSettings';
                    $call = new $call($this->registry);
                    $call->setBaseTable('PREFIX_app_' . $list['list_name']);
                    $check = false;
                    try {
                        if (method_exists($call, 'checkSettings')) {
                            $check = $call->checkSettings($this->data['store_id']);
                            Logger::info($list['list_name'] . '\ModelSettings->checkSettings() == ' . var_export($check, true));
                        }
                        else {
                            Logger::warning($list['list_name'] . '\ModelSettings->checkSettings() does not exist!');
                            $check = true;
                        }
                    }
                    catch (\Exception $e) {
                        $check = false;
                        Logger::error($list['list_name'] . '\ModelSettings->checkSettings() generated an exception: ' . $e->getMessage());
                    }
                }
                else {
                    $check = true;
                }
                $list['checked'] = $check;
                $this->data['installed'][$list['list_name']] = $list;
            }
        }

        $available = glob(DIR_HOME . 'apps/*', GLOB_ONLYDIR);
        foreach ($available as $dir) {
            $tmp = explode('/', trim($dir, '/'));
            $name = end($tmp);
            if (isset($this->data['installed'][$name]) || isset($this->data['available'][$name])) {
                continue;
            }
            if (file_exists($dir . '/information.php')) {
                include ($dir . '/information.php');
            }
            else {
                $app[$name] = array(
                    'name'          => array(1 => 'Missing information.php'),
                    'version'       => '0.0',
                    'author'        => 'Unknown',
                    'description'   => array(1 => $name . ' has no information'),
                    'error'         => true
                );
                Logger::warning('Application "' . $name . '" has no information');
            }
            if (!isset($app[$name])) {
                Logger::error('Application "' . $name . '" has the wrong "key" identifier for the $app[] array. This must be the same as the directory name.');
                $this->data['available'][] = array(
                    'name'          => array(1 => $name),
                    'description'   => array(1 => 'Application "' . $name . '" has the wrong "key" identifier for the $app[] array. This must be the same as the directory name.'),
                    'error'         => true,
                );
            }
            else {
                $app[$name]['list_name'] = $name;
                if (!isset($app[$name]['category'])) {
                    $app[$name]['category'] = 99;
                }
                if (isset($app[$name]['hidden'])) {
                    Logger::info('Application "' . $name . '" (' . $app[$name]['name'][1] . ') is hidden');
                    continue;
                }
                if ($this->types[$type] == $app[$name]['category']) {
                    $this->data['available'][] = $app[$name];
                }
            }
        }

        $this->document->setTitle(Language::getVar('SUMO_ADMIN_APPS_' . strtoupper($type) . '_DASHBOARD'));
        $this->document->addBreadcrumbs(
            array(
                'text'  => Language::getVar('SUMO_ADMIN_APPS_DASHBOARD'),
                'href'   => $this->url->link('common/apps', '', 'SSL')
            )
        );
        $this->document->addBreadcrumbs(
            array(
                'text'  => Language::getVar('SUMO_ADMIN_APPS_' . strtoupper($type) . '_DASHBOARD')
            )
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        else {
            $this->data['success'] = '';
        }

        if (isset($this->session->data['warning'])) {
            $this->data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        else {
            $this->data['warning'] = '';
        }

        $this->template = 'common/apps.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }
}
