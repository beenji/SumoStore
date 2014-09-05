<?php if ($reviews) {
    $count = 0;
    foreach ($reviews as $review) { ?>
    <div class="block">
        <div class="header"><?php echo $review['date_added']?> - <strong><?php echo $review['author']?></strong><span class="help-block"><img src="catalog/view/theme/<?php echo $this->config->get('template')?>/image/stars/stars1-<?php echo $review['rating'] . '.png'; ?>" alt="<?php echo $review['rating']; ?>" /> </span></div>
        <div class="content">
            <?php echo htmlentities(strip_tags(html_entity_decode($review['text'])))?>
        </div>
    </div>
    <?php
    $count++;
    if ($count < count($reviews)): echo '<hr />'; endif;
    } ?>
    <div class="pagination"><?php echo $pagination; ?></div>
<?php } else { ?>
    <div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>

<hr />
<?php if (!$this->customer->isLogged()):
echo Sumo\Language::getVar('SUMO_ACCOUNT_REQUIRED_TO_REVIEW', $this->url->link('account/account', '', 'SSL'));
else: ?>
<a href="#write-review" id="write-review"><?php echo Sumo\Language::getVar('SUMO_NOUN_REVIEW_WRITE') ?></a>
<form method="post" action="" id="review-form">
    <div class="form-group">
        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_NAME')?></label>
        <input type="text" name="name" value="" class="form-control" required />
    </div>
    <div class="form-group">
        <label class="control-group"><?php echo Sumo\Language::getVar('SUMO_NOUN_RATING')?></label>
        <div class="form-control">
            <span><?php echo Sumo\Language::getVar('SUMO_NOUN_RATING_WORST') ?></span>&nbsp;
            <input type="radio" name="rating" value="1" />
            &nbsp;
            <input type="radio" name="rating" value="2" />
            &nbsp;
            <input type="radio" name="rating" value="3" />
            &nbsp;
            <input type="radio" name="rating" value="4" />
            &nbsp;
            <input type="radio" name="rating" value="5" />
            &nbsp;<span><?php echo Sumo\Language::getVar('SUMO_NOUN_RATING_BEST') ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"><?php echo Sumo\Language::getVar('SUMO_NOUN_REVIEW_DESCRIPTION')?></label>
        <textarea name="text" cols="40" rows="8" class="form-control"></textarea>
        <span class="help-block"><?php echo Sumo\Language::getVar('SUMO_NOUN_REVIEW_DESCRIPTION_NOTE') ?></span>
    </div>
    <input type="hidden" name="captcha" value="" />
    <div class="form-group">
        <label class="control-label">&nbsp;</label>
        <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_CONTINUE')?>">
    </div>
</form>
<script type="text/javascript">
$(function() {

    $('#review-form').hide();
    $('#write-review').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $('#review-form').slideDown();
    })

    $('#review-form').on('submit', function(e) {
        e.preventDefault();
        var values = $('#review-form').serialize();
        $('#review-form :input').addClass('disabled').prop('disabled', 1);
        $.post('?route=product/product/write&addreview=true&product_id=<?php echo $this->request->get['product_id']?>', {data: values}, function(data) {
            if (data.success) {
                $('#review-form').slideUp(function() {
                    alert(data.success);
                })
            }
            else {
                $('#review-form :input').removeClass('disabled').prop('disabled', 0);
                alert(data.error);
            }
        }, 'json');
    })
})
</script>
<?php endif ?>
