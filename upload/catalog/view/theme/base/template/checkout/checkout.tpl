<?php
namespace Sumo;
echo $header?>
<div class="container">
    <?php if (!empty($settings['left']) && count($settings['left'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['left'] as $key => $item) {
            if (!$item || $item == null) {
                unset($settings['left'][$key]);
                continue;
            }
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    $mainClass = 'col-md-12';
    if (!empty($settings['left']) && !empty($settings['right'])) {
        $mainClass = 'col-md-6';
    }
    else if (!empty($settings['left']) || !empty($settings['right'])) {
        $mainClass = 'col-md-9';
    }
    ?>

    <div class="<?php echo $mainClass?>">
        <form method="post" id="form" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo Language::getVar('SUMO_CART_TITLE')?></h1>
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbs as $crumb): ?>
                        <li><?php if (!empty($crumb['href'])) { echo '<a href="' . $crumb['href'] . '">'; } echo $crumb['text']; if (!empty($crumb['href'])) { echo '</a>'; } ?></li>
                        <?php endforeach ?>
                        <li><span id="loader"></span></li>
                    </ol>
                    <div id="alerts" class="hidden"></div>
                    <?php if (isset($this->session->data['error'])): ?>
                    <div class="alert alert-warning"><?php echo $this->session->data['error']; unset($this->session->data['error']); ?></div>
                    <?php endif?>

                    <div id="checkout-holder">
                        <div id="step_2" data-step="2">
                            <div class="block">
                                <div class="header"><?php echo Language::getVar('SUMO_CHECKOUT_STEP_PAYMENT_ADDRESS')?></div>
                                <div class="content"></div>
                            </div>
                        </div>
                        <div id="step_3" data-step="3">
                            <div class="block">
                                <div class="header"><?php echo Language::getVar('SUMO_CHECKOUT_STEP_SHIPPING_ADDRESS')?></div>
                                <div class="content"></div>
                            </div>
                        </div>
                        <div id="step_4" data-step="4">
                            <div class="block">
                                <div class="header"><?php echo Language::getVar('SUMO_CHECKOUT_STEP_DISCOUNT')?></div>
                                <div class="content"></div>
                            </div>
                        </div>
                        <div id="step_5" data-step="5">
                            <div class="block">
                                <div class="header"><?php echo Language::getVar('SUMO_CHECKOUT_STEP_SHIPPING_METHOD')?></div>
                                <div class="content"></div>
                            </div>
                        </div>
                        <div id="step_6" data-step="6">
                            <div class="block">
                                <div class="header"><?php echo Language::getVar('SUMO_CHECKOUT_STEP_PAYMENT_METHOD')?></div>
                                <div class="content"></div>
                            </div>
                        </div>
                        <div id="step_7" data-step="7">
                            <div class="block">
                                <div class="header"><?php echo Language::getVar('SUMO_CHECKOUT_STEP_CONFIRM')?></div>
                                <div class="content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if (isset($settings['right'])): ?>
    <div class="col-md-3">
        <?php foreach ($settings['right'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <?php endif;

    if (isset($settings['bottom'])): ?>
    <div class="col-md-12">
        <?php
        foreach ($settings['bottom'] as $item) {
            echo $item;
        }
        ?>
    </div>
    <div class="clearfix"></div>
    <?php endif ?>
</div>

<script type="text/javascript">
var lastStep = 0;
$(function() {
    $('#checkout-holder > div').each(function() {
        $(this).find('.header').html('<span class="disabled"><a href="#step-' + $(this).data('step') + '" data-step="' + $(this).data('step') + '">' + $(this).find('.header').html() + '</a></span>');
    })
    $('#checkout-holder').on('click', '.header a', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && lastStep >= $(this).data('step')) {
            step($(this).data('step'));
        }
    })

    step(<?php echo empty($this->session->data['force_step']) ? 2 : $this->session->data['force_step']?>);
    $('#step_1').on('click', '.btn', function(e) {
        e.preventDefault();
        step($(this).data('step'));
    })
})
function startLoader() {
    $('#loader').addClass('label label-info').html('<?php echo Language::getVar('SUMO_CATALOG_CHECKOUT_LOADING')?>').show();
}
function endLoader() {
    $('#loader').removeClass('label label-info').hide();
}
function step(go) {
    startLoader();
    $('#checkout-holder > div').each(function() {
        $(this).find('.content').slideUp();
    });
    lastStep = go;
    $('#checkout-holder > div').each(function() {
        if (lastStep >= $(this).data('step')) {
            $(this).find('.header span').removeClass('disabled');
        }
        else {
            $(this).find('.header span').addClass('disabled');
        }
    })
    stepInner(go);
}
function stepInner(go) {
    $.post('checkout/checkout/progress', {data: $('#form').serialize()}, function() {
        if (go == 2 || go == 3) {
            $.post('checkout/checkout/address', {type: go == 2 ? 'payment' : 'shipping'}, function(data) {
                $('#step_' + go + ' .content').html(data);
                $('#step_' + go + ' .content').slideDown(function() {
                    endLoader();
                });
            })
        }
        else if (go == 4) {
            $.post('checkout/checkout/discount', function(data) {
                $('#step_4 .content').html(data);
                $('#step_4 .content').slideDown(function() {
                    endLoader();
                })
            })
        }
        else if (go == 5 || go == 6) {
            $.post('checkout/checkout/method', {type: go == 6 ? 'payment' : 'shipping'}, function(data) {
                $('#step_' + go + ' .content').html(data);
                $('#step_' + go + ' .content').slideDown(function() {
                    endLoader();
                });
            })
        }
        else if (go == 7) {
            $.post('checkout/checkout/confirmcheck', function(data) {
                if (data.step == 7) {
                    $.post('checkout/checkout/confirm', function(data) {
                        $('#step_7 .content').html(data);
                        $('#step_7 .content').slideDown(function(){
                            endLoader();
                        })
                    })
                }
                else {
                    step(data.step);
                }
            }, 'json')
            $('#step_' + go + ' .content').slideDown(function() {
                endLoader();
            });
        }
        else {
        }
    });
}
</script>

<?php echo $footer?>
