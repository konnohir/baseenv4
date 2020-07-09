var common = common || [];

common.paginator = {
    watch: function (suffix) {
        suffix = suffix || '';
        var checkAll = '#checkAll' + suffix;
        var checkRow = '.checkRow' + suffix;

        $(checkAll).click(function () {
            var checked = $(checkAll).prop('checked');
            $(checkRow).prop('checked', checked);
        });

        $(checkRow).click(function () {
            var type = $(this).prop('type');
            if (type === 'checkbox') {
                var all_count = $(checkRow).length;
                var selected_count = $(checkRow + ':checked').length;
                $(checkAll).prop('checked', all_count == selected_count);
            } else {
                var id = $(this).prop('id');
                $(checkRow + ':not([id=' + id + '])').prop('checked', false);
            }
        });
    },
    getId: function (self, suffix) {

        var id = $(self).attr('data-id');
        if (id) {
            return id;
        }

        var checkRow = '.checkRow' + (suffix || '');
        var $rows = $(checkRow + ':checked');
        if ($rows.length !== 1) {
            common.alert.error('1件選択してください。');
            return;
        }

        return $rows.first().prop('value');
    },
    getTargets: function (self, suffix) {
        var results = {};

        var id = $(self).attr('data-id');
        var _lock = $(self).attr('data-lock');
        if (id && _lock) {
            results[id] = { _lock };
            return results
        }

        var checkRow = '.checkRow' + (suffix || '');
        var $rows = $(checkRow + ':checked');
        if ($rows.length === 0) {
            common.alert.error('1件以上選択してください。');
            return;
        }

        $rows.each(function (idx, value) {
            var id = $(value).prop('value');
            var _lock = $(value).attr('data-lock');
            results[id] = { _lock };
        })
        return results;
    }
}

common.alert = {
    info: function (s) { alert(s) },
    error: function (s) { alert(s) },
    confirm: function (s) { confirm(s) },
}

common.fallbackErrorHandler = function (response) {
    var message = {
        401: '認証が必要です。',
        403: '許可されていない操作です。',
        404: '既に削除されています。',
        500: 'システムエラーが発生しました。',
    }
    common.alert.error(message[response.status] || '予期せぬエラーが発生しました。');
}

common.createFile = function (fileName, blobData) {
    if (navigator.appVersion.toString().indexOf('.NET') > 0) {
        //IE 10+
        window.navigator.msSaveBlob(blobData, fileName + ".pdf");
    } else {
        var blobUrl = window.URL.createObjectURL(new Blob([blobData], {
            type: blobData.type
        }));
        var a = $("<a/>").css('display', 'none').attr('href', blobUrl).attr('download', fileName)[0];
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
}

$(function () {

    // Watch table
    common.paginator.watch();

    // Record index url
    if ($('.index').length) {
        $(window).bind("beforeunload", function () {
            sessionStorage.indexUrl = location.href;
            sessionStorage.viewUrl = '';
        });
    }

    // Record view url
    if ($('.view').length) {
        sessionStorage.removeItem('viewUrl');
        $(window).bind("beforeunload", function () {
            sessionStorage.viewUrl = location.href;
        });
    }

    // Readonly checkbox
    $('input[type=checkbox][readonly]').click(function () {
        return false;
    })

    // 汎用ボタン
    $('.btn-jump, .btn-add, .btn-clear').click(function () {
        location.href = $(this).attr('data-action');
    });

    // 汎用ボタン (API)
    $('.btn-jump-api').click(function () {
        var targets = common.paginator.getTargets(this);
        if (targets) {
            $.ajax({
                url: $(this).attr('data-action'),
                method: 'post',
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('#postForm [name=_csrfToken]').val() },
                data: { targets },
            }).fail(function (response) {
                common.fallbackErrorHandler(response);
            }).always(function (response) {
                location.reload();
            });
        }
    });

    // 編集ボタン
    $('.btn-edit').click(function () {
        var id = common.paginator.getId(this);
        if (id) {
            location.href = $(this).attr('data-action') + '/' + id;
        }
    });

    // 削除ボタン
    $('.btn-delete').click(function () {
        var targets = common.paginator.getTargets(this);
        if (targets) {
            $.ajax({
                url: $(this).attr('data-action'),
                method: 'post',
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('#postForm [name=_csrfToken]').val() },
                data: { targets },
            }).fail(function (response) {
                common.fallbackErrorHandler(response);
            }).always(function (response) {
                if ($('.btn-cancel').length) {
                    $('.btn-cancel').click();
                } else {
                    location.reload();
                }
            });
        }
    });

    // パスワード再発行ボタン
    $('.btn-password-issue').click(function () {
        var targets = common.paginator.getTargets(this);
        if (targets) {
            $.ajax({
                url: $(this).attr('data-action'),
                method: 'post',
                xhrFields: { responseType: 'blob' },
                headers: { 'X-CSRF-TOKEN': $('#postForm [name=_csrfToken]').val() },
                data: { targets },
            }).fail(function (response) {
                common.fallbackErrorHandler(response);
            }).done(function (response) {
                common.createFile('password.csv', response);
            }).always(function (response) {
                location.reload();
                console.log(response)
            });
        }
    });

    // キャンセルボタン
    $('.btn-cancel').click(function () {
        if (sessionStorage.viewUrl) {
            location.href = sessionStorage.viewUrl;
            sessionStorage.removeItem('viewUrl');
        } else if (sessionStorage.indexUrl) {
            location.href = sessionStorage.indexUrl;
            sessionStorage.removeItem('indexUrl');
        } else {
            location.href = $(this).attr('data-action');
        }
    });

    // 一覧画面へ戻るボタン
    $('.btn-back-to-index').click(function () {
        var url = sessionStorage.indexUrl;
        sessionStorage.removeItem('viewUrl');
        sessionStorage.removeItem('indexUrl');
        location.href = url || $(this).attr('data-action');
    });

    // フォーム送信ボタン
    $('.btn-submit').click(function () {
        $(this).parents('form').submit();
    });

});


// 個別
$(function () {
    // 権限詳細
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