if (typeof console === "undefined" || typeof console.log === "undefined") {
    console = {};
    console.log = function() {};
}
$(function(){
    // Perform a menu search
    $('#menuSearch').keyup(function() {
        // Do something
        // No.
        // Oh yes! Start searching you lazy bastard!
        // ..
        // ..
        // Ok..
        var menuSearch = $(this).val().toLowerCase();

        if (menuSearch.length < 2) {
            $('.menu-search-results').remove();

            return;
        }

        var list = $('<ul class="menu-search-results" />'),
            append = false;

        $('.cl-vnavigation ul.sub-menu li a').filter(function() {
            return $(this).text().toLowerCase().match(menuSearch) !== null;
        }).each(function() {
            list.append('<li><a href="' + $(this).attr('href') + '">' + $(this).text() + '</a></li>');

            append = true;
        });

        $('.menu-search-results').remove();

        if (append) {
            list.appendTo($('#menuSearch').parent());
        }
    });

    $('.allow-tab').each(function() {
        $(this).on('keydown', function(e) {
            if (e.keyCode === 9) {
                var value   = this.value,
                start   = this.selectionStart,
                end     = this.selectionEnd;
                this.value = value.substring(0, start) + '    ' + value.substring(end);
                this.selectionStart = this.selectionEnd = start + 4;
                return false;
            }
            return true;
        })
    })

    redactorSettings = {
        focus: true,
        tabFocus: false,
        minHeight: 100,
        imageUploadParam: 'uploads',
        imageUpload: base + 'common/images/upload?mode=redactor&token=' + sessionToken
    };

    $('.redactor').redactor(redactorSettings);

    // Form errors
    if (formError != '') {
        $.gritter.add({
            text: formError,
            sticky: true,
            class_name: 'danger'
        });
    }

    $('a[rel="selectAll"], a[rel="deselectAll"], input.toggleAll').on('ifToggled click', function() {
        var parent = $(this).parent(),
            action = '',
            i = 0;

        while ($('input[type=checkbox]', parent).length <= 1 && i < 8) {
            parent = parent.parent();
            i++;
        }

        if ($(this).hasClass('toggleAll')) {
            action = $(this).is(':checked') ? 'selectAll' : 'deselectAll';
        } else {
            action = $(this).attr('rel');
        }

        if (action == 'selectAll') {
            // Select
            $('input[type=checkbox]', parent).prop('checked', true);
        }
        else {
            // Deselect
            $('input[type=checkbox]', parent).prop('checked', false);
        }

        $('input.icheck', parent).iCheck('update');
    });

    $('a[rel="selectedItemTrigger"]').click(function() {
        var msg = $(this).data('message'),
            elem = $(this);

        if (msg != undefined) {
            bootbox.confirm(msg, function(result) {
                if (result) {
                    $('#selectedItemListener').attr('action', elem.attr('href'));
                    $('#selectedItemListener').submit();
                }
            });
        } else {
            $('#selectedItemListener').attr('action', elem.attr('href'));
            $('#selectedItemListener').submit();
        }

        return false;
    });

    $('a[rel="singleItemTrigger"]').click(function() {
        var msg = $(this).data('message'),
            elem = $(this);

        if (msg != undefined) {
            bootbox.confirm(msg, function(result) {
                if (result) {
                    // Uncheck all items but parent item
                    $('input[name^="selected"]', $('#selectedItemListener')).prop('checked', false);
                    $('input[name^="selected"]', elem.closest('tr')).prop('checked', true);


                    $('#selectedItemListener').attr('action', elem.attr('href'));
                    $('#selectedItemListener').submit();
                }
            });
        } else {
            // Uncheck all items but parent item
            $('input[name^="selected"]', $('#selectedItemListener')).prop('checked', false);
            $('input[name^="selected"]', elem.closest('tr')).prop('checked', true);


            $('#selectedItemListener').attr('action', elem.attr('href'));
            $('#selectedItemListener').submit();
        }

        return false;
    });

    // [When this changes, update parsley as well!]
    $('#language-selector a').click(function() {
        $('.lang-block').hide();
        $('div.lang-' + $(this).data('lang-id')).css('display', 'block');
        $('.input-group.lang-' + $(this).data('lang-id')).css('display', 'table');

        $('#language-selector-btn > span:first-child').html($(this).html());
        $('#language-selector-btn').click();

        return false;
    });

    bootbox.setDefaults({
        locale: "nl"
    });

    $('a[rel="confirm"]').click(function() {
        var msg = $(this).data('message'),
                href = $(this).attr('href');

        bootbox.confirm(msg, function(result) {
            if (result) {
                document.location = href;
            }
        });

        return false;
    });

    //Functions
    function toggleSideBar(_this){
        var b = $("#sidebar-collapse")[0];
        var w = $("#cl-wrapper");
        var s = $(".cl-sidebar");

        if(w.hasClass("sb-collapsed")){
            $(".fa",b).addClass("fa-angle-left").removeClass("fa-angle-right");
            w.removeClass("sb-collapsed");
        }else{
            $(".fa",b).removeClass("fa-angle-left").addClass("fa-angle-right");
            w.addClass("sb-collapsed");
        }
        //updateHeight();
    }

    function updateHeight(){
        if(!$("#cl-wrapper").hasClass("fixed-menu")){
            var button = $("#cl-wrapper .collapse-button").outerHeight();
            var navH = $("#head-nav").height();
            //var document = $(document).height();
            var cont = $("#pcont").height();
            var sidebar = ($(window).width() > 755 && $(window).width() < 963)?0:$("#cl-wrapper .menu-space .content").height();
            var windowH = $(window).height();

            if(sidebar < windowH && cont < windowH){
                if(($(window).width() > 755 && $(window).width() < 963)){
                    var height = windowH;
                }else{
                    var height = windowH - button;
                }
            }else if((sidebar < cont && sidebar > windowH) || (sidebar < windowH && sidebar < cont)){
                var height = cont + button;
            }else if(sidebar > windowH && sidebar > cont){
                var height = sidebar + button;
            }

            // var height = ($("#pcont").height() < $(window).height())?$(window).height():$(document).height();
            $("#cl-wrapper .menu-space").css("min-height",height);
        }else{
            $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
        }
    }


            /*VERTICAL MENU*/
            /*$(".cl-vnavigation li ul").each(function(){
                $(this).parent().addClass("parent");
            });

            $(".cl-vnavigation li ul li.active").each(function(){
                $(this).parent().css({'display':'block'});
                $(this).parent().parent().addClass("open");
                //setTimeout(function(){updateHeight();},200);
            });

            $(".cl-vnavigation").delegate(".parent > a","click",function(e){
                $(".cl-vnavigation .parent.open > ul").not($(this).parent().find("ul")).slideUp(300, 'swing',function(){
                     $(this).parent().removeClass("open");
                });

                var ul = $(this).parent().find("ul");
                ul.slideToggle(300, 'swing', function () {
                    var p = $(this).parent();
                    if(p.hasClass("open")){
                        p.removeClass("open");
                    }else{
                        p.addClass("open");
                    }
                    //var menuH = $("#cl-wrapper .menu-space .content").height();
                    // var height = ($(document).height() < $(window).height())?$(window).height():menuH;
                    //updateHeight();
                 $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
                });
                e.preventDefault();
            });*/

            /*Small devices toggle*/
            $(".cl-toggle").click(function(e){
                var ul = $(".cl-vnavigation");
                ul.slideToggle(300, 'swing', function () {
                });
                e.preventDefault();
            });

            /*Collapse sidebar*/
            $("#sidebar-collapse").click(function(){
                    toggleSideBar();
            });


            if($("#cl-wrapper").hasClass("fixed-menu")){
                var scroll =  $("#cl-wrapper .menu-space");
                scroll.addClass("nano nscroller");

                function update_height(){
                    var button = $("#cl-wrapper .collapse-button");
                    var collapseH = button.outerHeight();
                    var navH = $("#head-nav").height();
                    var height = $(window).height() - ((button.is(":visible"))?collapseH:0);
                    scroll.css("height",height);
                    $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
                }

                $(window).resize(function() {
                    update_height();
                });

                update_height();
                $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });

            }else{
                $(window).resize(function(){
                    //updateHeight();
                });
                //updateHeight();
            }


            /*SubMenu hover */
                var tool = $("<div id='sub-menu-nav' style='position:fixed;z-index:9999;'></div>");

                function showMenu(_this, e){
                    if(($("#cl-wrapper").hasClass("sb-collapsed") || ($(window).width() > 755 && $(window).width() < 963)) && $("ul",_this).length > 0){
                        $(_this).removeClass("ocult");
                        var menu = $("ul",_this);
                        if(!$(".dropdown-header",_this).length){
                            var head = '<li class="dropdown-header">' +  $(_this).children().html()  + "</li>" ;
                            menu.prepend(head);
                        }

                        tool.appendTo("body");
                        var top = ($(_this).offset().top + 8) - $(window).scrollTop();
                        var left = $(_this).width();

                        tool.css({
                            'top': top,
                            'left': left + 8
                        });
                        tool.html('<ul class="sub-menu">' + menu.html() + '</ul>');
                        tool.show();

                        menu.css('top', top);
                    }else{
                        tool.hide();
                    }
                }

                $(".cl-vnavigation li").hover(function(e){
                    showMenu(this, e);
                },function(e){
                    tool.removeClass("over");
                    setTimeout(function(){
                        if(!tool.hasClass("over") && !$(".cl-vnavigation li:hover").length > 0){
                            tool.hide();
                        }
                    },500);
                });

                tool.hover(function(e){
                    $(this).addClass("over");
                },function(){
                    $(this).removeClass("over");
                    tool.fadeOut("fast");
                });


                $(document).click(function(){
                    tool.hide();
                });
                $(document).on('touchstart click', function(e){
                    tool.fadeOut("fast");
                });

                tool.click(function(e){
                    e.stopPropagation();
                });

                $(".cl-vnavigation li").click(function(e){
                    if((($("#cl-wrapper").hasClass("sb-collapsed") || ($(window).width() > 755 && $(window).width() < 963)) && $("ul",this).length > 0) && !($(window).width() < 755)){
                        showMenu(this, e);
                        e.stopPropagation();
                    }
                });

                $(".cl-vnavigation li").on('touchstart click', function(){
                    //alert($(window).width());
                });

            $(window).resize(function(){
                //updateHeight();
            });

            var domh = $("#pcont").height();
            $(document).bind('DOMSubtreeModified', function(){
                var h = $("#pcont").height();
                if(domh != h) {
                    //updateHeight();
                }
            });

            /*Return to top*/
            var offset = 220;
            var duration = 500;
            var button = $('<a href="#" class="back-to-top"><i class="fa fa-angle-up"></i></a>');
            button.appendTo("body");

            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop() > offset) {
                        jQuery('.back-to-top').fadeIn(duration);
                } else {
                        jQuery('.back-to-top').fadeOut(duration);
                }
            });

            jQuery('.back-to-top').click(function(event) {
                    event.preventDefault();
                    jQuery('html, body').animate({scrollTop: 0}, duration);
                    return false;
            });

            $('.switch').bootstrapSwitch();

            $('.icheck').iCheck({
                    checkboxClass: 'icheckbox_flat-green',
                    radioClass: 'iradio_flat-green'
            });

    /*Side Bar*/
    $('.toggle-menu').jPushMenu();

    /*Datepicker UI*/
    //$( ".ui-datepicker" ).datepicker();

    /*Tooltips*/
    //$('.ttip, [data-toggle="tooltip"]').tooltip();

    /*Popover*/
    //$('[data-popover="popover"]').popover();

    /*NanoScroller*/
    $(".nscroller").nanoScroller();


    /*Bind plugins on hidden elements*/
    /*Dropdown shown event*/
    $('.dropdown').on('shown.bs.dropdown', function () {
        $(".nscroller").nanoScroller();
    });

    /*Tabs refresh hidden elements*/
    $('.nav-tabs').on('shown.bs.tab', function (e) {
        $(".nscroller").nanoScroller();
    });
});

/*
$('.icheck').on('ifChecked', function(e) {
    $(this).attr('checked', true);
});

$('.icheck').on('ifUnchecked', function(e) {
    $(this).attr('checked', false);
});*/
