<?php

use \Sumo;
// Configuration
if (!defined('DB_HOSTNAME')) {
    if (file_exists('../config.mysql.php')) {
        require_once '../config.mysql.php';
    }
    // Second check
    if (!defined('DB_HOSTNAME')) {
        header('Location: //' . $_SERVER['HTTP_HOST'] . '/install/index.php');
        exit;
    }
}

if (!defined('DIR_HOME')) {
    define('DIR_HOME',          str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')) . '/');
    define('DIR_SYSTEM',        DIR_HOME . 'system/');
    define('DIR_CONFIG',        DIR_SYSTEM . 'config/');
    define('DIR_IMAGE',         DIR_HOME . 'image/');
    define('DIR_CACHE',         DIR_SYSTEM . 'cache/');
    define('DIR_DOWNLOAD',      DIR_HOME . 'download/');
    define('DIR_LOGS',          DIR_SYSTEM . 'logs/');

    require DIR_SYSTEM . '/engine/singleton.php';
    require DIR_SYSTEM . '/library/logger.php';
    Sumo\Logger::getInstance();


    require_once(DIR_SYSTEM . 'engine/error.php');
    require_once(DIR_SYSTEM . 'startup.php');

    // Registry
    $registry = new Registry();
    Sumo\Logger::info('Registry created');

    // Loader
    $loader = new Loader($registry);
    $registry->set('load', $loader);
    Sumo\Logger::info('Loader created');

    // Config
    $config = new Config();
    $registry->set('config', $config);
    Sumo\Logger::info('Config created');

    // Database
    Sumo\Database::setup(array(
        'hostname'  => DB_HOSTNAME,
        'username'  => DB_USERNAME,
        'password'  => DB_PASSWORD,
        'database'  => DB_DATABASE,
        'prefix'    => DB_PREFIX
    ));
    Sumo\Logger::info('Database created');

    $check = Sumo\Database::query("SELECT setting_value FROM PREFIX_settings WHERE setting_name = 'admin_directory'")->fetch();

    $tmp = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $route = explode('/', trim($_GET['route'], '/'));
    foreach ($tmp as $key => $dir) {
        if (isset($route[$dir])) {
            unset($tmp[$key]);
        }
    }
    if (empty($tmp)) {
        $tmp[0] = 'admin';
    }
    ksort($tmp);
    if (!empty($_SERVER['HTTP_HOST']) && !empty($check)) {
        if (!empty($check) && $check['setting_value'] != 'admin' && $tmp[count($tmp) - 1] != $check['setting_value']) {
            define('ADMIN_REDIRECT', true);
        }
    }
}
else {
    // Database
    //$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    Sumo\Database::setup(array(
        'hostname'  => DB_HOSTNAME,
        'username'  => DB_USERNAME,
        'password'  => DB_PASSWORD,
        'database'  => DB_DATABASE,
        'prefix'    => DB_PREFIX
    ));
    //$registry->set('db', $db);
}

define('DIR_CATALOG',       DIR_HOME . 'catalog/');
define('DIR_APPLICATION',   DIR_HOME . 'admin/');
define('DIR_TEMPLATE',      DIR_APPLICATION . 'view/template/');

require_once(DIR_SYSTEM . 'library/user.php');

$settings = Sumo\Database::fetchAll("SELECT setting_name, setting_value, is_json FROM PREFIX_settings");
foreach ($settings as $list) {
    if ($list['is_json']) {
        $list['setting_value'] = json_decode($list['setting_value'], true);
    }
    $config->set($list['setting_name'], $list['setting_value']);
    // Legacy
    $config->set('config_' . $list['setting_name'], $list['setting_value']);
}
$title = Sumo\Database::query("SELECT name FROM PREFIX_stores LIMIT 1")->fetch();
$config->set('name', $title['name']);
$store = Sumo\Database::query("SELECT * FROM PREFIX_stores WHERE store_id = 0 LIMIT 1")->fetch();
if ($store['base_default'] == 'https') {
    $store['base_https'] = 'https://' . $store['base_https'];
    // Check for http vs https if https is enabled
    if (!isset($_SERVER['HTTPS']) || (!$_SERVER['HTTPS'] || strtolower($_SERVER['HTTPS']) == 'off')) {
        // Check if URL is SSL-enabled or not
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $store['base_https'] . trim($_SERVER['REQUEST_URI'], '/') . '/');
        exit;
    }
}
else {
    $store['base_https'] = 'http://' . $store['base_http'];
}
$config->set('base_default', $store['base_default']);
$config->set('base_http', $store['base_https']);
$config->set('base_https', $store['base_https']);
// Legacy
$config->set('config_url', $store['base_https']);
$config->set('config_ssl', $store['base_https']);

if (defined('ADMIN_REDIRECT')) {
    header('Location: ' . $config->get('base_http') . 'error/not_found');
    exit;
}

if (!defined('ADMIN_ENABLED')) {
    $config->set('admin_directory', 'admin');
}

Sumo\Cache::setStore('-1');

Sumo\Logger::info('Settings are set');

define('HTTP_SERVER',   $store['base_https'] . $config->get('admin_directory') . '/');
define('HTTPS_SERVER',  $store['base_https'] . $config->get('admin_directory') . '/');
define('HTTP_STYLE_BASE', $store['base_https']);

// Language, CLEANUP REQUIRED!!!
$languages = array();
foreach (Sumo\Database::fetchAll("SELECT * FROM PREFIX_language ORDER BY language_id") as $list) {
    $languages[$list['language_id']] = $list;
}
Sumo\Language::setup($languages[$config->get('language_id')]);

$lang = $languages[$config->get('language_id')];

setlocale(LC_TIME, '');
$locale = setlocale(LC_TIME, $lang['locale']);
$config->set('locale', $lang['locale']);
Sumo\Logger::info('Locale: ' . ($locale ? 'set' : 'failed'));
Sumo\Logger::info('Language created');

// Formatter
Sumo\Formatter::setup($config);

// Cache
Sumo\Cache::setStore('admin');

// Mail
Sumo\Mail::setup($config);
Sumo\Mailer::setup($config);

// Url
$url = new Url($store['base_https'] . $config->get('admin_directory') . '/', $store['base_https'] . $config->get('admin_directory') . '/');
$registry->set('url', $url);
Sumo\Logger::info('URL created');

// Request
if (empty($_GET['route']) && !empty($_GET['_route_'])) {
    $_GET['route'] = str_replace($config->get('admin_directory') . '/', '', $_GET['_route_']);
}
$request = new Request();
$registry->set('request', $request);
Sumo\Logger::info('Request created');

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->addHeader('X-Powered-By: SumoStore');
$response->addHeader('X-Protected-By: SumoGuard');
$registry->set('response', $response);
Sumo\Logger::info('Response created');

// Session
$session = new Session();
$registry->set('session', $session);
Sumo\Logger::info('Session created');

// Document
$registry->set('document', new Document($config));
Sumo\Logger::info('Document created');

// Currency
$registry->set('currency', new Sumo\Currency($registry));
Sumo\Logger::info('Currency created');

// Weight
$registry->set('weight', new Sumo\Weight($registry));
Sumo\Logger::info('Weight created');

// Length
$registry->set('length', new Sumo\Length($registry));
Sumo\Logger::info('Lenght created');

// User
$registry->set('user', new User($registry));
Sumo\Logger::info('User created');

// Front Controller
$controller = new Front($registry);
Sumo\Logger::info('Front created');

// Login
$controller->addPreAction(new Sumo\Action('common/home/login'));
Sumo\Logger::info('Front pre-action common/home/login added');

// Permission
$controller->addPreAction(new Sumo\Action('common/home/permission'));
Sumo\Logger::info('Front pre-action common/home/permission added');

// Router
if (isset($request->get['route'])) {
    $route = strtolower($request->get['route']);
    $action = new Sumo\Action($route);
    Sumo\Logger::info('Action created with route ' . htmlentities($route));
}
else {
    $action = new Sumo\Action('common/home');
    Sumo\Logger::info('Action created with route common/home');
}

// Dispatch
$controller->dispatch($action, new Sumo\Action('error/not_found'));
Sumo\Logger::info('Dispatching action with fallback error/not_found');

// Output
$output = $response->output();
