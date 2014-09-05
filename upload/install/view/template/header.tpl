<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="<?php echo $base; ?>" />

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="SumoStore">

		<title>SumoStore - <?php echo $this->config->get('LANG_INSTALL_TITLE')?></title>

		<link href="view/stylesheet/bootstrap.css" rel="stylesheet">
		<link href="view/stylesheet/sumostore.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
        <script src="../catalog/view/required/general.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h3>SumoStore <?php echo $this->config->get('LANG_INSTALL_TITLE')?></h3>
			</div>
