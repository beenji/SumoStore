<?php
header('Content-Type: text/css');
header('Cache-Control: public');
header('X-Powered-By: SumoStore');
header('X-Protected-By: SumoGuard');

$lastModified = filemtime('general.css');
if (isset($_GET['lastmodified']) && is_numeric($_GET['lastmodified']) && (int) $_GET['lastmodified'] > $lastModified) {
    $lastModified = (int) $_GET['lastmodified'];
}
$etag = md5_file('general.css');
$etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
header('Etag: ' . $etag);

// Check if page has changed. If not, send 304 and exit
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified || $etagHeader == $etagFile) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
}
$start = microtime(true);

$css = file_get_contents('general.css');

// Check what styles are required
if (!empty($_GET['theme'])) {
    $theme = preg_replace('/\B/', '', $_GET['theme']);

    if (!empty($_GET['files'])) {
        $files = preg_replace('/[^a-z.,$]/', '', $_GET['files']);
        if (!empty($files)) {
            $files = explode(',', $files);
            foreach ($files as $file) {
                $theFile = '../theme/' . $theme . '/css/' . $file;
                if (file_exists($theFile)) {
                    $css .= 'PHP_EOL/= Extra stylesheet: ' . $file . ' =/PHP_EOL';
                    $css .= file_get_contents($theFile);
                } else {
                    $css .= 'PHP_EOL/= Extra stylesheet: ' . $file . ' was not found =/PHP_EOL';
                }
            }
        } else {
            $css .= 'PHP_EOL/= Files were given, but are incorrect? =/PHP_EOL';
        }
    }
}

// Check if there's custom styles that need to be loaded
if (isset($_GET['store_id'])) {
    $id = preg_replace('/\D/', '', $_GET['store_id']);
    if (!empty($id) || $id == 0) {
        include('../../../config.mysql.php');
        include('../../../system/engine/singleton.php');
        include('../../../system/library/database.php');

        try {
            Sumo\Database::setup(array(
                'hostname'  => DB_HOSTNAME,
                'username'  => DB_USERNAME,
                'password'  => DB_PASSWORD,
                'database'  => DB_DATABASE,
                'prefix'    => DB_PREFIX
            ));
            $theme = Sumo\Database::query("SELECT setting_value AS template FROM PREFIX_settings_stores WHERE store_id = :id AND setting_name = 'template'", array('id' => $id))->fetch();
            if (count($theme) && !empty($theme['template'])) {
                $check = Sumo\Database::query("SELECT setting_value FROM PREFIX_settings_stores WHERE setting_name = :name AND store_id = :id", array('id' => $id, 'name' => 'colors_' . $theme['template']))->fetch();
                if (count($check) && !empty($check['setting_value'])) {
                    $css .= 'PHP_EOL/= Specific colors, user generated =/PHP_EOL';
                    $colors = json_decode($check['setting_value']);
                    $body = array();
                    foreach ($colors as $name => $color) {
                        if (empty($color)) {
                            continue;
                        }
                        switch ($name) {
                            case 'body_background':
                                $body['background-color'] = $color;
                                break;

                            case 'body_background_image':
                                $body['background-image'] = 'url(/image/' . $color . ')';
                                break;

                            case 'body_background_repeat':
                                $body['background-repeat'] = $color;
                                break;

                            case 'top_bar_background':
                                $css .= '#top-line { background-color: ' . $color . '; }';
                                break;

                            case 'header_background':
                                $css .= '.header-wrapper { background-color: ' . $color . '; }';
                                break;

                            case 'content_background':
                                $css .= '.content-container, .content-container .container { background-color: ' . $color . '; }';
                                break;

                            case 'footer_background':
                                $css .= '.footer-container { background-color: ' . $color . '; }';
                                break;
                                
                            case 'pricebox_background':
                                $css .= '.product-box:hover { background-color: ' . $color . '; }';
                                break;

                            case 'text_color':
                                $body['color'] = $color;
                                break;

                            case 'link_color':
                                $css .= 'a { color: ' . $color . '; }';
                                break;

                            case 'link_hover_color':
                                $css .= 'a:hover { color: ' . $color . '; }';
                                break;

                            case 'menu_background':
                                $css .= '#header-menu { background-color: ' . $color . '; }';
                                break;

                            case 'menu_hover_background':
                                $css .= '#header-menu > li > a:hover { background-color: ' . $color . '; }';
                                break;

                            case 'menu_dropdown_background':
                                $css .= '#header-menu > li > ul { background-color: ' . $color . '; }';
                                break;

                            case 'menu_dropdown_hover_background':
                                $css .= '#header-menu > li > ul > li > a:hover { background-color: ' . $color . '; }';
                                break;

                            case 'menu_link_color':
                                $css .= '#header-menu > li > a { color: ' . $color . '; }';
                                break;

                            case 'menu_link_hover_color':
                                $css .= '#header-menu > li > a:hover { color: ' . $color . '; }';
                                break;

                            case 'menu_dropdown_link_color':
                                $css .= '#header-menu > li > ul > li > a { color: ' . $color . '; }';
                                break;

                            case 'menu_dropdown_link_hover_color':
                                $css .= '#header-menu > li > ul > li > a:hover { color: ' . $color . '; }';
                                break;

                            case 'button_primary':
                                $css .= '.btn-primary, .btn-primary:hover { background-image: none; background-color: ' . $color . '; box-shadow: none; border-color: ' . $color . '; }';
                                break;

                            case 'button_secondary':
                                $css .= '.btn-secondary, .btn-secondary:hover { background-image: none; background-color: ' . $color . '; box-shadow: none; border-color: ' . $color . '; }';
                                break;

                            case 'button_order':
                                $css .= '.btn-order, .btn-order:hover { background-image: none; background-color: ' . $color . '; box-shadow: none; border-color: ' . $color . '; }';
                                break;

                            case 'price_color':
                            case 'old_price_color':
                            case 'new_price_color':
                            case 'tax_price_color':
                            case 'sale_price_color':
                                $css .= '.' . str_replace(array('_color', '_'), array('', '-'), $name) . ' { color: ' . $color . '; }';
                                break;

                            case 'pricebox_text_color':
                                $css .= '.pricebox { color: ' . $color . '; }';
                                break;
                                
                            case 'footer_link_color':
                                $css .= 'ul.footer-links a { color: ' . $color . '; }';
                                break;

                            case 'footer_link_hover_color':
                                $css .= 'ul.footer-links a:hover { color: ' . $color . '; }';
                                break;

                            case 'footer_text_color':
                                $css .= '.footer-content, .footer-content p { color: ' . $color . '; }';
                                break;

                            case 'sidebar_header_background_color':
                                $css .= '.content-container .sidebar .header h3 { background-color: ' . $color . '; }';
                                break;

                            case 'sidebar_header_text_color':
                                $css .= '.content-container .sidebar .header h3 { color: ' . $color . '; }';
                                break;

                            case 'sidebar_box_background_color':
                                $css .= '.content-container .sidebar .content { background-color: ' . $color . '; }';
                                break;

                            case 'sidebar_box_link_color':
                                $css .= '.content-container .sidebar .content a { color: ' . $color . '; }';
                                break;

                            case 'sidebar_box_link_hover_color':
                                $css .= '.content-container .sidebar .content a:hover { color: ' . $color . '; }';
                                break;

                            default:
                                $css .= '.' . $name . ' { color: ' . $color . '; }';
                                break;
                        }
                    }

                    if (is_array($body)) {
                        $css .= 'body {';
                        if (isset($body['background-image'])) {
                            $css .= 'background: ' . $body['background-image'] . ' ' . $body['background-repeat'] . ' ' . $body['background-color'] . ';';
                        }
                        if (isset($body['color'])) {
                            $css .= 'color: ' . $body['color'] . ';';
                        }
                        $css .= '}';
                    }

                }
            } else {
                $css .= 'PHP_EOL/= Specific colors could not be found.. =/PHP_EOL';
            }

            $check = Sumo\Database::query("SELECT setting_value FROM PREFIX_settings_stores WHERE setting_name = :template AND store_id = :id", array('id' => $id, 'template' => 'stylesheet_' . $theme['template']))->fetch();
            if (count($check) && !empty($check['setting_value'])) {
                $css .= 'PHP_EOL/= Extra stylesheet, user generated =/PHP_EOL';
                $css .= $check['setting_value'];
            }
        } catch (\Exception $e) {
            $css .= 'PHP_EOL/= Could not load extra CSS =/';
        }
    }
}

$css = preg_replace('#\s+#', ' ', $css);
$css = preg_replace('#/\*.*?\*/#s', '', $css);
$css = preg_replace('#/=(.*?)=/#s', '/* \1 */', $css);
$css = str_replace('PHP_EOL', PHP_EOL, $css);
$css = str_replace('; ', ';', $css);
$css = str_replace(': ', ':', $css);
$css = str_replace(' {', '{', $css);
$css = str_replace('{ ', '{', $css);
$css = str_replace(' >', '>', $css);
$css = str_replace('> ', '>', $css);
$css = str_replace(' , ', ',', $css);
$css = str_replace('} ', '}', $css);
$css = str_replace(';}', '}', $css);

echo '/* Combined, stripped and cleaned stylesheet generated on ', date('d-m-Y H:i:s'), ' in ', round(microtime(true) - $start, 8), ' seconds */', PHP_EOL, trim($css);
