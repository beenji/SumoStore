<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="SumoStore">

        <base href="<?php echo $base?>" />

        <title><?php echo $title?> - <?php echo $this->config->get('name')?></title>

        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/bootstrap/bootstrap.switch.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/bootstrap/bootstrap.datetimepicker.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/jquery/jquery.icheck.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/jquery/jquery.gritter.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/jquery/jquery.nanoscroller.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/jquery/jquery.redactor.css">
        <link rel="shortcut icon" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/img/icon.png">

        <link href="//fonts.googleapis.com/css?family=Open+Sans:600,300,400" rel="stylesheet" type="text/css">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="view/js/fixes/html5shiv.js"></script>
            <script src="view/js/fixes/respond.min.js"></script>
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/style.css">
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/fonts/awesome.css">

        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.js"></script>
        <script type="text/javascript">
            var sessionToken = '<?php echo $token; ?>',
                base = '<?php echo $base; ?>',
                formError = '';
        </script>

        <?php foreach ($styles as $style) { ?>
        <link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
        <?php } ?>
    </head>
    <body>

        <div id="cl-wrapper">

            <div class="cl-sidebar">
                <div class="cl-toggle"><i class="fa fa-bars"></i></div>
                <div class="cl-navblock">
                    <div class="menu-space">
                        <div class="content">
                            <div class="sidebar-logo">
                                <div class="logo">
                                    <a href="./">SumoStore</a>
                                </div>
                            </div>
                            <ul class="cl-vnavigation">
                                <?php
                                foreach ($menu as $item) {
                                    echo '<li class="parent';
                                    if (isset($item['active'])) {
                                        echo ' open active';
                                    }
                                    echo '"><a href="' . $item['url'] . '">';
                                    if (!empty($item['icon'])) {
                                        echo '<i class="' . $item['icon'] . '"></i>';
                                    }
                                    echo '<span>' . Sumo\Language::getVar($item['name']) . '</span></a>';
                                    if (isset($item['children'])) {
                                        echo '<ul class="sub-menu" ';
                                        if (isset($item['active'])) {
                                            echo 'style="display:block;"';
                                        }
                                        echo '>' . PHP_EOL;
                                        foreach ($item['children'] as $list) {
                                            echo '<li';
                                            if (isset($list['active'])) {
                                                echo ' class="active"';
                                            }
                                            echo '><a href="' . $list['url'] . '">';
                                            if (!empty($list['icon'])) {
                                                #echo '<i class="' . $list['icon'] . '"></i>';
                                            }
                                            echo Sumo\Language::getVar($list['name']) . '</a></li>' . PHP_EOL;
                                        }
                                        echo '</ul>';
                                    }
                                    echo '</li>' . PHP_EOL;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="text-right collapse-button" style="padding:7px 9px;">
                        <input type="text" class="form-control search" id="menuSearch" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCH_PLURAL');?>" />
                        <button id="sidebar-collapse" class="btn btn-default" style=""><i style="color:#fff;" class="fa fa-angle-left"></i></button>
                    </div>
                </div>
            </div>
            <div class="container-fluid" id="pcont">
                <div id="head-nav" class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-collapse">
                            <ul class="nav navbar-nav navbar-right user-nav">
                                <?php $latestVersion = Sumo\Communicator::getVersion(); ?>
                                <li><a href="http://www.sumostore.nl/download/" style="color:#<?php echo $latestVersion == VERSION ? '629E14' : 'F41958'?>">V<?php echo VERSION?></a></li>
                                <li class="dropdown profile_menu">
                                    <a href="javascript:return false;" class="dropdown-toggle" data-toggle="dropdown"><span><?php echo Sumo\Language::getVar('SUMO_ADMIN_HEADER_WELCOME', $this->user->getUserName())?></span> <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <?php if (isset($stores)): foreach ($stores as $store):?>
                                        <li><a href="http://<?php echo $store['base_http']?>" target="_blank"><?php echo $store['name']?></a></li>
                                        <?php endforeach; endif; ?>
                                        <li class="divider"></li>
                                        <li><a href="<?php echo $this->url->link('common/logout', 'token=' . $this->session->data['token'], 'SSL')?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_LOGOUT')?></a></li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="nav navbar-nav not-nav">
                                <li class="button dropdown">
                                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-globe"></i><span class="bubble" id="notifications-bubble">0</span></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <div class="nano nscroller">
                                                <div class="content">
                                                    <ul id="notifications">
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <?php /*
                                <li class="button"><a class="toggle-menu menu-right push-body speech-button" href="javascript:;"><i class="fa fa-coffee"></i></a></li>
                                */ ?>
                                <li class="button" id="debug_enabled"><a class=""><i class="fa fa-fighter-jet"></i><span class="bubble" id="debug"></span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (is_array($errors) && count($errors)) {
                    echo '<div class="clearfix"><br /></div>';
                    foreach ($errors as $error) {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }
                } ?>

                <div class="cl-mcont">
                    <div class="page-head">
                        <h2><?php echo $title?></h2>
                        <ol class="breadcrumb">
                            <?php foreach ($breadcrumbs as $list): ?>
                            <li<?php if (empty($list['href'])): echo ' class="active">'; else:?>><a href="<?php echo $list['href']?>"><?php endif?><?php echo $list['text']?><?php if (!empty($list['href'])): ?></a><?php endif?></li>
                            <?php endforeach?>
                        </ol>
                    </div>
