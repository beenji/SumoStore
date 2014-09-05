<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <base href="<?php echo $base?>" />
    <link rel="shortcut icon" href="view/img/icon.png">

    <title><?php echo $this->document->getTitle()?></title>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:600,300,400" rel="stylesheet" type="text/css">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/bootstrap/bootstrap.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="view/js/fixes/html5shiv.js"></script>
        <script src="view/js/fixes/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/fonts/awesome.css">

</head>

<body class="texture">

<div id="cl-wrapper" class="login-container">

    <div class="middle-login">
        <div class="block-flat">
            <div class="header">
                <h3 class="text-center"><img class="logo-img" src="view/img/logo.png" alt="logo"/></h3>
            </div>
            <div>
                <form style="margin-bottom: 0px !important;" method="post" class="form-horizontal" action="<?php echo $action; ?>">
                    <div class="content">
                        <h4 class="title"><?php echo Sumo\Language::getVar('SUMO_ADMIN_LOGIN'); ?></h4>
                        <?php if ($error_warning) { ?>
                        <div class="alert alert-danger"><?php echo $error_warning; ?></div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user align-center" style="width: 20px;"></i></span>
                                    <input type="text" placeholder="<?php echo Sumo\Language::getVar('SUMO_ADMIN_LOGIN_USERNAME'); ?>" id="username" name="username" class="form-control" value="<?php echo $username; ?>" />
                                </div>

                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock align-center" style="width: 20px;"></i></span>
                                    <input type="password" placeholder="<?php echo Sumo\Language::getVar('SUMO_ADMIN_LOGIN_PASSWORD'); ?>" id="password" name="password" class="form-control" />
                                </div>

                                <?php if ($this->config->get('admin_reset_password')) { ?>
                                <p class="align-right" style="margin: -8px 0 0 0;">
                                    <a href="<?php echo $forgotten; ?>"><?php echo Sumo\Language::getVar('SUMO_ADMIN_LOGIN_FORGOTTEN')?></a>
                                </p>
                                <hr style="margin-bottom: 10px;" />
                                <?php } ?>

                                <label for="remember" class="checkbox">
                                    <input type="checkbox" name="remember" id="remember" value="1" style="margin-top: 3px;" />
                                    <?php echo Sumo\Language::getVar('SUMO_ADMIN_LOGIN_REMEMBER')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="foot">
                        <a class="btn btn-default" href="../"><?php echo Sumo\Language::getVar('SUMO_BUTTON_CANCEL'); ?></a>
                        <button class="btn btn-primary" type="submit"><?php echo Sumo\Language::getVar('SUMO_ADMIN_LOGIN'); ?></button>
                    </div>

                    <?php if ($redirect) { ?>
                    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                    <?php } ?>
                </form>
            </div>
        </div>
        <div class="text-center out-links">Copyright &copy; 2013-<?php echo date('Y')?> <a href="https://www.sumostore.net/">Sumostore</a><br />Protected by SumoGuard</div>
    </div>
</div>
</body>
</html>
