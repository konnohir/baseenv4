var app = app || {};

$(function () {

    if ($('.add, .edit').length > 0) {

        // 編集種別
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

        // 部店プルダウン絞り込み
        $('#EditForm3 [name="MOrganizations[m_department1_id]"]').change(function() {
            var mDepartment1Id = $(this).val();
            console.log(mDepartment1Id);
            $('#EditForm3 [name="MOrganizations[m_department2_id]"] option[data-m-department1]').addClass('d-none');
            if (mDepartment1Id) {
                $('#EditForm3 [name="MOrganizations[m_department2_id]"] option[data-m-department1=' + mDepartment1Id + ']').removeClass('d-none');
            }
            $('#EditForm3 [name="MOrganizations[m_department2_id]"]').val(null);
        }).each(function () {
            // 画面初期表示
            $(this).change();
        });

    }

});