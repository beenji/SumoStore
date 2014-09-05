<?php echo $header; ?>
<h1><?php echo $this->config->get('LANG_STEP_2_TITLE')?></h1>
<div class="navigation">
    <div class="progress progress-striped">
        <div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
            <span class="sr-only">50% Complete</span>
        </div>
    </div>

    <ul class="steps">
        <li><?php echo $this->config->get('LANG_STEP_1_SHORT_TITLE')?></li>
        <li><strong><?php echo $this->config->get('LANG_STEP_2_SHORT_TITLE')?></strong></li>
        <li><?php echo $this->config->get('LANG_STEP_3_SHORT_TITLE')?></li>
        <li><?php echo $this->config->get('LANG_STEP_4_SHORT_TITLE')?></li>
    </ul>
</div>
<div id="content">
    <?php if (!empty($warning)): ?>
    <div class="alert alert-danger"><?php echo $warning?></div>
    <?php endif?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <div class="alert alert-info"><p><?php echo $this->config->get('LANG_STEP_2_INFO')?></p></div>
        <p><?php echo $this->config->get('LANG_STEP_2_PART_1')?></p>
        <fieldset>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="35%"><strong><?php echo $this->config->get('LANG_PHP_SETTINGS')?></strong></th>
                        <th width="25%"><strong><?php echo $this->config->get('LANG_CURRENT')?></strong></th>
                        <th width="25%"><strong><?php echo $this->config->get('LANG_REQUIRED')?></strong></th>
                        <th width="15%" class="text-center"><strong>Status</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $this->config->get('LANG_PHP_VERSION')?>:</td>
                        <td><?php echo phpversion(); ?></td>
                        <td>5.3+</td>
                        <td align="center"><?php echo (phpversion() >= '5.0') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>Register Globals:</td>
                        <td><?php echo (ini_get('register_globals')) ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Uit</td>
                        <td align="center"><?php echo (!ini_get('register_globals')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>Magic Quotes GPC:</td>
                        <td><?php echo (ini_get('magic_quotes_gpc')) ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Uit</td>
                        <td align="center"><?php echo (!ini_get('magic_quotes_gpc')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>File Uploads:</td>
                        <td><?php echo (ini_get('file_uploads')) ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo (ini_get('file_uploads')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>Session Auto Start:</td>
                        <td><?php echo (ini_get('session_auto_start')) ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Uit</td>
                        <td align="center"><?php echo (!ini_get('session_auto_start')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>

        <p><?php echo $this->config->get('LANG_STEP_2_PART_2')?></p>
        <fieldset>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="35%"><strong><?php echo $this->config->get('LANG_PHP_EXTENSIONS')?></strong></th>
                        <th width="25%"><strong><?php echo $this->config->get('LANG_CURRENT')?></strong></th>
                        <th width="25%"><strong><?php echo $this->config->get('LANG_REQUIRED')?></strong></th>
                        <th width="15%" class="text-center"><strong><?php echo $this->config->get('LANG_STATUS')?></strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>MySQL:</td>
                        <td><?php echo extension_loaded('mysql') ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo extension_loaded('mysql') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>GD:</td>
                        <td><?php echo extension_loaded('gd') ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo extension_loaded('gd') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>cURL:</td>
                        <td><?php echo extension_loaded('curl') ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo extension_loaded('curl') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>mCrypt:</td>
                        <td><?php echo function_exists('mcrypt_encrypt') ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo function_exists('mcrypt_encrypt') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>ZIP:</td>
                        <td><?php echo extension_loaded('zlib') ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo extension_loaded('zlib') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                    <tr>
                        <td>SOAP:</td>
                        <td><?php echo class_exists('SoapClient') ? $this->config->get('LANG_ON') : $this->config->get('LANG_OFF')?></td>
                        <td>Aan</td>
                        <td align="center"><?php echo class_exists('SoapClient') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>

        <p><?php echo $this->config->get('LANG_STEP_2_PART_3')?></p>
        <fieldset>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><strong><?php echo $this->config->get('LANG_FILES')?></th>
                        <th align="center"><strong><?php echo $this->config->get('LANG_STATUS')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): $file = DIR_SUMOSTORE . $file;?>
                    <tr>
                        <td><?php echo $file; ?></td>
                        <td align="center">
                            <?php if (!file_exists($file)) { ?>
                                <span class="bad"><?php echo $this->config->get('LANG_MIA')?></span><img src="view/image/bad.png" alt="Bad">
                            <?php } elseif (!is_writable($file)) { ?>
                                <span class="bad"><?php echo $this->config->get('LANG_FNW')?></span><img src="view/image/bad.png" alt="Bad">
                            <?php } else { ?>
                                <img src="view/image/good.png">
                            <?php } ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </fieldset>

        <p><?php echo $this->config->get('LANG_STEP_2_PART_4')?></p>
        <fieldset>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><strong><?php echo $this->config->get('LANG_DIRECTORIES')?></strong></th>
                        <th align="center"><strong><?php echo $this->config->get('LANG_STATUS')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($directories as $directory): $directory = DIR_SUMOSTORE . $directory;?>
                    <tr>
                        <td><?php echo $directory; ?></td>
                        <td align="center">
                            <?php if (!is_dir($directory)) { ?>
                                <span class="bad"><?php echo $this->config->get('LANG_MIA')?></span><img src="view/image/bad.png" alt="Bad">
                            <?php } elseif (!$check[$directory]) { ?>
                                <span class="bad"><?php echo $this->config->get('LANG_FNW')?></span><img src="view/image/bad.png" alt="Bad">
                            <?php } else { ?>
                                <img src="view/image/good.png">
                            <?php } ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </fieldset>
        <div class="buttons">
            <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $this->config->get('LANG_PREVIOUS_STEP')?></a></div>
            <div class="pull-right">
                <input type="submit" value="<?php echo $this->config->get('LANG_NEXT_STEP')?>" class="btn btn-primary" />
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<?php echo $footer; ?>
