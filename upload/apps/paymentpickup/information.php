<?php
// SumoStore APP: paymentpickup
// Information.php generated on 14-07-2014
$app['paymentpickup'] = array(
    // Display name in backend
    'name'          => array(
        1 => 'Betalen bij afhalen',
        2 => 'Pay on pickup'
    ),
    // Author
    'author'        => 'SumoStore',
    // Author's homepage
    'url'           => 'https://www.sumostore.net/',
    // Unique APP ID, will be automated when uploaded via the AppStore
    // For now, make sure it's unique for your installation
    'app_id'        => '100000026',
    // Description for frontend
    'description'   => array(
        1 => 'De bestelling wordt betaald in de winkel',
        2 => 'The payment will be completed in the store'
    ),
    // App version
    'version'       => '1.0.0',
    // Minimum SumoStore version
    'sumo_version'  => '1.0',
    // Category of the app
    'category'      => 2,
    // Logo for the app
    // If not local, make sure you format the URL correct:
    // FAIL:    http://author-domain.net/my-app/logo.png
    // CORRECT: //author-domain.net/my-app/logo.png
    // This prevents SSL-errors
    'logo'          => '/apps/paymentpickup/logo.png'
);
