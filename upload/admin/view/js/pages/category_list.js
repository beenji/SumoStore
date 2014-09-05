$(function() {
    // Walk through categories, enabling and disabling switches
    $('.switch-status.switch-disabled').bootstrapSwitch('setActive', false);

    $('.switch-status').on('switch-change', function(e, data) {
        var status = 0,
            categoryTree = [],
            elem = $(this);

        if (data.value) {
            status = 1;
        }

        $.post('catalog/category/status&token=' + sessionToken, {
            category_id: $(this).data('category-id'),
            status: status
        }, function(data){
            /* Do something */
            if (data.result) {
                if (!status) {
                    categoryID = elem.data('category-id');

                    // Toggle all children
                    $('.switch-status-parent-' + categoryID).bootstrapSwitch('setState', false);
                    $('.switch-status-parent-' + categoryID).bootstrapSwitch('setActive', false);
                }
                else {
                    // Enable all children
                    categoryTree.push(elem.data('category-id'));
                    elem = $('.switch', elem.closest('tr').next('tr'));
                    
                    while (elem.data('parent-id') > 0 && categoryTree.indexOf(elem.data('parent-id')) > -1) {
                        // Toggle child
                        elem.bootstrapSwitch('setActive', true);

                        // Find next row
                        categoryTree.push(elem.data('category-id'));
                        elem = $('.switch', elem.closest('tr').next('tr'));
                    } 
                }
            }
            else {
                // Something went wrong
            }

        }, 'json');
    });
});

// Select correct tab
var tab = window.location.hash;

if (tab != undefined) {
    tab = tab.replace('#', '');
    $('a[href="#' + tab + '"]').click();
}
