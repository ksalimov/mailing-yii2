/**
 * Created by Konstantin on 2016-12-26.
 */
$(document).ready(function () {
    $('#delete_mail').on('click', function () {
        var keys = $('#w1').yiiGridView('getSelectedRows');
        var ids = [];
        $('#w1 tr').each(function (index, value) {
            $this = $(this);
            $.each(keys, function (i, v) {
                if($this.data('key') == v) {
                    ids.push($this.attr('id'));
                }
            });

        });
        $.post({
            url: 'index.php?r=site%2Fdelete',
            datatype: 'json',
            data: {
                keylist: keys,
                ids: ids
            }
        });
    });

    $('table tbody tr td:not(:first-child)').on('click', function () {
        let id = $(this).parent().attr('id');
        let path = '?r=site%2Fmessage&id='+id.replace(/\s/g,'');
        $(location).attr('href', path);
    });
});
