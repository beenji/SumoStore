<?php
/**
    @author     chris@sumostore.net
    @version    1.0.0
    @package    Sumo
    @description

                Determine the required environment; catalog or administration
*/

// If the request is for a resource, we don't need a lot
if (!empty($_GET['resource'])) {
    $file       = str_replace(array('../', '\\', './'), '', $_GET['resource']);
    $split      = explode('/', $_GET['resource']);
    $first      = $split[0];
    $last       = $split[count($split) - 1];
    $ext        = explode('.', $last);
    $ext        = end($ext);
    $ext        = mb_substr($last, mb_strrpos($last, '.') + 1);

    if (empty($ext) || !in_array($ext, array('png','jpg','jpeg','bmp','gif','woff','css','ico','js','eot','svg','ttf','woff','otf'))) {
        /** @todo Add SumoGuard warning **/
        exit('[warning] invalid file');
    }

    switch ($ext) {
        case 'css':
            header('Content-Type: text/css');
            break;

        case 'js':
            header('Content-Type: application/x-javascript');
            break;

        case 'png':
        case 'jpg':
        case 'jpeg':
        case 'bmp':
        case 'gif':
            header('Content-Type: image/' . $ext);
            break;
    }

    unset($split[0]);
    $file       = implode('/', $split);
    define('DIR_HOME',        str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');

    // Catalog?
    if ($first == 'view') {
        $file   = DIR_HOME . 'catalog/' . $first . '/' . $file;
        readfile($file);
    }
    // App?
    else if ($first == 'apps' || $first == 'app') {
        $file   = DIR_HOME . 'apps/' . $file;
        readfile($file);
    }
    // Admin!
    else {
        $file   = DIR_HOME . 'admin/' . $file;
        readfile($file);
    }
    exit;
}

$goToInstall = '<a href="install/?language=nl">Klik hier om door te gaan</a> - <a href="install/?language=en">Click to continue</a>';
if (file_exists('config.mysql.php')) {

    require 'config.mysql.php';

    if (!defined('DB_HOSTNAME') || !defined('DB_USERNAME') || !defined('DB_PASSWORD') || !defined('DB_DATABASE')) {
        exit($goToInstall);
    }

    define('DIR_HOME',        str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');
    define('DIR_SYSTEM',      DIR_HOME . 'system/');
    define('DIR_IMAGE',       DIR_HOME . 'image/');
    define('DIR_CACHE',       DIR_SYSTEM . 'cache/');
    define('DIR_DOWNLOAD',    DIR_HOME . 'download/');
    define('DIR_LOGS',        DIR_SYSTEM . 'logs/');

    // Startup
    require DIR_SYSTEM . 'engine/singleton.php';
    require DIR_SYSTEM . 'library/logger.php';
    Sumo\Logger::getInstance();

    require DIR_SYSTEM . 'engine/error.php';

    require DIR_SYSTEM . 'startup.php';

    define('ABS_START', microtime(true));

    // Registry
    $registry = new Registry();

    // Loader
    $loader = new Loader($registry);
    $registry->set('load', $loader);

    // Config
    $config = new Config();
    $registry->set('config', $config);

    // Database *legacy*
    //$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    Sumo\Database::setup(array(
        'hostname'  => DB_HOSTNAME,
        'username'  => DB_USERNAME,
        'password'  => DB_PASSWORD,
        'database'  => DB_DATABASE,
        'prefix'    => DB_PREFIX
    ));
    //$registry->set('db', Sumo);

    Sumo\Cache::disableCache(defined('NO_CACHE') ? true : false);

    $check = Sumo\Database::query("SELECT setting_value FROM PREFIX_settings WHERE setting_name = 'admin_directory'")->fetch();
    $stores = Sumo\Database::fetchAll("SELECT base_http, base_https FROM PREFIX_stores");
    $tmp = $_SERVER['REQUEST_URI'];
    foreach ($stores as $list) {
        foreach ($list as $key => $value) {
            $list[$key] = explode('/', trim($value, '/'));
            $list[$key] = end($list[$key]);
        }
        $tmp = trim(str_replace($list, '', $tmp), '/');
    }
    $tmp = explode('/', $tmp);
    $tmp = $tmp[0];
    if (!empty($_SERVER['HTTP_HOST']) && !empty($tmp) && !empty($check) && !isset($_GET['resource'])) {
        if ($tmp == $check['setting_value']) {
            define('ADMIN_ENABLED', true);
            require 'admin/index.php';
            exit();
        }
    }
}
else {
    exit($goToInstall);
}

define('CATALOG_ENABLED', true);
require 'catalog/index.php';
