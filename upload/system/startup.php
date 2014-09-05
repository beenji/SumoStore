<?php

//require DIR_HOME . 'license.php';
if (!defined('LICENSE_KEY') && !defined('INSTALLATION')) {
    //exit('Missing license.php file and/or definition.');
}

require DIR_HOME . 'version.php';
if (defined('DEVELOPMENT')) {
    Sumo\Logger::setLevel(10);
}

if (!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Amsterdam');
}
// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['PATH_TRANSLATED'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

    if (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Helper
require DIR_SYSTEM . 'helper/json.php';
require DIR_SYSTEM . 'helper/utf8.php';
require DIR_SYSTEM . 'helper/dompdf.php';

// Engine
require DIR_SYSTEM . 'engine/action.php';
require DIR_SYSTEM . 'engine/controller.php';
require DIR_SYSTEM . 'engine/front.php';
require DIR_SYSTEM . 'engine/loader.php';
require DIR_SYSTEM . 'library/database.php';
require DIR_SYSTEM . 'engine/model.php';
require DIR_SYSTEM . 'engine/registry.php';

if (!isset($unknown)) {
    Sumo\Logger::info('Engine is loaded');
}

// Common
require DIR_SYSTEM . 'library/cache.old.php';       # to be removed, legacy only
require DIR_SYSTEM . 'library/cache.php';
require DIR_SYSTEM . 'library/url.php';
require DIR_SYSTEM . 'library/config.php';
require DIR_SYSTEM . 'library/db.php';              # to be removed, legacy only
require DIR_SYSTEM . 'library/document.php';
require DIR_SYSTEM . 'library/encryption.php';
require DIR_SYSTEM . 'library/image.php';
require DIR_SYSTEM . 'library/language.old.php';    # to be removed, legacy only
require DIR_SYSTEM . 'library/language.php';
require DIR_SYSTEM . 'library/log.php';             # to be removed, legacy only
require DIR_SYSTEM . 'library/mail.php';
require DIR_SYSTEM . 'library/mailer.php';
require DIR_SYSTEM . 'library/pagination.php';
require DIR_SYSTEM . 'library/request.php';
require DIR_SYSTEM . 'library/response.php';
require DIR_SYSTEM . 'library/session.php';
require DIR_SYSTEM . 'library/template.php';
require DIR_SYSTEM . 'library/formatter.php';
require DIR_SYSTEM . 'library/apps.php';

if (!isset($unknown)) {
    Sumo\Logger::info('Common library is loaded');
}

// Application Classes
require DIR_SYSTEM . 'library/customer.php';
require DIR_SYSTEM . 'library/affiliate.php';
require DIR_SYSTEM . 'library/currency.php';
require DIR_SYSTEM . 'library/tax.php';             # to be removed, legacy only
require DIR_SYSTEM . 'library/weight.php';
require DIR_SYSTEM . 'library/length.php';
require DIR_SYSTEM . 'library/cart.php';

if (!isset($unknown)) {
    Sumo\Logger::info('Application library is loaded');
}

// Load security plugin
require DIR_SYSTEM . 'engine/communicator.php';
//if (!isset($unknown)) {
    //Sumo\Logger::info('SumoGuard Communicator loaded');
//}
//else {
    //Sumo\Logger::info('test');
//}
require DIR_SYSTEM . 'engine/security.php';

if (ini_get('register_globals')) {
    ini_set('session.use_cookies', 'On');
    ini_set('session.use_trans_sid', 'Off');

    session_set_cookie_params(0, '/');
    session_start();

    $globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

    foreach ($globals as $global) {
        foreach(array_keys($global) as $key) {
            unset(${$key});
        }
    }
}

// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {
    function clean($data) {
           if (is_array($data)) {
              foreach ($data as $key => $value) {
                $data[clean($key)] = clean($value);
              }
        } else {
              $data = stripslashes($data);
        }

        return $data;
    }

    $_GET = clean($_GET);
    $_POST = clean($_POST);
    $_COOKIE = clean($_COOKIE);
}

if (isset($_REQUEST)) {
    unset($_REQUEST);
}
