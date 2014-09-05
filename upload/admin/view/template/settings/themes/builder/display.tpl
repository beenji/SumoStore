<div class="tab-content">
    <div class="tab-pane active cont">
        <form method="post" action="" class="form">
            <div class="row">
                <div class="col-md-8" style="min-width: 850px;">
                    <div clas="row">
                        <div class="col-md-12" style="width: 760px;">
                            <img src="http://puu.sh/8jmbN/4d41d1fe6d.png" />
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <hr />
                    </div>
                    <div clas="row">
                        <div class="col-md-12">
                            <h3>[kies uw layout]</h3>
                            <div class="form-group layout-choose">
                                <a href="#layout-12">[12]</a> - <a href="#layout-48">[4-8]</a> - <a href="#layout-363">[3-6-3]</a> - <a href="#layout-84">[8-4]</a>
                            </div>
                        </div>
                        <div class="col-md-12" id="layout">
                            <?php /** layout: common/home **/ ?>
                            <div class="col-md-12">
                                <div class="well column-container">
                                    <div class="row"><div class="col-md-12"><div class="well widget-container"><div class="row"></div></div></div></div>
                                    <div class="row"><div class="col-md-4"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-8"><div class="well widget-container"><div class="row"></div></div></div></div>
                                    <div class="row"><div class="col-md-6"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-6"><div class="well widget-container"><div class="row"></div></div></div></div>
                                    <div class="row"><div class="col-md-12"><div class="well widget-container"><div class="row"></div></div></div></div>
                                    <div class="row"><div class="col-md-6"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-6"><div class="well widget-container"><div class="row"></div></div></div></div>
                                    <div class="row"><div class="col-md-8"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-4"><div class="well widget-container"><div class="row"></div></div></div></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 text-center">
                                    <a href="#add-column"><i class="fa fa-plus-circle fa-4x"></i></a>
                                </div>
                            </div>
                            <?php /** end layout: common/home **/ ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <hr />
                    </div>
                    <div clas="row">
                        <div class="col-md-12">
                            <img src="http://puu.sh/8jmgF/59ba88f86e.png" />
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">[current layout]</label>
                        <select class="form-control" id="layoutswitch">
                            <option>common/home</option>
                            <option>product/category</option>
                            <option>product/product</option>
                            <option>account/</option>
                            <option>information/</option>
                            <option>checkout/cart</option>
                            <option>checkout/checkout</option>
                        </select>
                    </div>
                    <hr />
                    <?php if (is_array($widgets) && count($widgets)): ?>
                    <ul>
                        <?php foreach ($widgets as $widget): ?>
                        <li><?php echo $widget['name']?></li>
                        <?php endforeach ?>
                    </ul>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <?php echo Sumo\Language::getVar('SUMO_ADMIN_THEMES_NO_WIDGETS_AVAILABLE')?>
                    </div>
                    <?php endif ?>
                </div>
            </div>
        </form>
    </div>
</div>
<style type="text/css">
.well.column-container {
    border: 1px dashed #E5E5E5;
    background: #FFF;
}
.widget-container {
    border: 1px solid #E3E3E3;
}

</style>
<script type="text/javascript">
$(function(){
    $('.layout-choose a').on('click', function (e) {
        e.preventDefault();
        var layout = $(this).attr('href');
        bootbox.confirm('[weet u zeker dat u de huidige layout wilt wijzigen? hiermee worden bestaande widgets verwijderd!]', function(result){
            if (result) {
                switch (layout) {
                    case '#layout-12':
                        var skeleton = '<div class="col-md-12">';
                                skeleton += '<div class="well column-container">';
                                    //skeleton += '<div class="row"></div>';
                                skeleton += '</div>';
                                skeleton += '<div class="row">';
                                    skeleton += '<div class="col-md-4 col-md-offset-4 text-center">';
                                        skeleton += '<a href="#add-column"><i class="fa fa-plus-circle fa-4x"></i></a>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                            skeleton += '</div>';
                        $('#layout').html(skeleton);
                        break;

                    case '#layout-48':
                        var skeleton = '<div class="col-md-12">';
                                skeleton += '<div class="col-md-4">';
                                    skeleton += '<div class="well widget-container">';
                                        skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                                skeleton += '<div class="col-md-8">';
                                    skeleton += '<div class="well column-container">';
                                        //skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                    skeleton += '<div class="row">';
                                        skeleton += '<div class="col-md-4 col-md-offset-4 text-center">';
                                            skeleton += '<a href="#add-column"><i class="fa fa-plus-circle fa-4x"></i></a>';
                                        skeleton += '</div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                            skeleton += '</div>';
                        $('#layout').html(skeleton);
                        break;

                    case '#layout-363':
                        var skeleton = '<div class="col-md-12">';
                                skeleton += '<div class="col-md-3">';
                                    skeleton += '<div class="well widget-container">';
                                        skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                                skeleton += '<div class="col-md-6">';
                                    skeleton += '<div class="well column-container">';
                                        //skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                    skeleton += '<div class="row">';
                                        skeleton += '<div class="col-md-4 col-md-offset-4 text-center">';
                                            skeleton += '<a href="#add-column"><i class="fa fa-plus-circle fa-4x"></i></a>';
                                        skeleton += '</div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                                skeleton += '<div class="col-md-3">';
                                    skeleton += '<div class="well widget-container">';
                                        skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                            skeleton += '</div>';
                        $('#layout').html(skeleton);
                        break;

                    case '#layout-84':
                        var skeleton = '<div class="col-md-12">';
                                skeleton += '<div class="col-md-8">';
                                    skeleton += '<div class="well column-container">';
                                        //skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                    skeleton += '<div class="row">';
                                        skeleton += '<div class="col-md-4 col-md-offset-4 text-center">';
                                            skeleton += '<a href="#add-column"><i class="fa fa-plus-circle fa-4x"></i></a>';
                                        skeleton += '</div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                                skeleton += '<div class="col-md-4">';
                                    skeleton += '<div class="well widget-container">';
                                        skeleton += '<div class="row"></div>';
                                    skeleton += '</div>';
                                skeleton += '</div>';
                            skeleton += '</div>';
                        $('#layout').html(skeleton);
                        break;
                }
            }
        })
    })

    $('#layout').on('click', 'a[href="#add-column"]', function(e) {
        e.preventDefault();
        bootbox.dialog({
            message: '[kies de gewenste indeling]',
            buttons: {
                one: {
                    label: '12',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.column-container').append('<div class="row"><div class="col-md-12"><div class="well widget-container"><div class="row"></div></div></div></div>');
                    }
                },
                two: {
                    label: '4-8',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.column-container').append('<div class="row"><div class="col-md-4"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-8"><div class="well widget-container"><div class="row"></div></div></div></div>');
                    }
                },
                three: {
                    label: '6-6',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.column-container').append('<div class="row"><div class="col-md-6"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-6"><div class="well widget-container"><div class="row"></div></div></div></div>');
                    }
                },
                four: {
                    label: '8-4',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.column-container').append('<div class="row"><div class="col-md-8"><div class="well widget-container"><div class="row"></div></div></div><div class="col-md-4"><div class="well widget-container"><div class="row"></div></div></div></div>');
                    }
                },
                cancel: {
                    label: 'Annulleren',
                    className: 'btn-primary',
                    callback: function () {
                        // nothing
                    }
                },
            }
        })
    });
});
</script>
