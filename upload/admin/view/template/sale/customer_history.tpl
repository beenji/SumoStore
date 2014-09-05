<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<table class="table table-striped">
    <thead>
        <tr>
            <td><?php echo $column_date_added; ?></td>
            <td><?php echo $column_comment; ?></td>
        </tr>
    </thead>
    <tbody>
        <?php if ($histories) { ?>
        <?php foreach ($histories as $history) { ?>
        <tr>
            <td><?php echo $history['date_added']; ?></td>
            <td><?php echo $history['comment']; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
            <td class="center" colspan="2"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<div class="pagination"><?php echo $pagination; ?></div>
