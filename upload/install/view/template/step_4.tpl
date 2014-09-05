<?php echo $header; ?>
<h1><?php echo $this->config->get('LANG_STEP_4_TITLE')?></h1>
<div class="navigation">
    <div class="progress progress-striped">
        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
            <span class="sr-only">100% Complete</span>
        </div>
    </div>

    <ul class="steps">
        <li><?php echo $this->config->get('LANG_STEP_1_SHORT_TITLE')?></li>
        <li><?php echo $this->config->get('LANG_STEP_2_SHORT_TITLE')?></li>
        <li><?php echo $this->config->get('LANG_STEP_3_SHORT_TITLE')?></li>
        <li><strong><?php echo $this->config->get('LANG_STEP_4_SHORT_TITLE')?></strong></li>
    </ul>
</div>
<div id="content">
    <p class="alert alert-danger"><?php echo $this->config->get('LANG_STEP_4_INSTALL_DIR')?></p>
    <p class="alert alert-success"><?php echo $this->config->get('LANG_STEP_4_SUCCESS')?></p>
    <div class="buttons">
        <div class="pull-right">
            <a class="btn btn-primary pull-right" href="../admin/settings/store/general?firstrun=true" target="_blank"><?php echo $this->config->get('LANG_STEP_4_BACKEND')?></a>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php echo $footer; ?>
