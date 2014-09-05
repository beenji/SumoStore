<?php
echo $header
?>
<div class="col-md-4 col-md-offset-8 page-head-actions align-right settingstable">
    <div class="btn-group align-left">
        <?php
        foreach ($languages as $list):
            if ($list['is_default']):
        ?>
        <button class="btn btn-primary dropdown-toggle" id="language-selector-btn" data-toggle="dropdown" type="button"><span><img src="view/img/flags/<?php echo $list['image']; ?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></span>&nbsp; <span class="caret"></span></button>
        <?php
                break;
            endif;
        endforeach; ?>
        <ul class="dropdown-menu pull-right" id="language-selector">
            <?php foreach ($languages as $list): ?>
            <li><a href="#other-language" data-lang-id="<?php echo $list['language_id']; ?>"><img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />&nbsp; &nbsp;<?php echo $list['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php
// Always enable multi-site settings, but it's kinda useless to show 'm if there is only one store..
if (count($stores)): ?>
<ul class="nav nav-tabs">
    <?php foreach ($stores as $list): ?>
    <li class="<?php if ($list['store_id'] == $current_store){ echo 'active'; } ?>">
        <a href="<?php echo $this->url->link('app/newsletterbasic', 'token=' . $this->session->data['token'] . '&store_id=' . $list['store_id'], 'SSL')?>">
            <?php echo $list['name']?>
        </a>
    </li>
    <?php endforeach?>
</ul>
<?php
endif;
?>

<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" id="newsletter-form" data-parsley-validate>
            <input type="hidden" name="store_id" value="<?php echo $current_store?>">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER')?></label>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="filter" value="1"> <?php echo Sumo\Language::getVar('SUMO_NOUN_YES')?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="filter" value="0"> <?php echo Sumo\Language::getVar('SUMO_NOUN_NO')?>
                        </label>
                    </div>
                </div>
                <div class="filter-hide filter-1">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL')?></label>
                        <div>
                            <label class="radio-inline">
                                <input type="radio" name="mail_type" value="1"> <?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL_ALL_CUSTOMERS')?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="mail_type" value="0" checked> <?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL_ALL_MEMBERS')?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_FROM_COUNTRY')?></label>
                        <select name="country" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_ALL_COUNTRIES')?></option>
                            <?php foreach ($countries as $list): ?>
                            <option value="<?php echo $list['country_id']?>"><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_GENDER')?></label>
                        <select name="gender" class="form-control">
                            <option value="b"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_GENDER_BOTH')?></option>
                            <option value="m"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_MALE')?></option>
                            <option value="f"><?php echo Sumo\Language::getVar('SUMO_NOUN_GENDER_FEMALE')?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_AGE')?></label>
                        <select name="age" class="form-control">
                            <option value="0"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_AGE_ALL')?></option>
                            <option>0-18</option>
                            <option>18-25</option>
                            <option>25-35</option>
                            <option>35-50</option>
                            <option>55+</option>
                        </select>
                    </div>
                </div>
                <div class="filter-hide filter-0">
                    <div class="form-group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL')?></label>
                        <select name="to" class="form-control">
                            <option value="newsletter"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL_ALL_MEMBERS')?></option>
                            <option value="customer_all"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL_ALL_CUSTOMERS')?></option>
                            <option value="customer_group"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_TYPE_MAIL_CUSTOMER_GROUP')?></option>
                        </select>
                    </div>
                    <div class="form-group customer_group">
                        <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_FILTER_CUSTOMER_GROUP')?></label>
                        <select name="customer_group_id" class="form-control">
                            <?php foreach ($customer_groups as $list): ?>
                            <option value="<?php echo $list['customer_group_id']?>"><?php echo $list['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_RECEIVERS')?></label>
                    <span class="help-block" id="receivers"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_RECEIVERS_AMOUNT_NONE')?></span>
                </div>
                <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <input type="submit" class="btn btn-primary" value="<?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_SEND')?>" />
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_SUBJECT')?></label>
                    <?php foreach ($languages as $list): ?>
                    <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <input name="mail[<?php echo $list['language_id']?>][subject]" class="form-control" <?php if (isset($mail[$list['language_id']]['instructions'])) { echo $mail[$list['language_id']]['instructions']; } ?> placeholder="<?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_SUBJECT_PLACEHOLDER')?>" required />
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_MESSAGE')?></label>
                    <?php foreach ($languages as $list): ?>
                    <div class="input-group lang-block lang-<?php echo $list['language_id']?><?php if ($list['is_default']): ?> lang-active<?php endif; ?>">
                        <span class="input-group-addon">
                            <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                        </span>
                        <textarea name="mail[<?php echo $list['language_id']?>][message]" class="form-control redactor-newsletter" rows="30" required placeholder="<?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_MESSAGE_PLACEHOLDER')?>"><?php echo htmlentities('<html>
<head>
    <link rel="stylesheet" type="text/css" href="{url}admin/view/css/bootstrap/bootstrap.css">
</head>
<body>
    <div class="hidden for-text-preview-only" style="display:none;">U leest de allereerste nieuwsbrief van {name}. Lees dit bericht in uw e-mail client zodat u de rest van de nieuwsbrief kunt lezen.</div>
    <div class="hidden">Ziet u deze e-mail zonder opmaak? Voeg ons dan toe aan uw contactpersonen zodat u de opmaak wel ziet of klik op de melding van uw e-mail client.</div>
    <div class="container">
        <div class="well"><h1>De allereerste nieuwsbrief!</h1></div>
        <div class="row">
            <div class="col-sm-8 col-xs-12">
                <h3>Titel van het eerste bericht</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer lacinia, urna ut congue gravida, mauris erat iaculis arcu, vitae varius metus lorem at nibh. Proin ac urna ipsum. Cras dictum iaculis sodales. Quisque ultricies congue sagittis. Vivamus eu aliquam purus, eget tincidunt tellus. Phasellus et justo non lectus pretium semper a et nisi. Maecenas ut commodo purus. In euismod sagittis lorem, a cursus tortor blandit nec. Duis id velit in sapien aliquam suscipit. Pellentesque dignissim nibh leo, non cursus ante consectetur sed. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>
                <p>Vestibulum enim purus, auctor eget cursus vitae, imperdiet eu nisi. Proin sit amet lacinia magna. Ut quis dui erat. Donec vel laoreet justo. Cras metus leo, cursus eu velit a, fringilla vulputate dolor. Etiam vel turpis vitae nulla imperdiet bibendum a a velit. Nullam consequat feugiat libero, eu eleifend massa condimentum at. Aenean auctor, turpis vel faucibus lacinia, leo dolor blandit lectus, aliquet consequat magna urna vel libero. Praesent egestas velit quis turpis cursus sollicitudin a quis lectus. Sed turpis lacus, porta a gravida at, luctus sit amet turpis. Praesent eros magna, gravida nec nisl ut, interdum viverra augue. Pellentesque lobortis, nibh ut rutrum convallis, sem risus elementum neque, non convallis nibh metus non quam. Proin mollis consectetur sodales. Nunc volutpat nisl lorem, iaculis pretium erat vestibulum eu.</p>
                <p>Nullam fringilla magna et nisi malesuada, quis bibendum magna auctor. Duis ornare tempus libero tincidunt ornare. Suspendisse consectetur molestie metus. Proin pellentesque dui nec vehicula euismod. Nullam non convallis ligula. Aenean fringilla commodo odio, quis aliquam turpis cursus vel. Vestibulum a tristique ante, eget hendrerit tortor. Ut aliquet arcu lectus, in malesuada est pharetra et. Donec dignissim, quam vitae viverra fringilla, felis mi tincidunt nisl, sed viverra justo elit in erat. Maecenas volutpat magna nibh, et molestie mi tempor a. Nulla id metus dolor. Etiam et ante lobortis erat cursus lacinia. Vestibulum volutpat magna orci, sit amet tristique lectus adipiscing ut. </p>
            </div>
            <div class="col-sm-4 col-xs-12">
                <h3>Een andere titel</h3>
                <p>Beste {firstname} {lastname},<br />U ontvangt deze nieuwsbrief omdat u zich hiervoor heeft ingeschreven. We willen u graag op de hoogte brengen van nieuwe producten die onze winkel sinds kort aanbied en hopen dat u hierin geïnteresseerd bent. Mocht dit niet het geval zijn, kunt u dit via de link onderin de email aanpassen en bieden wij u onze excuses aan.</p>
            </div>
        </div>
        <div class="col-md-12 text-center">
        &copy; 2014 - {name} - <a href="{url}account/newsletter">Nieuwsbrief instellingen</a>
        </div>
    </div>
</body>
</html>', ENT_QUOTES, 'UTF-8');?></textarea>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>

<div class="hidden">
    <div id="newsletter-dialog">
        <h3><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_DIALOG_TITLE')?></h3>
        <p><?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_DIALOG_MESSAGE')?></p>
        <div id="progress">
        </div>
    </div>
</div>

<script type="text/javascript">
var sessionToken    = '<?php echo $this->session->data['token']?>';
var labelCancel     = '<?php echo Sumo\Language::getVar('SUMO_NOUN_CANCEL')?> / <?php echo Sumo\Language::getVar('SUMO_NOUN_EDIT')?>';
var labelTest       = '<?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_SEND_TEST_MAIL')?>';
var labelConfirm    = '<?php echo Sumo\Language::getVar('APP_NEWSLETTERBASIC_SEND_MAIL')?>';
</script>
<?php echo $footer?>
