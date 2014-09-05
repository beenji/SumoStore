                </div>
            </div>
        </div>
        <nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right side-chat">
            <div class="header">
                <h3>Help</h3>
            </div>
            <div class="sub-header" href="#">
                <div class="icon"><i class="fa fa-user"></i></div> <p>Online (4)</p>
            </div>
            <div class="content">
                <p class="title">Family</p>
                <ul class="nav nav-pills nav-stacked contacts">
                    <li class="online"><a href="#"><i class="fa fa-circle-o"></i> Michael Smith</a></li>
                    <li class="online"><a href="#"><i class="fa fa-circle-o"></i> John Doe</a></li>
                    <li class="online"><a href="#"><i class="fa fa-circle-o"></i> Richard Avedon</a></li>
                    <li class="busy"><a href="#"><i class="fa fa-circle-o"></i> Allen Collins</a></li>
                </ul>

                <p class="title">Friends</p>
                <ul class="nav nav-pills nav-stacked contacts">
                    <li class="online"><a href="#"><i class="fa fa-circle-o"></i> Jaime Garzon</a></li>
                    <li class="outside"><a href="#"><i class="fa fa-circle-o"></i> Dave Grohl</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Victor Jara</a></li>
                </ul>

                <p class="title">Work</p>
                <ul class="nav nav-pills nav-stacked contacts">
                    <li><a href="#"><i class="fa fa-circle-o"></i> Ansel Adams</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Gustavo Cerati</a></li>
                </ul>

            </div>
        </nav>
        <script type="text/javascript">
            window.ParsleyConfig = {
              i18n: {
                <?php echo Sumo\Language::getVar('SUMO_LOCALE_ISO_639_1'); ?>: {
                    type: {
                        email:      '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_EMAIL'); ?>',
                        url:        '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_URL'); ?>',
                        number:     '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_NUMBER'); ?>',
                        integer:    '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_NUMBER'); ?>',
                        digits:     '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_NUMBER'); ?>',
                        alphanum:   '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_ALPHANUM'); ?>'
                    },
                    minlength:      '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_MINLENGTH'); ?>',
                    maxlength:      '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_MAXLENGTH'); ?>',
                    length:         '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_LENGTH'); ?>',
                    required:       '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_REQUIRED'); ?>',
                    notblank:       '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_NOTBLANK'); ?>',
                    pattern:        '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_PATTERN'); ?>',
                    min:            '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_MIN'); ?>',
                    max:            '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_MAX'); ?>',
                    range:          '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_RANGE'); ?>',
                    mincheck:       '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_MINCHECK'); ?>',
                    maxcheck:       '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_MAXCHECK'); ?>',
                    check:          '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_CHECK'); ?>',
                    equalto:        '<?php echo Sumo\Language::getVar('SUMO_ERROR_INPUT_EQUALTO'); ?>'
                }
              }
            };
        </script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/bootstrap/bootstrap.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/bootstrap/bootstrap.bootbox.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/bootstrap/bootstrap.switch.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/bootstrap/bootstrap.datetimepicker.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.icheck.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.gritter.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.nanoscroller.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.parsley.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.maskedinput.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.redactor.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/jquery/jquery.pushmenu.js"></script>
        <script src="<?php echo str_replace('http:', '', HTTP_STYLE_BASE)?>admin/view/js/core.js"></script>

        <?php foreach ($scripts as $script) {
            if (stristr($script, '//') == true) {
                echo '<script src="' . $script . '"></script>';
            }
            else {
                echo '<script src="'. str_replace('http:', '', HTTP_STYLE_BASE) . 'admin/' . $script . '"></script>';
            }
        }
        ?>
        <script type="text/javascript">
            <?php if (!defined('DEVELOPMENT')): ?> $("#debug_enabled").hide(); <?php endif ?>

            window.ParsleyValidator.setLocale('<?php echo Sumo\Language::getVar('SUMO_LOCALE_ISO_639_1'); ?>');
            $('.date-picker').datetimepicker({
                format: '<?php echo Sumo\Formatter::dateFormatToJS(); ?>',
                autoclose: true,
                minView: 2,
                maxView: 2
            });
            $('.birthdate-picker').datetimepicker({
                format: '<?php echo Sumo\Formatter::dateFormatToJS(); ?>',
                autoclose: true,
                minView: 2,
                maxView: 4,
                startView: 4
            });
            <?php if (!file_exists(DIR_HOME . 'apps/notifications/information.php')) { ?>
            $('#notifications').parent().parent().find('.foot').hide();
            function getNotifications() {
                $.getJSON('<?php echo $this->url->link('common/home/notifications', 'token=' . $this->session->data['token'], 'SSL')?>', function(data) {
                    $('#notifications').html('');
                    $('#notifications-bubble').html('0');
                    $.each(data, function(index, item) {
                        $('#notifications').append('<li><a href="' + item.url + '">' + item.text + '</a></li>');
                        $('#notifications-bubble').html(parseFloat($('#notifications-bubble').html()) + 1);
                    })
                })
            }
            setInterval(getNotifications, 60000);
            getNotifications();
            <?php } ?>
        </script>
    </body>
</html>

<?php /*
        $('#sumoguard_notifications_count').html(0);
        $('#sumoguard_notifications').html('');
        var warning = 0;
        $.getJSON('common/sumoguard/notifications?token=<?php echo $token?>', function(data){
            if (data.count >= 1) {
                $('#sumoguard_notifications_count').html(data.count);
            }
            else {
                $('#sumoguard_notifications_count').hide();
            }
            $('#sumoguard_notifications').html('<h3>' + data.count_text + '</h3>');
            $.each(data.notifications, function(key, item){
                var html = '<a href="';
                if (item.url) {
                    html += item.url;
                }
                else {
                    html += '#';
                }
                html += '" class="item"><i class="';
                if (item.icon) {
                    if (item.icon == 'icon-unlock') {
                        warning++;
                    }
                    html += item.icon;
                }
                else {
                    html += 'icon-warning-sign';
                }
                html += '"></i> ' + item.message;
                if (item.time) {
                    html += ' <span class="time"><i class="icon-time"></i> ' + item.time + '</span><div class="clearfix"></div>';
                }
                html += '</a>';
                $('#sumoguard_notifications').append(html);
            });

            $('#sumoguard_notifications_count').parent().on('click', function() {
                $.post('common/sumoguard/markasread?token=<?php echo $token?>', {}, function(data){ });
                var newCount = '0';
                if (warning) {
                    newCount = warning;
                }
                $('#sumoguard_notifications_count').html(newCount);
            })
        })
    });
*/?>
