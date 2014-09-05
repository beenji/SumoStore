<?php
define('VERSION', '1.0.9');
if (!defined('LICENSE_KEY')) {
    header('Location: error/');
    header('X-Powered-By: SumoStore');
    header('X-Protected-By: SumoGuard');
    exit('_not_defined_license_key');
}
