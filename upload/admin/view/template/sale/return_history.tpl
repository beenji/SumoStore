<?php if ($error) { ?>
<div class="warning"><?php echo $error; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<table class="table table-striped">
    <thead>
        <tr>
            <td><b><?php echo $column_date_added; ?></b></td>
            <td><b><?php echo $column_comment; ?></b></td>
            <td><b><?php echo $column_status; ?></b></td>
            <td><b><?php echo $column_notify; ?></b></td>
        </tr>
    </thead>
    <tbody>
    <?php if ($histories) { ?>
        <?php foreach ($histories as $history) { ?>
        <tr>
            <td><?php echo $history['date_added']; ?></td>
            <td><?php echo $history['comment']; ?></td>
            <td><?php echo $history['status']; ?></td>
            <td><?php echo $history['notify']; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
            <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="pagination"><?php echo $pagination; ?></div>
