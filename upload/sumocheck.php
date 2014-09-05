<?php
// SumoCheck v1.0.0
echo '<html><head><title>SumoCheck</title></head><body>';
$errors = array();
if(version_compare(phpversion(), '5.3.0', '<')) {
    $errors[] = 'Warning: You need to use PHP5 or above for SumoStore to work!';
}

if (!ini_get('file_uploads')) {
    $errors[] = 'Warning: file_uploads needs to be enabled!';
}

if (ini_get('session.auto_start')) {
    $errors[] = 'Warning: SumoStore will not work with session.auto_start enabled!';
}

if (!extension_loaded('pdo_mysql') || !extension_loaded('pdo')) {
    $errors[] = 'Warning: PDO::MySQL extension needs to be loaded for SumoStore to work!';
}

if (!extension_loaded('gd')) {
    $errors[] = 'Warning: GD extension needs to be loaded for SumoStore to work!';
}

if (!extension_loaded('curl')) {
    $errors[] = 'Warning: CURL extension needs to be loaded for SumoStore to work!';
}

if (!function_exists('mcrypt_encrypt')) {
    $errors[] = 'Warning: mCrypt extension needs to be loaded for SumoStore to work!';
}

if (!extension_loaded('zlib')) {
    $errors[] = 'Warning: ZLIB extension needs to be loaded for SumoStore to work!';
}

if (count($errors)) {
    echo '<strong>This server does not fit the requirements to run SumoStore</strong><br /><ul>';
    foreach ($errors as $error) {
        echo '<li><span style="color: red;">' . $error . '</span></li>';
    }
    echo '</ul>';
}
else {
    echo '<strong>Hooray! Go install SumoStore!</strong>';
}
echo '</body></html>';
