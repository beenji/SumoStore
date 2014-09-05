<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" action="" class="form" id="headerform">
            <div class="well column-div">
                <h3><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_ARRANGE')?></h3>
                <div class="row">
                    <div class="col-md-4" class="column-div column-1">
                        <strong class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_PART')?> 1</strong>
                        <input type="radio" name="header[1]" class="column-empty" value="empty" <?php if (isset($settings[1]) && $settings[1] == 'empty') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_EMPTY')?><br />
                        <input type="radio" name="header[1]" class="column-logo" value="logo" <?php if ((isset($settings[1]) && $settings[1] == 'logo') || !isset($settings[1])) { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_LOGO')?><br />
                        <input type="radio" name="header[1]" class="column-search" value="search" <?php if (isset($settings[1]) && $settings[1] == 'search') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCHBAR')?><br />
                        <input type="radio" name="header[1]" class="column-cart" value="cart" <?php if (isset($settings[1]) && $settings[1] == 'cart') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPPING_CART')?>
                    </div>
                    <div class="col-md-4" class="column-div column-2">
                        <strong class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_PART')?> 2</strong>
                        <input type="radio" name="header[2]" class="column-empty" value="empty" <?php if (isset($settings[2]) && $settings[2] == 'empty') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_EMPTY')?><br />
                        <input type="radio" name="header[2]" class="column-logo" value="logo" <?php if (isset($settings[2]) && $settings[2] == 'logo') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_LOGO')?><br />
                        <input type="radio" name="header[2]" class="column-search" value="search" <?php if ((isset($settings[2]) && $settings[2] == 'search') || !isset($settings[2])) { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCHBAR')?><br />
                        <input type="radio" name="header[2]" class="column-cart" value="cart" <?php if (isset($settings[2]) && $settings[2] == 'cart') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPPING_CART')?>
                    </div>
                    <div class="col-md-4" class="column-div column-3">
                        <strong class="help-block"><?php echo Sumo\Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_HEADER_PART')?> 3</strong>
                        <input type="radio" name="header[3]" class="column-empty" value="empty" <?php if (isset($settings[3]) && $settings[3] == 'empty') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_EMPTY')?><br />
                        <input type="radio" name="header[3]" class="column-logo" value="logo" <?php if (isset($settings[3]) && $settings[3] == 'logo') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_LOGO')?><br />
                        <input type="radio" name="header[3]" class="column-search" value="search" <?php if (isset($settings[3]) && $settings[3] == 'search') { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCHBAR')?><br />
                        <input type="radio" name="header[3]" class="column-cart" value="cart" <?php if ((isset($settings[3]) && $settings[3] == 'cart') || !isset($settings[3])) { echo 'checked'; } ?>> <?php echo Sumo\Language::getVar('SUMO_NOUN_SHOPPING_CART')?>
                    </div>
                </div>
            </div>
            <p class="align-right">
                <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>">
            </p>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('#headerform').on('submit', function(e) {
        $('.column-div input').each(function(){
            $(this).prop('disabled', 0);
            //$(this).prop('checked', 0);
            if ($(this).is(':checked') == true) {
                $(this).prop('checked', 1);
            }
            else {
                $(this).prop('checked', 0);
            }
        })
    })
    $('.column-div input').each(function() {
        $(this).on('click', function() {
            if ($(this).val() != 'empty') {
                var value = $(this).val();
                $('.column-' + value).prop('disabled', 1);
                $(this).prop('disabled', 0);
            }
            if ($('.column-logo').is(':checked') == true) {
                $('.column-logo').prop('disabled', 1);
            }
            else {
                $('.column-logo').prop('disabled', 0);
            }
            if ($('.column-search').is(':checked') == true) {
                $('.column-search').prop('disabled', 1);
            }
            else {
                $('.column-search').prop('disabled', 0);
            }
            if ($('.column-cart').is(':checked') == true) {
                $('.column-cart').prop('disabled', 1);
            }
            else {
                $('.column-cart').prop('disabled', 0);
            }
        });

        $(this).on('click2', function(){
            if ($('.column-logo').is(':checked') == true) {
                $('.column-logo').prop('disabled', 1);
            }
            else {
                $('.column-logo').prop('disabled', 0);
            }
            if ($('.column-search').is(':checked') == true) {
                $('.column-search').prop('disabled', 1);
            }
            else {
                $('.column-search').prop('disabled', 0);
            }
            if ($('.column-cart').is(':checked') == true) {
                $('.column-cart').prop('disabled', 1);
            }
            else {
                $('.column-cart').prop('disabled', 0);
            }
        })
    });
    setTimeout(function(){
        $('.column-logo, .column-search, .column-cart').trigger('click2');
    }, 500);
})
</script>
