<?php echo $header; ?>

<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?>
    <a href="<?php echo $breadcrumb['href']; ?>">
        <?php echo $breadcrumb['text']; ?>
    </a>
    <?php } ?>
</div>
<div id="pad-wrapper">
    <?php if ($error_warning) { ?>
        <div class="alert alert-warning">
            <i class="icon-warning-sign"></i>
            <?php echo $error_warning; ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-4">
            <h4><img src="img/icons/cats-24.png" alt="" /> <?php echo $heading_title; ?></h4>
        </div>
        <div class="col-md-8">
            <div class="pull-right">
                <a onclick="$('#form').submit();" class="btn btn-sm btn-primary">
                    <?php echo $button_save; ?>
                </a>
                <a href="<?php echo $cancel; ?>" class="btn btn-sm btn-primary">
                    <?php echo $button_cancel; ?>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div class="form-group">
                    <label class="col-md-2 control-label">
                        <?php echo $entry_group?> *
                    </label>
                    <div class="col-md-6">
                        <?php foreach ($languages as $list): ?>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <img src="view/img/flags/<?php echo $list['image']?>" title="<?php echo $list['name']?>" />
                            </span>
                            <input type="text" name="filter_group_description[<?php echo $list['language_id']?>][name]" value="<?php echo isset($filter_group_description[$list['language_id']]) ? $filter_group_description[$list['language_id']]['name'] : ''; ?>" class="form-control" language="<?php echo $list['language_id']?>" />
                        </div>
                        <br />
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="clearfix"><br /></div>
                <table id="filter" class="table list">
                    <thead>
                        <tr>
                            <td style="width: 60%;">
                                <?php echo $entry_name ?> *
                            </td>
                            <td style="width: 10%;">
                                <?php echo $entry_sort_order; ?>
                            </td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $filter_row = 0; ?>
                    <?php foreach ($filters as $filter) { ?>
                        <tr>
                            <td>
                                <input type="hidden" name="filter[<?php echo $filter_row; ?>][filter_id]" value="<?php echo $filter['filter_id']; ?>" />
                            <?php foreach ($languages as $language) { ?>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <img src="view/img/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                                </span>
                                
                                <input type="text" name="filter[<?php echo $filter_row; ?>][filter_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($filter['filter_description'][$language['language_id']]) ? $filter['filter_description'][$language['language_id']]['name'] : ''; ?>" class="form-control"/>
                            </div>
                            <br />
                            <?php if (isset($error_filter[$filter_row][$language['language_id']])) { ?>
                            <span class="error"><?php echo $error_filter[$filter_row][$language['language_id']]; ?></span>
                            <?php } ?>
                            <?php } ?></td>
                            <td>
                                <input type="text" name="filter[<?php echo $filter_row; ?>][sort_order]" value="<?php echo $filter['sort_order']; ?>" size="1" class="form-control" />
                            </td>
                            <td>
                                <a onclick="$(this).parent().parent().remove();" class="btn btn-sm btn-danger">
                                    <i class="icon-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php $filter_row++; ?>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td class="left"><a onclick="addFilter();" class="button"><?php echo $button_add_filter; ?></a></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
var filter_row = <?php echo $filter_row; ?>;

function addFilter() {
    html = '';
	//html  = '<tbody id="filter-row' + filter_row + '">';
	html += '  <tr>';	
    html += '    <td class="left"><input type="hidden" name="filter[' + filter_row + '][filter_id]" value="" />';
	<?php foreach ($languages as $language) { ?>
	html += '<div class="input-group"><span class="input-group-addon"><img src="view/img/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="filter[' + filter_row + '][filter_description][<?php echo $language['language_id']; ?>][name]" value="" class="form-control" /> </div><br />';
    <?php } ?>
	html += '    </td>';
	html += '    <td class="right"><input type="text" name="filter[' + filter_row + '][sort_order]" value="" size="1" class="form-control" /></td>';
	html += '     <td class="left"><a onclick="$(this).parent().parent().remove();" class="btn btn-sm btn-danger"><i class="icon-trash"></i></a></td>';
	html += '  </tr>';	
    //html += '</tbody>';
	
	$('#filter tbody').append(html);
	
	filter_row++;
}
//--></script> 
<?php echo $footer; ?>