<div class="col-md-4">
    <div id="header-search">
        <form method="get" action="<?php echo $this->url->link('product/search')?>">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" name="search" placeholder="<?php echo Sumo\Language::getVar('SUMO_NOUN_SEARCH_PLURAL')?>" class="form-control" id="search-input" autocomplete="off">
                    <span class="input-group-addon" onclick="$('#header-search form').submit();"><i class="picons-search"></i></span>
                </div>
            </div>
            <ul id="livesearch-search-results"></ul>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function() {
    window.search = '';
    window.busy = false;
    $('#search-input').on('keyup change', function(e) {
        if (window.busy || window.search == $('#search-input').val()) {
            return;
        }
        window.search = $('#search-input').val();
        window.busy = true;
        $.getJSON('<?php echo $this->url->link('product/search/ajax')?>&keyword=' + search, function(data) {
            var items = '';
            $.each(data, function(s, el) {
                items += '<li><a href="' + el.href + '">' + el.name + '</a></li>';
            })
            $('#livesearch-search-results').html(items).addClass('active');
        });
        setTimeout(function() {
            window.busy = false;
            if ($('#search-input').val() != window.search) {
                $('#search-input').trigger('change');
            }
        }, 100);
    })
    $(document).on('keydown', function(ev) {
        try {
            if( ev.keyCode == 27) {
                $('#livesearch-search-results').removeClass('active');
            }
        }
        catch(e) {}
    });
    $('.content-container').on('click', function() {
        $('#livesearch-search-results').removeClass('active');
    })
})
</script>
