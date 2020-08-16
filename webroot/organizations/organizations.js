var app = app || {};

$(function () {

    if ($('.add, .edit').length > 0) {

        $('[name=edit_type]').change(function () {
            var editType = $(this).val();
            $('[id^=EditForm]').addClass('d-none');
            $('#EditForm' + editType).removeClass('d-none');
        }).each(function () {
            // 画面初期表示
            var checked = $(this).prop('checked');
            if (checked) {
                $(this).change();
            }
        });

        // Debug
        $(document).on('change', function () {
            $('footer').html("<pre>" + JSON.stringify($("form:first").serializeArray(), null, 2) + "</pre>");
        });

    }

});