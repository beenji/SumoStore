<?php echo $header ?>

<form action="<?php echo $this->url->link('settings/emails/remove', 'token=' . $this->session->data['token'], 'SSL') ?>" method="post" id="selectedItemListener">
    <div class="block-flat">
        <?php if ($emails): ?>
        <table class="table no-border list">
            <thead class="no-border">
                <tr>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_NAME')?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_TITLE')?></strong></th>
                    <th><strong><?php echo Sumo\Language::getVar('SUMO_ADMIN_STATUS_MAILS_EVENT')?></strong></th>
                    <th style="width: 185px;"></th>
                </tr>
            </thead>
            <tbody class="no-border-y">
                <?php foreach ($emails as $list): ?>
                <tr>
                    <td>
                        <?php echo $list['name']?>
                    </td>
                    <td>
                        <?php
                        if (!empty($list['lang'][$this->config->get('language_id')]['title'])) {
                            echo $list['lang'][$this->config->get('language_id')]['title'];
                        }
                        else {
                            $echod = false;
                            foreach ($list['lang'] as $lang => $content) {
                                if (!empty($content['title']) && !$echod) {
                                    echo $content['title'];
                                    $echod = true;
                                }
                            }
                            if (!$echod) {
                                echo Sumo\Language::getVar('SUMO_NOUN_EMPTY');
                            }
                        }
                        ?>
                    <td>
                        <?php echo $list['event_key']?>
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            <a href="<?php echo $this->url->link('settings/emails/update', 'token=' . $this->session->data['token'] . '&mail_id=' . $list['mail_id'], 'SSL')?>" class="btn btn-sm btn-secondary"><?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT') ?></a>
                            <a href="#preview-mail" mail="<?php echo $list['mail_id']?>" class="btn btn-sm btn-primary preview-url"><?php echo Sumo\Language::getVar('SUMO_BUTTON_PREVIEW_MAIL') ?></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>


        <?php else: ?>
        <p class="well"><?php echo Sumo\Language::getVar('SUMO_NOUN_NO_EMAILS') ?></p>
        <?php endif ?>

        <hr>

        <div class="table-padding">
            <?php if ($emails): ?>
            <div class="btn-group pull-left">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"><?php echo Sumo\Language::getVar('SUMO_NOUN_WITH_SELECTED') ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?php echo $this->url->link('settings/emails/remove', 'token=' . $this->session->data['token'], 'SSL') ?>" rel="selectedItemTrigger" data-message="<?php echo Sumo\Language::getVar('SUMO_NOUN_SHOP_CONFIRM_DELETE') ?>"><?php echo Sumo\Language::getVar('SUMO_NOUN_DELETE') ?></a></li>
                </ul>
            </div>
            <?php endif ?>
            <div class="btn-group pull-right">
                <a href="<?php echo $this->url->link('settings/emails/update', 'token=' . $this->session->data['token'], 'SSL') ?>" class="btn btn-sm btn-primary"><?php echo Sumo\Language::getVar('SUMO_NOUN_EMAIL_NEW'); ?></a>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
</form>

<script type="text/javascript">
$(function(){
    $('.preview-url').on('click', function(e) {
        e.preventDefault();
        $.post('<?php echo $this->url->link('settings/emails/preview', 'token=' . $this->session->data['token'], 'SSL')?>&mail_id=' + $(this).attr('mail'), function(html) {
            bootbox.dialog({
                message: html
            })
        });
    })
})
</script>

<?php echo $footer ?>
