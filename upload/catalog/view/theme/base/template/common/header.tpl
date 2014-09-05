<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <base href="<?php echo $base?>">
        <link href="<?php echo $base?>catalog/view/required/stylesheet.php?theme=<?php echo $this->config->get('template')?>&files=picons.css,general.css,extra.css&store_id=<?php echo $this->config->get('store_id')?>" rel="stylesheet">

        <title><?php echo $title?></title>

        <?php
        if (is_array($description)) {
            if (!empty($description[$this->config->get('language_id')])) {
                $description = $description[$this->config->get('language_id')];
            }
            else {
                $tmp = $description;
                foreach ($tmp as $desc) {
                    if (!empty($desc)) {
                        $description = $desc;
                    }
                }
            }
        }
        if (is_array($description) || empty($description)) {
            unset($description);
        }

        if (isset($description)) { ?>

        <meta name="description" content="<?php echo $description; ?>" />
        <?php } ?>
        <?php if (!empty($keywords)) { ?>

        <meta name="keywords" content="<?php echo $keywords; ?>" />
        <?php } ?>
        <?php if (!empty($icon)) { ?>

        <link href="<?php echo $icon; ?>" rel="icon" />
        <?php } ?>
        <?php if (isset($links)) { foreach ($links as $link) { ?>

        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
        <?php } } ?>

        <link href='//fonts.googleapis.com/css?family=Open+Sans:700,400,300' rel='stylesheet' type='text/css'>
        <?php if (isset($styles)) { foreach ($styles as $style) { ?>

        <link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
        <?php } } ?>

        <script type="text/javascript" src="<?php echo $base?>catalog/view/required/general.js"></script>
        <script type="text/javascript" src="<?php echo $base?>catalog/view/required/sumostore.js"></script>
        <?php if (isset($scripts)) { foreach ($scripts as $script) { ?>
        <script type="text/javascript" src="<?php echo $script; ?>"></script>
        <?php } } ?>

    </head>
    <body>
        <div class="container body-wrapper">
            <div class="container header-wrapper">
                <div class="row" id="top-line">
                    <div class="col-md-12">
                        <div class="col-md-6 col-xs-6">
                            <?php if (!$logged) {
                                echo Sumo\Language::getVar('SUMO_NOUN_WELCOME_GUEST', array($this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL')));
                            } else {
                                echo Sumo\Language::getVar('SUMO_NOUN_WELCOME_CUSTOMER', array($this->url->link('account/account', '', 'SSL'), $this->customer->getData('firstname')));
                            } ?>
                        </div>
                        <div class="col-md-6 text-right col-xs-6 header-breadcrumbs">
                            <a href="<?php echo $this->url->link('account/account', '', 'SSL')?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT')?></a>
                            <a href="<?php echo $this->url->link('product/compare')?>" id="compare-total"><?php echo Sumo\Language::getVar('SUMO_PRODUCT_COMPARE', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0))?></a>
                            <a href="<?php echo $this->url->link('account/wishlist', '', 'SSL')?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT_WISHLIST')?></a>
                            <?php if ($logged): ?>
                            <a href="<?php echo $this->url->link('account/logout', 'logout=' . $this->session->data['logout'], 'SSL')?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_ACCOUNT_LOGOUT')?></a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="header">
                        <?php echo $this->getChild('app/widgetsimpleheader')?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav" id="header-menu">
                            <li>
                                <a href="<?php echo $this->url->link()?>">
                                    <?php echo Sumo\Language::getVar('SUMO_NOUN_HOME')?>
                                </a>
                            </li>
                            <?php foreach ($categories as $list): ?>
                            <li>
                                <a href="<?php echo $list['url']?>"><?php echo $list['name']?></a>
                                <?php if (isset($list['children'])): ?>
                                <ul>
                                    <?php foreach ($list['children'] as $child): ?>
                                    <li><a href="<?php echo $child['url']?>"><?php echo $child['name']?></a></li>
                                    <?php endforeach?>
                                </ul>
                                <?php endif ?>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="content-container">
                <div class="col-md-12">
                    <div id="notification" class="notification-holder"></div>
                </div>
                <?php if ($this->config->get('in_maintenance')): ?>
                <div class="col-md-12 notification-holder">
                    <div class="alert alert-danger"><?php echo Sumo\Language::getVar('SUMO_MAINTENANCE_CONTENT')?></div>
                </div>
                <?php endif?>
