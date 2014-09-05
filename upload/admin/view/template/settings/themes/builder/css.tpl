<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" action="" class="form">
            <h3><?php echo Sumo\Language::getVar('SUMO_ADMIN_THEMES_TAB_CSS')?></h3>
            <textarea name="css" class="form-control allow-tab" cols="*" rows="20"><?php echo $css ?></textarea>
            <div class="align-right">
                <br />
                <input type="submit" value="<?php echo Sumo\Language::getVar('SUMO_NOUN_SAVE')?>" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>
