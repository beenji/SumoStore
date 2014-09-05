<?php

if (!defined('CATALOG_ENABLED')) {
    // SumoGuard notification
    header('Location: ../');
    exit;
}

define('DIR_APPLICATION',   DIR_HOME . 'catalog/');
define('DIR_TEMPLATE',      DIR_APPLICATION . 'view/theme/');

// Which store is this?
if (!empty($_SERVER['HTTP_HOST'])) {

    $url = rtrim($_SERVER['HTTP_HOST'] . '/' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'), '/') . '%';
    $store = Sumo\Database::query(
        "SELECT *
        FROM PREFIX_stores
        WHERE base_http     LIKE :url1
            OR base_https   LIKE :url2
            OR REPLACE(base_http, 'www.', '')    LIKE :url3
            OR REPLACE(base_https, '', '')   LIKE :url4
        LIMIT 1",
        array(
            'url1'  => $url,
            'url2'  => $url,
            'url3'  => str_replace('www.', '', $url),
            'url4'  => str_replace('www.', '', $url)
        )
    )->fetch();
}

if (empty($store) || !count($store)) {
    $store = Sumo\Database::query("SELECT * FROM PREFIX_stores WHERE store_id = 0 LIMIT 1")->fetch();
}

$config->set('store_id', $store['store_id']);
$config->set('store_data', $store);
$config->set('name', $store['name']);
$config->set('base_default', $store['base_default']);
$config->set('base_http', $store['base_http']);
$config->set('base_https', $store['base_https']);

// Update cache class with store
Sumo\Cache::setStore($store['store_id']);

// Get default settings
foreach (Sumo\Database::fetchAll("SELECT setting_name, setting_value, is_json FROM PREFIX_settings") as $list) {
    $config->set($list['setting_name'], $list['is_json'] ? json_decode($list['setting_value'], true) : $list['setting_value']);
}

// Overrule with store settings
foreach (Sumo\Database::fetchAll("SELECT setting_name, setting_value, is_json FROM PREFIX_settings_stores WHERE store_id = :id", array('id' => $store['store_id'])) as $list) {
    $default    = $config->get($list['setting_name']);
    $value      = $list['is_json'] ? json_decode($list['setting_value'], true) : $list['setting_value'];
    $type       = explode('_', $list['setting_name']);
    if ($type[0] == 'image' && empty($value)) {
        continue;
    }
    if ($default != $value) {
        $config->set($list['setting_name'], $value);
    }
}

$checkTemplate = $config->get('template');
if (empty($checkTemplate)) {
    $config->set('template', 'base');
}

// Define URL's for the system
$url = new Url('http://' . $store['base_http'], 'http://' . $store['base_http']);
if (!empty($store['base_https']) && $store['base_default'] == 'https') {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on')) {
        // Check if URL is SSL-enabled or not
        $parts = explode('/', $_GET['_route_']);
        if ($_GET['_route_'] == 'common/pc' || $_GET['_route_'] == 'product/search/ajax') {
            // ignore
        }
        else
        if (in_array($parts[0], array('account', 'checkout', 'affiliate', 'app', 'information'))) {
            $url = new Url('https://' . $store['base_https'], 'https://' . $store['base_https']);
        }
        else {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: http://' . $store['base_http'] . trim($_SERVER['REQUEST_URI'], '/'));
            exit;
        }
    }
    else if ($store['base_default'] == 'https') {
        $url = new Url('http://' . $store['base_http'], 'https://' . $store['base_https']);
    }
}
$registry->set('url', $url);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->addHeader('X-Powered-By: SumoStore');
$response->addHeader('X-Protected-By: SumoGuard');
$registry->set('response', $response);

// Session
$session = new Session();
$registry->set('session', $session);

// Language
$languages = array();
foreach (Sumo\Database::fetchAll("SELECT * FROM PREFIX_language ORDER BY language_id") as $list) {
    $languages[$list['language_id']] = $list;
}
Sumo\Language::setup($languages[$config->get('language_id')]);
$lang = $languages[$config->get('language_id')];
setlocale(LC_TIME, '');
$locale = setlocale(LC_TIME, $lang['locale']);
$config->set('locale', $lang['locale']);
// Formatter
Sumo\Formatter::setup($config);
Sumo\Mailer::setup($config);
Sumo\Mail::setup($config);

/*
// Language Detection
$languages = Sumo\Cache::find('languages');
if (!is_array($languages) || !count($languages)) {
    $data = Sumo\Database::fetchAll("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1'");

    foreach ($data as $result) {
        $languages[$result['code']] = $result;
    }
    Sumo\Cache::set('languages', $languages);
}
$detect = '';

if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
    $browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

    foreach ($browser_languages as $browser_language) {
        foreach ($languages as $key => $value) {
            if ($value['status']) {
                $locale = explode(',', $value['locale']);

                if (in_array($browser_language, $locale)) {
                    $detect = $key;
                }
            }
        }
    }
}

if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages) && $languages[$session->data['language']]['status']) {
    $code = $session->data['language'];
} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages) && $languages[$request->cookie['language']]['status']) {
    $code = $request->cookie['language'];
} elseif ($detect) {
    $code = $detect;
} else {
    $code = $config->get('config_language');
}

if (!isset($session->data['language']) || $session->data['language'] != $code) {
    $session->data['language'] = $code;
}

if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
    setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
}

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language *legacy*
$language = new LanguageOld($languages[$code]['directory']);
$language->load($languages[$code]['filename']);
$registry->set('language', $language);

// Language
Sumo\Language::setup($languages[$code]);
*/

// Document
$registry->set('document', new Document());

// Customer
$registry->set('customer', new Customer($registry));

// Currency
$registry->set('currency', new Sumo\Currency($registry));

// Tax
$registry->set('tax', new Sumo\Tax($registry));

// Weight
$registry->set('weight', new Sumo\Weight($registry));

// Length
$registry->set('length', new Sumo\Length($registry));

// Cart
$registry->set('cart', new Cart($registry));

// Encryption
$registry->set('encryption', new Encryption($config->get('encryption')));

// Front Controller
$controller = new Front($registry);

// SEO URL's
$controller->addPreAction(new Sumo\Action('common/seo_url'));

// Router
if (!isset($request->get['route']) && isset($request->get['_route_'])) {
    $request->get['route'] = $request->get['_route_'];
}

// Maintenance Mode
$controller->addPreAction(new Sumo\Action('common/maintenance/index'));

if (isset($request->get['route'])) {
    $action = new Sumo\Action(strtolower($request->get['route']));
}
else {
    $action = new Sumo\Action('common/home');
}

// Dispatch
$controller->dispatch($action, new Sumo\Action('error/not_found'));

// Output
$output = $response->output(true);
//echo preg_replace( '#\s+#', ' ', $output );
//exit;
echo str_replace('</body>', '</body><!-- ' . round(microtime(true) - ABS_START, 8) . ' seconds to generate -->', $output);
