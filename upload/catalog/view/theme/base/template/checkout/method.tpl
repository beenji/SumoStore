<?php
if (!empty($warning)) {
    echo '<div class="alert alert-warning checkout-warning">' . $warning . '</div>';
}
?>
    <div class="col-md-12" id="<?php echo $type?>_method">
        <p><?php if (isset($info)): echo $info; endif; ?></p>
        <?php if (isset($methods)): ?>
        <table class="table no-border checkout-method-table">
            <tbody class="no-border-y checkout-method-tbody">
                <?php foreach ($methods as $method): foreach ($method['options'] as $key => $option): ?>
                <tr>
                    <td valign="top">
                        <input type="radio" name="<?php echo $type?>_method" required value="<?php $value = $method['list_name'] . '.' . $key; echo $value;?>" <?php if (isset($this->session->data[$type]['method']) && $this->session->data[$type]['method'] == $value) { echo 'checked'; }?>>
                    </td>
                    <td valign="top">
                        <?php echo isset($option['name']) ? $option['name'] : $method['name']?>
                    </td>
                    <td valign="top">
                        <?php echo isset($option['description']) ? $option['description'] : $method['description']?>
                    </td>
                    <td valign="top" class="text-right">
                        <?php echo !empty($option['rate']) ? $option['rate'] : '';?>
                    </td>
                </tr>
                <?php if (defined('DEVELOPMENT')): ?>
                <tr class="method-debug hidden">
                    <td colspan="4"><pre><?php echo print_r($method,true)?></pre></td>
                </tr>
                <?php endif; endforeach; endforeach ?>
            </tbody>
        </table>
        <?php endif ?>

        <div class="form-group">
            <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_COMMENT') ?></label>
            <textarea name="comment" class="form-control"><?php if (isset($this->session->data['comment'])): echo $this->session->data['comment']; endif; ?></textarea>
        </div>
        <div class="col-md-4 col-md-offset-8 pull-right text-right">
            <?php if (defined('DEVELOPMENT')) { ?> <a href="#toggle_debug" onclick="$('.method-debug').removeClass('hidden').toggle(); return false;">Show debug</a> <?php } ?>
            <input type="button" value="<?php echo Sumo\Language::getVar('BUTTON_CONTINUE') ?>" id="button-<?php echo $type?>-method" class="btn btn-primary btn-checkout-continue" />
        </div>
    </div>

    <div class="clearfix"></div>
<script type="text/javascript">
$(function() {
    <?php if (defined('DEVELOPMENT')) { ?>
    $('.method-debug').removeClass('hidden').hide();
    <?php } ?>
    $('#<?php echo $type?>_method').on('click', '.btn', function() {
        var elem = $('input[name="<?php echo $type?>_method"]:checked');
        if (elem.val() && elem.val().length) {
            step(<?php echo $type == 'shipping' ? 6 : 7?>);
        }
    })
})
