$(document).ready(function () {
    var modal = $('#delete_label_modal');
    var items = [];

    $('.label-item').each(function (_, el) {
        el = $(el);
        var id = el.data('id');
        items[id] = {
            id: id,
            delete_url: el.data('delete-url'),
            el: el
        };
    });

    $('.label-delete-link').click(function () {
        var id = $(this).data('id');
        modal.find('.confirm-delete-link').data('id', id);
    });

    $('.confirm-delete-link').click(function (e) {
        e.preventDefault();
        $(this).addClass('m-loader m-loader--light m-loader--right');

        var id = $(this).data('id');
        var item = items[id];
        if (!item) {
            return;
        }
        $.post(item.delete_url, function (response) {
            window.location.reload();
        });
    });
    $('.break-delete-link').click(function () {
        $(this).data('id', '');
    });
});