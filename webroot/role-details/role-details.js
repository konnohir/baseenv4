var app = app || {};

$(function () {
    // 権限詳細チェックボックス制御
    $('[data-type=controller]').change(function () {
        var checked = $(this).prop('checked');
        $(this).parents('li').first()
            .find('ul input[type=checkbox]')
            .prop('checked', checked)
            .prop('disabled', checked);
        return false;
    }).each(function () {
        // 画面初期表示
        var checked = $(this).prop('checked');
        if (checked) {
            $(this).change();
        }
        if ($('.view').length) {
            // IE fix
            $(this).off('change');
        }
    });
});