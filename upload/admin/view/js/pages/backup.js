$(function() {
    $('a[rel=selectAllTables]').click(function() {
        $('#tables option').prop('selected', true);

        return false;
    });

    $('a[rel=deselectAllTables]').click(function() {
        $('#tables option').prop('selected', false);

        return false;
    });
})
