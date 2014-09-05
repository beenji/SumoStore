<?php
// Error Reporting enabled for installation, to check if any errors occur
if (isset($_GET['debug']) || isset($_GET['error']) || isset($_GET['errors']) || isset($_GET['show_errors'])) {
    error_reporting(E_ALL);
}

// Domain
define('HTTP_DEFAULT', !isset($_SERVER['HTTPS']) || (!$_SERVER['HTTPS'] || strtolower($_SERVER['HTTPS']) == 'off') ? 'http' : 'https');
define('HTTP_HOST', $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/.\\') . '/');
define('HTTP_SERVER', HTTP_DEFAULT . '://' . HTTP_HOST);
define('HTTP_SUMOSTORE', HTTP_DEFAULT . '://' . HTTP_HOST . '/');

// Directories
define('DIR_APPLICATION', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_SYSTEM', str_replace('\'', '/', realpath(dirname(__FILE__) . '/../')) . '/system/');
define('DIR_SUMOSTORE', str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/');
define('DIR_HOME', DIR_SUMOSTORE);
define('DIR_CACHE', DIR_SYSTEM . 'cache/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('INSTALLATION', true);

// Requirements
require DIR_SYSTEM . '/engine/singleton.php';
require DIR_SYSTEM . '/library/logger.php';
Sumo\Logger::getInstance();


require_once(DIR_SYSTEM . 'engine/error.php');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=UTF-8');
$response->addHeader('X-Powered-By: SumoStore');
$response->addHeader('X-Protected-By: SumoGuard');
$registry->set('response', $response);

// Document
$document = new Document();
$registry->set('document', $document);

// "Configuration"
$config = new Config();
$registry->set('config', $config);

// Session
$session = new Session();
$registry->set('session', $session);

// Language
if (empty($session->data['language'])) {
    $session->data['language'] = 'nl';
}

if (!empty($request->get['language'])) {
    $language = preg_replace('~[^a-z]~', '', strtolower($request->get['language']));
    if (file_exists(DIR_LANGUAGE . $language . '.php')) {
        $session->data['language'] = $language;
    }
}

include_once(DIR_LANGUAGE . $session->data['language'] . '.php');
foreach ($language as $key => $translation) {
    $config->set('LANG_' . strtoupper($key), $translation);
}

// Front Controller
$controller = new Front($registry);

// Router
if (isset($request->get['route'])) {
    $action = new Sumo\Action($request->get['route']);
}
else if (isset($request->get['_route_'])) {
    $action = new Sumo\Action($request->get['_route_']);
}
else {
    $action = new Sumo\Action('step_1');
}

// Dispatch
$controller->dispatch($action, new Sumo\Action('step_1'));

// Output
$response->output();
