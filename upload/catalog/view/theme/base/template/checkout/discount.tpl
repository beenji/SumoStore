<div class="col-md-12">
    <div class="col-sm-4">
        <h3><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_COUPON_TITLE')?></h3>
        <div class="col-sm-11">
            <p><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_COUPON_INFO')?></p>
            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_COUPON_CODE')?>:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="discount[coupon]" value="<?php if (isset($this->session->data['discount']['coupon'])) { echo $this->session->data['discount']['coupon']['code']; }?>">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" id="coupon"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_DISCOUNT_CHECK')?></button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_COUPON_RESULT')?>:</label>
                <p><span id="coupon_result"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_COUPON_NONE')?></span></p>
            </div>
        </div>
    </div>
    <?php if ($this->config->get('voucher_enabled')): ?>
    <div class="col-sm-4">
        <h3><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_VOUCHER_TITLE')?></h3>
        <div class="col-sm-11">
            <p><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_VOUCHER_INFO')?></p>
            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_VOUCHER_CODE')?>:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="discount[voucher]" value="<?php if (isset($this->session->data['discount']['voucher'])) { echo $this->session->data['discount']['voucher']['code']; }?>">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" id="voucher"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_DISCOUNT_CHECK')?></button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_VOUCHER_RESULT')?>:</label>
                <p><span id="voucher_result"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_VOUCHER_NONE')?></span></p>
            </div>
        </div>
    </div>
    <?php endif;
    if ($this->config->get('points_value')): ?>
    <div class="col-sm-4">
        <h3><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_REWARD_TITLE')?></h3>
        <div class="col-sm-11">
            <p><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_REWARD_INFO')?></p>
            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_REWARD_USE')?>:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="discount[reward]" value="<?php if (isset($this->session->data['discount']['reward'])) { echo $this->session->data['discount']['reward']; }?>">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" id="reward"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_DISCOUNT_CHECK')?></button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_REWARD_RESULT')?>:</label>
                <p><span id="reward_result"><?php echo Sumo\Language::getVar('SUMO_CHECKOUT_REWARD_CALCULATION', array(intval($this->customer->getRewardPoints()), Sumo\Formatter::currency($this->config->get('points_value')), Sumo\Formatter::currency($this->config->get('points_value') * $this->customer->getRewardPoints())))?></span></p>
            </div>
        </div>
    </div>
    <?php endif?>

    <div class="clearfix"></div>

    <div class="form-group">
        <input type="button" class="btn btn-primary pull-right" onclick="window.step(5);" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONTINUE')?>">
    </div>

    <div class="clearfix"></div>
</div>
<script type="text/javascript">
$(function() {
    <?php if ($this->config->get('points_value')): ?>

    $('input[name="discount[reward]"]').on('change blur keyup', function() {
        startLoader();
        console.log('changed');
        var elem = $(this);
        var elemParent = elem.parent().parent();
        var points = parseInt(elem.val());
        var value = <?php echo $this->config->get('points_value')?>;
        if (points >= 0 && points <= <?php echo intval($this->customer->getRewardPoints())?>) {
            $.post('checkout/checkout/discountcheck', {type: 'reward', amount: points}, function(data) {
                elemParent.removeClass('has-error').addClass('has-success');
                $('#reward_result').html(data.reward['display']);
                endLoader();
            }, 'json');
        }
        else {
            elemParent.addClass('has-error').removeClass('has-success');
            $('#reward_result').html('<?php echo Sumo\Language::getVar('SUMO_CHECKOUT_REWARD_INVALID')?>');
            endLoader();
        }
    })
    $('#reward').on('click', function() {
        $('input[name="discount[reward]"]').trigger('change');
    })
    <?php if (isset($this->session->data['discount']['reward'])): ?>
    $('#reward').trigger('click');
    <?php endif;
    endif?>

    $('input[name="discount[coupon]"]').on('change blur', function() {
        startLoader();
        var elem = $(this);
        var elemParent = elem.parent().parent();
        $.post('checkout/checkout/discountcheck', {type: 'coupon', code: $(this).val()}, function(data) {
            endLoader();
            if (data.coupon) {
                elemParent.removeClass('has-error').addClass('has-success');
                $('#coupon_result').html(data.coupon['display']);
            }
            else {
                elemParent.removeClass('has-success').addClass('has-error');
                $('#coupon_result').html('<?php echo Sumo\Language::getVar('SUMO_CHECKOUT_COUPON_INVALID')?>')
            }
        }, 'json');
    })
    $('#coupon').on('click', function() {
        $('input[name="discount[coupon]"]').trigger('change');
    })
    <?php if (isset($this->session->data['discount']['coupon'])): ?>
    $('#coupon').trigger('click');
    <?php endif ?>

    $('input[name="discount[voucher]"]').on('change blur', function() {
        startLoader();
        var elem = $(this);
        var elemParent = elem.parent().parent();
        $.post('checkout/checkout/discountcheck', {type: 'voucher', code: $(this).val()}, function(data) {
            endLoader();
            if (data.voucher) {
                elemParent.removeClass('has-error').addClass('has-success');
                $('#voucher_result').html(data.voucher['display']);
            }
            else {
                elemParent.removeClass('has-success').addClass('has-error');
                $('#voucher_result').html('<?php echo Sumo\Language::getVar('SUMO_CHECKOUT_VOUCHER_INVALID')?>')
            }
        }, 'json');
    })
    $('#voucher').on('click', function() {
        $('input[name="discount[voucher]"]').trigger('change');
    })
    <?php if (isset($this->session->data['discount']['voucher'])): ?>
    $('#voucher').trigger('click');
    <?php endif ?>
})
</script>
