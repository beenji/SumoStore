<?php
class ControllerDesignSumobuilder extends Controller
{
    private $error = array();

    public function index()
    {
        $breadcrumb = array(
            'text'      => Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_TITLE'),
            'href'      => $this->url->link('design/sumobuilder', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $this->document->setBreadcrumbs($breadcrumb);
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_TITLE'));
        $this->document->addStyle('css/lib/sumobuilder.css');
        $this->document->addStyle('css/lib/jquery-ui-bootstrap.css');

        $this->load->model('design/sumobuilder');

        $this->data['themes'] = $this->model_design_sumobuilder->getThemes();
        $this->data['token'] = $this->session->data['token'];

        $this->template = 'design/sumobuilder.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );
        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        $this->load->model('design/sumobuilder');
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['action'])) {
            $theme_id   = isset($this->request->post['theme_id']) ? $this->request->post['theme_id'] : 1;
            $response   = array();

            switch ($this->request->post['action']) {
                case 'pages':
                    $this->data['settings']     = $this->model_design_sumobuilder->getTheme($theme_id);
                    if (isset($this->session->data['builder_' . $theme_id])) {
                        foreach ($this->session->data['builder_' . $theme_id] as $key => $value) {
                            $this->data['settings'][$key] = $value;
                        }
                    }

                    $this->load->model('localisation/language');
                    $this->data['languages']    = $this->model_localisation_language->getLanguages();

                    $this->load->model('catalog/information');
                    $pages                      = $this->model_catalog_information->getInformations();

                    $this->load->model('setting/store');
                    $stores                     = $this->model_settings_stores->getStores();

                    $this->data['stores'][0]    = $this->config->get('name');
                    foreach ($stores as $list) {
                        $this->data['stores'][$list['store_id']] = $list['name'];
                    }

                    foreach ($pages as $list) {
                        $list['title'] = $this->data['stores'][$list['store_id']] . ': ' . $list['title'];
                        $this->data['pages'][]  = $list;
                    }

                    $this->data['icons']        = $this->model_design_sumobuilder->getIcons(true);

                    $this->template = 'design/sumobuilder/pages.tpl';
                    return $this->response->setOutput($this->render());
                    break;

                case 'backgrounds':
                    // RC3/V1
                    break;

                case 'colors':
                    $this->data['settings']     = $this->model_design_sumobuilder->getTheme($theme_id);
                    if (isset($this->session->data['builder_' . $theme_id])) {
                        foreach ($this->session->data['builder_' . $theme_id] as $key => $value) {
                            $this->data['settings'][$key] = $value;
                        }
                    }
                    $this->template = 'design/sumobuilder/colors.tpl';
                    return $this->response->setOutput($this->render());
                    break;

                case 'fonts':
                    $this->data['settings']     = $this->model_design_sumobuilder->getTheme($theme_id);
                    if (isset($this->session->data['builder_' . $theme_id])) {
                        foreach ($this->session->data['builder_' . $theme_id] as $key => $value) {
                            $this->data['settings'][$key] = $value;
                        }
                    }
                    $this->data['fonts']        = $this->model_design_sumobuilder->getFonts();
                    $this->template = 'design/sumobuilder/fonts.tpl';
                    return $this->response->setOutput($this->render());
                    break;

                case 'custom':
                    $this->data['settings']     = $this->model_design_sumobuilder->getTheme($theme_id);
                    if (isset($this->session->data['builder_' . $theme_id])) {
                        foreach ($this->session->data['builder_' . $theme_id] as $key => $value) {
                            $this->data['settings'][$key] = $value;
                        }
                    }
                    $this->template = 'design/sumobuilder/custom.tpl';
                    return $this->response->setOutput($this->render());
                    break;

                case 'save':
                    $this->data['token'] = $this->session->data['token'];
                    $this->template = 'design/sumobuilder/save.tpl';
                    return $this->response->setOutput($this->render());
                    break;

                case 'save_as':
                    $new_theme_id = $this->model_design_sumobuilder->addTheme($this->request->post['name']);
                    if (!$new_theme_id) {
                        $response['result'] = Language::getVar('SUMO_ADMIN_DESIGN_SUMOBUILDER_SAVE_ERROR_NAME_IN_USE');
                    }
                    else {
                        $this->session->data['builder_' . $theme_id]['lastmodified'] = time();
                        $this->session->data['builder_' . $theme_id]['theme_id'] = $new_theme_id;
                        $this->model_design_sumobuilder->saveTheme($new_theme_id, $this->session->data['builder_' . $theme_id]);
                        $this->session->data['builder_' . $theme_id] = $this->model_design_sumobuilder->getTheme($theme_id);
                        $this->session->data['builder_' . $new_theme_id] = $this->model_design_sumobuilder->getTheme($new_theme_id);
                        $this->createStylesheet($this->session->data['builder_' . $new_theme_id], $new_theme_id);
                        $response['result'] = 'OK';
                        Cache::remove('builder', true);
                    }
                    break;

                case 'absolutesave':
                    $this->session->data['builder_' . $theme_id]['lastmodified'] = time();
                    $this->session->data['builder_' . $theme_id]['theme_id'] = $theme_id;
                    $this->model_design_sumobuilder->saveTheme($theme_id, $this->session->data['builder_' . $theme_id]);
                    $this->session->data['builder_' . $theme_id] = $this->model_design_sumobuilder->getTheme($theme_id);
                    $this->createStylesheet($this->session->data['builder_' . $theme_id], $theme_id);
                    $response['saved'] = 'true';
                    Cache::remove('builder', true);
                    break;

                case 'midsave':
                    if (isset($this->request->post['data'])) {
                        $tmp = array();
                        parse_str(str_replace('amp;', '', $this->request->post['data']), $tmp);
                        $response['data'] = $tmp;
                        if (!isset($this->session->data['builder_' . $theme_id])) {
                            $this->session->data['builder_' . $theme_id] = array();
                        }
                        $tmp = $this->cleanOut($tmp);
                        foreach ($tmp as $key => $values) {
                            $this->session->data['builder_' . $theme_id][$key] = $values;
                        }
                        $response['saved'] = true;
                    }
                    break;

                case 'delete':
                    if ($theme_id <= 1) {
                        $response['removed'] = false;
                        $response['result'] = Language::getVaR('SUMO_ADMIN_DESIGN_BUILDER_REMOVE_DEFAULT');
                    }
                    else {
                        $result = $this->model_design_sumobuilder->removeTheme($theme_id);
                        if (!$result) {
                            $response['removed'] = false;
                            $response['result'] = Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_REMOVE_FAILED');
                        }
                        else {
                            $response['removed'] = true;
                            $response['result'] = Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_REMOVE_SUCCESSFULL');
                        }
                    }
                    break;
            }

            $this->response->setOutput(json_encode($response));
        }
    }

    private function cleanOut($input)
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->cleanOut($value);
            }
        }
        else {
            $input = stripslashes($input);
        }
        return $input;
    }

    private function createStylesheet($settings, $theme_id)
    {
        $css = '/* Stylesheet generated on ' . date('d-m-Y H:i:s', $settings['lastmodified']) . ' */' . PHP_EOL;

        $shadows = array(
            'box-shadow' => '0 1px 3px rgba(0,0,0,0.2)',
            '-webkit-box-shadow' => '0 1px 3px rgba(0,0,0,0.2)',
            '-moz-box-shadow' => '0 1px 3px rgba(0,0,0,0.2)'
        );

        $rules = array(
            // body style
            'body' => array(
                'background-color' => $settings['colors']['body_background_color'] . ' !important'
            ),
            // headings
            'h1,h2,h3,h4,h5,h6' => array(
                'color' => $settings['colors']['headings']['color'],
                'border' => $settings['colors']['headings']['border_enabled'] ? $settings['colors']['headings']['border']['width'].'px ' . $settings['colors']['headings']['border']['style'] . ' ' . $settings['colors']['headings']['border']['color'] : 'none'
            ),
            // body text color
            'body,.cart-info thead td,.checkout-product thead td, table tbody tr td,.wishlist-info thead td,.sitemap-info ul li ul li,.sitemap-info ul li ul li a,.product-grid .name a,#content .box-product .name a,.product-list .name a,.product-info .wishlist-compare-friend a,.product-bottom-related .name a,.product-box-slide .name a,.product-right-sm-info span.p_title a,.box-category-home .name a,.product-right-sm-info span.p_title a,.box-category-home .subcat a,.product-compare a,.product-info .review > div a,.mini-cart-info .name a,.mini-cart-info td,.mini-cart-total td' => array(
                'color' => $settings['colors']['body_text_color']
            ),
            // body light text color
            '.heading h5,.product_box_brand span,.product_box_brand a,.product-description-l,.product-description-l span,.product-description-l a,ul.breadcrumbs li:before,.product-info .cart .minimum,.product-info .you-save,.product-right-sm-info span.p_subtitle,.articleHeader span,.updateInfo,.commentList li .created,#comments .pagination,#commentRespond .note,.blogModule .info' => array(
                'color' => $settings['colors']['body_light_color'],
            ),
            // links color
            'a, .box ul li, #product-top .product-description .product-description-l span.stock, .commentList li .name, .commentList li .name a' => array(
                'color' => $settings['colors']['link_normal_color']
            ),
            // breadcrumbs
            '.breadcrumbs li a' => array(
                'color' => $settings['colors']['breadcrumbs']['text_color']
            ),
            '.breadcrumbs li a:hover' => array(
                'color' => $settings['colors']['breadcrumbs']['text_hover_color']
            ),
            // backgrounds
            '#menu_contacts .mc:hover span.mm_icon, .product-info .cart .dec:hover, .product-info .cart .inc:hover, .es-nav span:hover, .product-related .bx-wrapper div.bx-next:hover, .product-related .bx-wrapper div.bx-prev:hover, #toTopHover, .product-right-sm-info span.p_icon, #livesearch_search_results li:hover, #livesearch_search_results .highlighted, #swipebox-action, .top-bar ul > li a:hover' => array(
                'background-color' => $settings['colors']['link_normal_color']
            ),
            // links hover color
            'a:hover, .product-info .review > div a:hover, .sitemap-info ul li ul li:hover, .sitemap-info ul li ul li a:hover, .htabs a:hover, #header #cart:hover .heading a div#cart-total, .product-grid .name a:hover, #content .box-product .name a:hover, .product-list .name a:hover, .product-info .wishlist-compare-friend a:hover, .product-bottom-related .name a:hover, .product-right-sm-info span.p_title a:hover, .box-category-home .subcat a:hover, .product-info .save-percent ' => array(
                'color' => $settings['colors']['link_hover_color']
            ),
            '.product-right-sm-info .product-right-sm-info-content:hover span.p_icon, .camera_wrap .camera_pag .camera_pag_ul li:hover > span, .flex-control-paging li a:hover, #swipebox-action:hover, .tp-bullets.simplebullets.round .bullet:hover' => array(
                'background-color' => $settings['colors']['link_hover_color']
            ),
            // main column
            '.wrapper' => array(
                'background-color' => $settings['colors']['main_column']['background_enabled'] ? $settings['colors']['main_column']['background_color'] : 'inherit',
                'border' => $settings['colors']['main_column']['border_enabled'] ? $settings['colors']['main_column']['border']['width'].'px ' . $settings['colors']['main_column']['border']['style'] . ' ' . $settings['colors']['main_column']['border']['color'] : 'none',
            ),
        );

        // Main column shadow
        if ($settings['colors']['main_column']['shadow']) {
            $rules['.wrapper']['box-shadow'] = '0 1px 3px rgba(0,0,0,0.2)';
            $rules['.wrapper']['-webkit-box-shadow'] = '0 1px 3px rgba(0,0,0,0.2)';
            $rules['.wrapper']['-moz-box-shadow'] = '0 1px 3px rgba(0,0,0,0.2)';
        }

        // Content column
        $tmp = array();
        if ($settings['colors']['content_column']['background_enabled']) {
            $tmp['background-color'] = $settings['colors']['content_column']['background_color'];
            $rules['.for-search-engines-only']['color'] = $settings['colors']['content_column']['background_color'];
        }
        if ($settings['colors']['content_column']['border_enabled']) {
            $tmp['border'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];


            // Content pagination, content inner borders
            $rules['.pagination, .product-info .price, .product-info .review, .product-info .options, .product-info .cart, .product-right-sm-logo, .product-right-sm-custom, .product-right-sm-info, .product-right-sm-related, .product-share, .product-right-sm-tags, .commentList .even, .childComment .even, .commentList .odd, .childComment .odd, .articleCat, .cart-info table, .cart-total table, .checkout-product table, .wishlist-info table, .order-list .order-content, table.list, .attribute, .compare-info']['border-top'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];
            $rules['.product-compare']['border-left'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];
            $rules['.product-info .image, .product-info .image-additional img, .product-info .image-additional-left img, .contact-map, .manufacturer-list, .checkout-heading, .review-list, .product-info .option-image img']['border'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];
            $rules['.product-info .image-additional img:hover, .product-info .image-additional-left img:hover']['border'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['link_hover_color'];
            $rules['.cart-info thead td, .cart-info tbody td, .cart-total table, .checkout-product thead td, .checkout-product tbody td, .checkout-product tfoot td, .wishlist-info thead td, .wishlist-info tbody td, .order-list .order-content, table.list td, .box-category-home .subcat li, .attribute td, .compare-info td, .mini-cart-info td, .mini-cart-total']['border-bottom'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];
            $rules['.cart-info table, .checkout-product table, .wishlist-info table, table.list, .attribute, .compare-info']['border-left'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];
            $rules['.cart-info table, .checkout-product table, table thead tr th:last-child, table tfoot tr td:last-child, .wishlist-info table, table.list td, .attribute td, .compare-info td']['border-right'] = $settings['colors']['content_column']['border']['width'].'px ' . $settings['colors']['content_column']['border']['style'] . ' ' . $settings['colors']['content_column']['border']['color'];

        }
        if ($settings['colors']['content_column']['shadow']) {
            $tmp['box-shadow'] = '0 1px 3px rgba(0,0,0,0.2)';
            $tmp['-webkit-box-shadow'] = '0 1px 3px rgba(0,0,0,0.2)';
            $tmp['-moz-box-shadow'] = '0 1px 3px rgba(0,0,0,0.2)';
        }

        if (count($tmp)) {
            $rules['.product-filter, #content, .cart-info thead td, .checkout-heading, .checkout-product thead td, table.list thead td, .compare-info thead td, .compare-info thead tr td:first-child, .attribute thead td, .attribute thead tr td:first-child, .tab-content, .manufacturer-heading, .wishlist-info thead td, #header #cart .content, .reveal-modal, .custom_box, .success, .warning, .attention, #cboxContent'] = $tmp;
        }

        $tmp = array();

        // header
        if ($settings['colors']['header']['background_enabled']) {
            $rules['#header']['background-color'] = $settings['colors']['header']['background_color'];
            $rules['.is-sticky #header']['background-color'] = $settings['colors']['header']['mini']['background_color'];
        }

        // Topbar
        $rules['#top-line'] = array(
            'color' => $settings['colors']['topbar']['text_color'],
        );
        if ($settings['colors']['topbar']['background_enabled']) {
            $rules['#top-line']['background-color'] = $settings['colors']['topbar']['background_color'] . ' !important';
        }
        $rules['#top-line a']['color'] = $settings['colors']['topbar']['link_normal_color'];
        $rules['#top-line a:hover']['color'] = $settings['colors']['topbar']['link_hover_color'];
        $rules['#top-line .separator']['border-right'] = '1px solid ' . $settings['colors']['topbar']['separator_color'];

        // Topbar dropdown
        $rules['.dropdown_l ul']['background-color'] = $settings['colors']['topbar']['dropdown_normal_background_color'] . ' !important';
        $rules['.dropdown_l li a']['color'] = $settings['colors']['topbar']['dropdown_normal_text_color'] . ' !important';
        $rules['.dropdown_l li a:hover'] = array(
            'background-color' => $settings['colors']['topbar']['dropdown_hover_background_color'] . ' !important',
            'color' => $settings['colors']['topbar']['dropdown_hover_text_color'] . ' !important'
        );

        // Logo
        $rules['#header #logo']['padding'] = '5px 15px 5px 0';

        // Search
        $rules['#header #search']['margin-top'] = '22px';
        $rules['#header #search input'] = array(
            'background-color' => $settings['colors']['searchbar']['background_color'] . ' !important',
            'color' => $settings['colors']['searchbar']['text_color'],
            'border' => '1px solid ' . $settings['colors']['searchbar']['border_color']
        );
        $rules['#header #search input:focus']['border'] = '1px solid ' . $settings['colors']['searchbar']['border_hover_color'];

        // Cart
        $rules['#header #cart']['background-color'] = $settings['colors']['shopping_cart']['background_color'];
        $rules['#header #cart h5']['color'] = $settings['colors']['shopping_cart']['text_color'];
        $rules['#header #cart .heading a div#cart-total'] = array(
            'color' => $settings['colors']['shopping_cart']['text_color'],
            'border-right' => '1px solid ' . $settings['colors']['shopping_cart']['border_color']
        );
        $rules['#notification a']['color'] = $settings['colors']['shopping_cart']['text_color'];
        $rules['#header #cart .heading a div#cart-total:hover'] = array(
            'color' => $settings['colors']['shopping_cart']['border_hover_color'],
            'border-right' => '1px solid ' . $settings['colors']['shopping_cart']['border_color']
        );

        // Main menu
        $rules['#menu']['background-color'] = $settings['colors']['menu']['background_color'];
        $rules['#menu_v > ul > li, #menu_h > ul > li, #menu_brands > ul > li, .menu_links,#menu_informations > ul > li, #menu_contacts > ul > li, #menu_normal > ul > li']['border-left'] = '1px solid ' . $settings['colors']['menu']['seperator_border_color'];
        $rules['#menu_v > ul > li a:hover, #menu_h > ul > li a:hover, #menu_brands > ul > li a:hover, #menu_informations > ul > li a:hover, #menu_contacts > ul > li a:hover, #menu_normal > ul > li a:hover'] = array(
            'background-color' => $settings['colors']['menu']['background_hover_color'],
            'color' => $settings['colors']['menu']['link_hover_color']
        );

        // Sub menu
        $rules['#menu_v > ul > li > div, #menu_v > ul > li > div > ul > li > div, #menu_v > ul > li > div > ul > li > div > ul > li > div, #menu_h > ul > li > div, #menu_brands > ul > li > div, #menu_informations > ul > li > div, #menu_contacts > ul > li > div, #menu_normal > ul > li > div'] = array(
            'background-color' => $settings['colors']['submenu']['background_color'],
            'color' => $settings['colors']['submenu']['link_color']
        );
        $rules['#menu_h > ul > li ul > li ul > li:first-child,']['border-top'] = '1px solid ' . $settings['colors']['submenu']['seperator_border_color'];
        $rules['#menu a, #menu span, #menu_v > ul > li ul > li > a, #menu_h > ul > li ul > li > a, #menu_h > ul > li a, #menu_brands > ul > li > div > div a, #menu_informations > ul > li a, #menu_informations > ul > li ul > li > a, #menu_informations > ul > li > span, #menu_normal > ul > li ul > li > a']['color'] = $settings['colors']['submenu']['link_color'];
        $rules['#menu_v > ul > li ul > li > a:hover, #menu_v > ul > li > div > ul > li ul > li > a:hover, #menu_h span a:hover, #menu_h > ul > li ul > li > a:hover, #menu_brands > ul > li > div > div:hover a, #menu_informations > ul > li ul > li > a:hover, #menu_informations > ul > li span:hover, #menu_normal > ul > li ul >li a:hover']['color'] = $settings['colors']['submenu']['link_hover_color'];
        $rules['#menu_brands > ul > li > div > div:hover']['background-color'] = $settings['colors']['submenu']['background_hover_color'];
        $rules['#menu_v > ul > li ul > li, #menu_h > ul > li ul > li, #menu_brands > ul > li > div > div, #menu_informations > ul > li ul > li, #menu_normal > ul > li ul >li']['border-bottom'] = '1px solid ' . $settings['colors']['submenu']['seperator_border_color'];
        $rules['#menu span, #menu span a']['background-color'] = $settings['colors']['submenu']['heading_background_color'];

        // Mobile menu
        $rules['.top-bar ul > li.name a'] = array(
            'background-color' => $settings['colors']['mobilemenu']['background_color'],
            'color' => $settings['colors']['mobilemenu']['text_color']
        );
        $rules['.top-bar:hover ul > li.name a']['background-color'] = $settings['colors']['mobilemenu']['background_hover_color'];

        // Left/right column heading
        if ($settings['colors']['left_column_header']['shadow']) {
            $rules['#column-left .box .box-heading, #column-left .product-box-slider .box-heading'] = $shadows;
        }
        if ($settings['colors']['left_column_header']['background_enabled']) {
            $rules['#column-left .box .box-heading, #column-left .product-box-slider .box-heading']['background-color'] = $settings['colors']['left_column_header']['background_color'];
            $rules['#column-left .box .box-heading, #column-left .product-box-slider .box-heading']['padding'] = '12px 15px';
        }
        else {
            $rules['#column-left .box .box-heading, #column-left .product-box-slider .box-heading']['padding'] = '12px 0';
        }
        if ($settings['colors']['left_column_header']['border_enabled']) {
            $rules['#column-left .box .box-heading, #column-left .product-box-slider .box-heading']['border-bottom'] = $settings['colors']['left_column_header']['border']['width'].'px ' . $settings['colors']['left_column_header']['border']['style'] . ' ' . $settings['colors']['left_column_header']['border']['color'];
        }
        $rules['#column-left .box .box-heading, #column-left .product-box-slider .box-heading']['color'] = $settings['colors']['left_column_header']['text_color'];

        if ($settings['colors']['right_column_header']['shadow']) {
            $rules['#column-right .box .box-heading, #column-right .product-box-slider .box-heading'] = $shadows;
        }
        if ($settings['colors']['right_column_header']['background_enabled']) {
            $rules['#column-right .box .box-heading, #column-right .product-box-slider .box-heading']['background-color'] = $settings['colors']['right_column_header']['background_color'];
            $rules['#column-right .box .box-heading, #column-right .product-box-slider .box-heading']['padding'] = '12px 15px';
        }
        else {
            $rules['#column-right .box .box-heading, #column-right .product-box-slider .box-heading']['padding'] = '12px 0';
        }
        if ($settings['colors']['right_column_header']['border_enabled']) {
            $rules['#column-right .box .box-heading, #column-right .product-box-slider .box-heading']['border-bottom'] = $settings['colors']['right_column_header']['border']['width'].'px ' . $settings['colors']['right_column_header']['border']['style'] . ' ' . $settings['colors']['right_column_header']['border']['color'];
        }
        $rules['#column-right .box .box-heading, #column-right .product-box-slider .box-heading']['color'] = $settings['colors']['right_column_header']['text_color'];

        // Left/right column box
        if ($settings['colors']['left_column_box']['shadow']) {
            $rules['#column-left .box .box-content, #column-left .product-box-slider .box-content'] = $shadows;
        }
        if ($settings['colors']['left_column_box']['background_enabled']) {
            $rules['#column-left .box .box-content, #column-left .product-box-slider .box-content']['background-color'] = $settings['colors']['left_column_box']['background_color'];
            $rules['#column-left .box .box-content, #column-left .product-box-slider .box-content']['padding'] = '20px 15px';
        }
        else {
            $rules['#column-left .box .box-content, #column-left .product-box-slider .box-content']['padding'] = '15px 0';
        }
        if ($settings['colors']['left_column_box']['border_enabled']) {
            $rules['#column-left .box .box-content, #column-left .product-box-slider .box-content']['border-bottom'] = $settings['colors']['left_column_box']['border']['width'].'px ' . $settings['colors']['left_column_box']['border']['style'] . ' ' . $settings['colors']['left_column_box']['border']['color'];
        }
        $rules['#column-left .box .box-content ul li, #column-left .box .box-content ul li a, #column-left .product-box-slider .box-content, #column-left .box-content a']['color'] = $settings['colors']['left_column_box']['text_color'];

        if ($settings['colors']['right_column_box']['shadow']) {
            $rules['#column-right .box .box-content, #column-right .product-box-slider .box-content'] = $shadows;
        }
        if ($settings['colors']['right_column_box']['background_enabled']) {
            $rules['#column-right .box .box-content, #column-right .product-box-slider .box-content']['background-color'] = $settings['colors']['right_column_box']['background_color'];
            $rules['#column-right .box .box-content, #column-right .product-box-slider .box-content']['padding'] = '20px 15px';
        }
        else {
            $rules['#column-right .box .box-content, #column-right .product-box-slider .box-content']['padding'] = '15px 0';
        }
        if ($settings['colors']['right_column_box']['border_enabled']) {
            $rules['#column-right .box .box-content, #column-right .product-box-slider .box-content']['border-bottom'] = $settings['colors']['right_column_box']['border']['width'].'px ' . $settings['colors']['right_column_box']['border']['style'] . ' ' . $settings['colors']['right_column_box']['border']['color'];
        }
        $rules['#column-right .box .box-content ul li, #column-right .box .box-content ul li a, #column-right .product-box-slider .box-content, #column-right .box-content a']['color'] = $settings['colors']['right_column_box']['text_color'];

        // Category column heading
        if ($settings['colors']['category_column_header']['shadow']) {
            $rules['.box-category .box-heading-category'] = $shadows;
        }
        if ($settings['colors']['category_column_header']['background_enabled']) {
            $rules['.box-category .box-heading-category']['background-color'] = $settings['colors']['category_column_header']['background_color'];
            $rules['.box-category .box-heading-category']['padding'] = '12px 15px';
        }
        else {
            $rules['.box-category .box-heading-category']['padding'] = '12px';
        }
        if ($settings['colors']['category_column_header']['border_enabled']) {
            $rules['.box-category .box-heading-category']['border-bottom'] = $settings['colors']['left_column_header']['border']['width'].'px ' . $settings['colors']['left_column_header']['border']['style'] . ' ' . $settings['colors']['left_column_header']['border']['color'];
        }
        $rules['.box-category .box-heading-category']['color'] = $settings['colors']['category_column_header']['text_color'];

        // Category column box
        if ($settings['colors']['category_column_box']['shadow']) {
            $rules['.box-category .box-content-category'] = $shadows;
        }
        if ($settings['colors']['category_column_box']['background_enabled']) {
            $rules['.box-category .box-content-category']['background-color'] = $settings['colors']['category_column_box']['background_color'];
            $rules['.box-category .box-content-category ul > li > a:hover']['padding-left'] = '18px';
            $rules['.box-category .box-content-category ul > li > a']['padding'] = '10px 25px 10px 15px';
            $rules['.box-category .box-content-category ul > li > ul > li > a']['padding'] = '10px 25px 10px 30px';
            $rules['.box-category .box-content-category ul > li > ul > li > a:hover']['padding-left'] = '33px';
        }
        else {
            $rules['.box-category .box-content-category ul > li > a:hover']['padding-left'] = '3px';
            $rules['.box-category .box-content-category ul > li > a']['padding'] = '10px 25px 10px 0';
            $rules['.box-category .box-content-category ul > li > ul > li > a']['padding'] = '10px 25px 10px 15px';
            $rules['.box-category .box-content-category ul > li > ul > li > a:hover']['padding-left'] = '18px';
        }
        if ($settings['colors']['category_column_box']['border_enabled']) {
            $rules['.box-category .box-content-category ul > li + li, .box-category .box-content-category ul > li ul']['border-top'] = $settings['colors']['category_column_box']['border']['width'].'px ' . $settings['colors']['category_column_box']['border']['style'] . ' ' . $settings['colors']['category_column_box']['border']['color'];
        }
        $rules['.box-category .box-content-category a']['color'] = $settings['colors']['category_column_box']['text_color'];

        // Prices
        $rules['.price, .total, .product-info .price .discount']['color'] = $settings['colors']['prices']['price_color'];
        $rules['.price-old, .wishlist-info tbody .price s']['color'] = $settings['colors']['prices']['old_price_color'];
        $rules['.price-new, .cart-total .total-r']['color'] = $settings['colors']['prices']['new_price_color'];
        $rules['.price-tax, .product-info .price .reward']['color'] = $settings['colors']['prices']['tax_price_color'];

        // Buttons
        $rules['a.button, input.button, .ei-title h4 a.button, .product-right-sm-tags div a'] = array(
            'background-color' => $settings['colors']['buttons']['forms']['background_color'],
            'color' => $settings['colors']['buttons']['forms']['text_color']
        );
        $rules['a.button:hover, input.button:hover, .ei-title h4 a.button:hover, .product-right-sm-tags div a:hover'] = array(
            'background-color' => $settings['colors']['buttons']['forms']['background_hover_color'],
            'color' => $settings['colors']['buttons']['forms']['text_hover_color']
        );

        $rules['.product-grid .cart input.button, .product-list .cart input.button, #content .box-product .cart input.button, .product-box-slider .cart input.button, .product-bottom-related .cart input.button, #header #cart .checkout .mini-cart-button, a.button-exclusive, input.button-exclusive'] = array(
            'background-color' => $settings['colors']['buttons']['cart']['background_color'],
            'color' => $settings['colors']['buttons']['cart']['text_color']
        );
        $rules['.product-grid .cart input.button:hover, .product-list .cart input.button:hover, #content .box-product .cart input.button:hover, .product-box-slider .cart input.button:hover, .product-bottom-related .cart input.button:hover, #header #cart .checkout .mini-cart-button:hover, a.button-exclusive:hover, input.button-exclusive:hover'] = array(
            'background-color' => $settings['colors']['buttons']['cart']['background_hover_color'],
            'color' => $settings['colors']['buttons']['cart']['text_hover_color']
        );

        $rules['a.button, input.button, a.button-exclusive, input.button-exclusive, input.button'] = array(
            '-webkit-border-radius' => $settings['colors']['buttons']['border_radius'] . 'px',
            '-moz-border-radius' => $settings['colors']['buttons']['border_radius'] . 'px',
            'border-radius' => $settings['colors']['buttons']['border_radius'] . 'px',
        );

        // Price box
        if ($settings['colors']['price_box']['shadow_hover']) {
            $rules['.product-grid > div:hover, #content .box-product > div:hover, .product-box-slider'] = $shadows;
        }
        $rules['.product-grid > div:hover, .product-list > div:hover, #content .box-product >div:hover']['background-color'] = $settings['colors']['price_box']['background_hover_color'];
        $rules['span.sale-icon']['background-color'] = $settings['colors']['price_box']['sale_badge'];
        if ($settings['colors']['price_box']['border_enabled']) {
            $rules['.product-grid > div:hover, .product-list > div:hover, #content .box-product > div:hover']['border'] = $settings['colors']['price_box']['border']['width'].'px ' . $settings['colors']['price_box']['border']['style'] . ' ' . $settings['colors']['price_box']['border']['color'];
        }
        $rules['.product-box-slider .name, .product-box-slider .name a, .category-list .name a']['color'] = $settings['colors']['price_box']['text_color'];

        // Product zoom enabled?
        if ($settings['product']['display_image'] == 'zoom') {
            $rules['#content .product-grid div:hover .image a img, #content .box-product div:hover .image a img'] = array(
                'transform' => 'scale(1.1)',
                '-o-transform' => 'scale(1.1)',
                '-ms-transform' => 'scale(1.1)',
                '-moz-transform' => 'scale(1.1)',
                '-webkit-transform' => 'scale(1.1)',
            );
        }

        // Tabs
        $rules['.htabs a'] = array(
            'background-color' => $settings['colors']['tabs']['background_color'],
            'color' => $settings['colors']['tabs']['text_color']
        );
        $rules['.htabs a.selected, .htabs a:hover'] = array(
            'background-color' => $settings['colors']['tabs']['selected_background_color'],
            'color' => $settings['colors']['tabs']['text_color']
        );

        // Footer
        $rules['#footer'] = array(
            'background-color' => $settings['colors']['footer']['background_color'],
            'color' => $settings['colors']['footer']['text_color']
        );
        if ($settings['colors']['footer']['border_enabled']) {
            $rules['#footer h3']['border-bottom'] = $settings['colors']['footer']['border']['width'].'px ' . $settings['colors']['footer']['border']['style'] . ' ' . $settings['colors']['footer']['border']['color'];
            $rules['#footer_poweredby']['border-top'] = $settings['colors']['footer']['border']['width'].'px ' . $settings['colors']['footer']['border']['style'] . ' ' . $settings['colors']['footer']['border']['color'];
        }
        $rules['#footer h3']['color'] = $settings['colors']['footer']['title_color'];
        $rules['#footer a']['color'] = $settings['colors']['footer']['link_color'];
        $rules['#footer a:hover']['color'] = $settings['colors']['footer']['link_hover_color'];

        // Bottom footer
        $rules['#footer_copyright'] = array(
            'background-color' => $settings['colors']['footer_copyright']['background_color'],
            'color' => $settings['colors']['footer_copyright']['text_color']
        );
        if ($settings['colors']['footer_copyright']['border_enabled']) {
            $rules['#footer_copyright']['border-top'] = $settings['colors']['footer_copyright']['border']['width'].'px ' . $settings['colors']['footer_copyright']['border']['style'] . ' ' . $settings['colors']['footer_copyright']['border']['color'];
            $rules['#footer_copyright']['border-bottom'] = $settings['colors']['footer_copyright']['border']['width'].'px ' . $settings['colors']['footer_copyright']['border']['style'] . ' ' . $settings['colors']['footer_copyright']['border']['color'];
        }
        $rules['#footer_copyright h3']['color'] = $settings['colors']['footer_copyright']['title_color'];
        $rules['#footer_copyright a']['color'] = $settings['colors']['footer_copyright']['link_color'];
        $rules['#footer_copyright a:hover']['color'] = $settings['colors']['footer_copyright']['link_hover_color'];

        // Fonts
        $fonts = array();
        foreach ($settings['fonts'] as $where => $list) {
            if ($list['name'] != 'Arial' && $list['name'] != 'Helvetica') {
                $fonts[$list['name']] = isset($list['weight']) ? $list['weight'] : 'normal';
            }
        }
        foreach ($fonts as $name => $weight) {
            $font = 'fonts/' . str_replace(' ', '', strtolower($name));
            $css .= '@font-face{font-family:"' . $name . '";
            src: url("' . $font . '.eot");
            src: url("' . $font . '.eot?#iefix") format("embedded-opentype"),
                url("' . $font . '.svg#' . $name . '") format("svg"),
                url("' . $font . '.woff") format("woff"),
                url("' . $font . '.ttf") format("truetype");
            font-weight: normal;
            font-style: ' . $weight . ';}';
        }

        // Body fonts
        $rules['body, p, .ei-title h3 a, .cart-info thead .price, .cart-info tbody .price, .top-bar ul > li.name h1 a, .box-category-home .subcat a, .box-category-home .all a']['font-family'] = '"' . $settings['fonts']['body']['name'] . '", sans-serif';
        $rules['body, p, .ei-title h3 a, .cart-info thead .price, .cart-info tbody .price, .top-bar ul > li.name h1 a, .box-category-home .subcat a, .box-category-home .all a']['direction'] = 'ltr';

        // Headings fonts
        $rules['h1, h2, h3, h4, h5, h6, #content .box-heading, .box-category .box-heading-category, .box-filter .box-heading, #column-left .box .box-heading, #column-right .box .box-heading, #column-left .product-box-slider .box-heading, #column-right .product-box-slider .box-heading, .product-grid .name a, .product-list .name a, #content .box-product .name a, .product-right-sm-related .name a, .product-bottom-related .name a, #column-left .box-product .name a, #column-right .box-product .name a, .product-box-slider .name a, .box-category-home a']['font-family'] = '"' . $settings['fonts']['headings']['name'] . '","' . $settings['fonts']['body']['name'] . '", sans-serif';
        if ($settings['fonts']['headings']['uppercase']) {
            $rules['h1, h2, h3, h4, h5, h6, #content .box-heading, .box-category .box-heading-category, .box-filter .box-heading, #column-left .box .box-heading, #column-right .box .box-heading, #column-left .product-box-slider .box-heading, #column-right .product-box-slider .box-heading, .product-grid .name a, .product-list .name a, #content .box-product .name a, .product-right-sm-related .name a, .product-bottom-related .name a, #column-left .box-product .name a, #column-right .box-product .name a, .product-box-slider .name a, .box-category-home a']['text-transform'] = 'uppercase';
        }
        $rules['h1, h2, h3, h4, h5, h6, #content .box-heading, .box-category .box-heading-category, .box-filter .box-heading, #column-left .box .box-heading, #column-right .box .box-heading, #column-left .product-box-slider .box-heading, #column-right .product-box-slider .box-heading, .product-grid .name a, .product-list .name a, #content .box-product .name a, .product-right-sm-related .name a, .product-bottom-related .name a, #column-left .box-product .name a, #column-right .box-product .name a, .product-box-slider .name a, .box-category-home a']['font-style'] = $settings['fonts']['headings']['type'];

        // Menu fonts
        $rules['#menu #homepage a, #menu_oc > ul > li > a, #menu_v > ul > li > a, #menu_h > ul > li > a, #menu_brands > ul > li > a, .menu_links a, #menu_informations > ul > li > span, #menu_informations > ul > li > a, #menu_contacts > ul > li > a, .top-bar > ul .name h1 a, #menu_normal a, #menu_normal > ul > li > a'] = array(
            'font-family' => $settings['fonts']['menu']['name'],
            'font-size' => $settings['fonts']['menu']['size'],
            'font-style' => $settings['fonts']['menu']['type']
        );
        if ($settings['fonts']['menu']['uppercase']) {
            $rules['#menu #homepage a, #menu_oc > ul > li > a, #menu_v > ul > li > a, #menu_h > ul > li > a, #menu_brands > ul > li > a, .menu_links a, #menu_informations > ul > li > span, #menu_informations > ul > li > a, #menu_contacts > ul > li > a, .top-bar > ul .name h1 a, #menu_normal > ul > li > a, #menu_normal a']['text-transform'] = 'uppercase';
        }

        // Icons
        $css .= '.product-list .wishlist, .product-grid .wishlist {background: url("../image/wishlist-1.png") no-repeat scroll center transparent;}
.product-list .compare, .product-grid .compare {background: url("../image/compare-1.png")  no-repeat scroll center transparent;}
span.wishlist {background: url("../image/wishlist-1.png") no-repeat scroll left center transparent;}
span.compare {background: url("../image/compare-1.png") no-repeat scroll left center transparent;}
span.friend {background: url("../image/send-1.png") no-repeat scroll left center transparent;}
#header #cart .heading a div#cart-icon {
    background: url("../image/icon_cart_1.png")  no-repeat scroll 65% 50%;
}';
        $css .= $settings['custom']['css'];

        foreach ($rules as $rule => $data) {
            $tmp = '';
            if (!is_array($data)) {
                exit($rule . ' => ' . $data);
            }
            foreach ($data as $key => $value) {
                $tmp .= $key . ':' . $value . ';';
            }
            $css .= $rule . '{' . $tmp . '}' . PHP_EOL;
        }

        /*
        // Cart



/*  Mobile Main Menu Bar  *
.top-bar ul > li.name a {
    background-color: <?php echo $this->config->get('oxy_mm_mobile_bg_color'); ?>;
    color: <?php echo $this->config->get('oxy_mm_mobile_text_color'); ?>!important;
}
.top-bar:hover ul > li.name a {
    background-color: <?php echo $this->config->get('oxy_mm_mobile_bg_hover_color'); ?>;
}

        */
        $css .= '.accordion li.dcjq-parent-li {position: relative;}
        .accordion li > a + .dcjq-icon {float: right; width: 39px; height: 39px; position: absolute; top: 0; right: 0; background: url(../image/plus_red.png) no-repeat center; cursor:pointer}
        .accordion li.dcjq-parent-li .dcjq-icon:hover {background: url(../image/plus_red_hover.png) no-repeat center #FFFFFF;}
        .accordion li.dcjq-parent-li > a + .dcjq-icon {display: block; width: 39px; height: 39px; background: url(../image/plus_red.png) no-repeat center;}
        .accordion li.dcjq-parent-li > a.active + .dcjq-icon {display: block; width: 39px; height: 39px; background: url(../image/minus_red.png) no-repeat center;}
        .accordion li.dcjq-parent-li > a.active + .dcjq-icon:hover {background: url(../image/minus_red.png) no-repeat center #FFFFFF;}
        .accordion li > a + .dcjq-icon {display: none;}
        .accordion li > a.active + .dcjq-icon {display: none;}

        #toTop {
            display:none;
            text-decoration:none;
            position:fixed;
            bottom:45px;
            right:20px;
            overflow:hidden;
            width:42px;
            height:42px;
            border:none;
            text-indent:100%;
            background:url(../image/ui.totop.png) no-repeat left top rgba(0, 0, 0, 0.15);
        }

        #toTopHover {
            background:url(../image/ui.totop.png) no-repeat left -42px;
            width:42px;
            height:42px;
            display:block;
            overflow:hidden;
            float:left;
            opacity: 0;
            -moz-opacity: 0;
            filter:alpha(opacity=0);
        }

        #toTop:active, #toTop:focus {
            outline:none;
        }
        #livesearch_search_results {
                margin:0px;
                padding:0px;
                position: absolute;
                top: 38px;
                left: 0px;
                background-color: #FFF;
                list-style-type: none;
                z-index: 12;
                width: 100%;
                min-width: 160px;
                border-left: 1px solid #CCC;
                border-right: 1px solid #CCC;
                border-bottom: 1px solid #CCC;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        #livesearch_search_results li {
                padding: 12px 15px;
                border-bottom: 1px solid #F1F1F1;
        }
        #livesearch_search_results li:last-child  {
                border-bottom: none;
        }
        #livesearch_search_results a,
        #livesearch_search_results a:visited,
        #livesearch_search_results a:hover {
                color: #464646;
                text-decoration: none;
        }
        #livesearch_search_results li:hover a {
                color: #FFFFFF;
        }
        #livesearch_search_results li {
                transition: all 0.2s ease-in 0s;
        }';

        $fp = fopen(DIR_CACHE . '/builder.stylesheet.' . $theme_id, 'w+');
        fwrite($fp, $css);
        fclose($fp);

    }
}
