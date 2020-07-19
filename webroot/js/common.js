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
            results[id] = { _lock: _lock };
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
            results[id] = { _lock: _lock };
        })
        return results;
    }
}

common.alert = {
    info: function (content, callback) {
        this.modal(content, '情報', 'bg-info', callback, false)
    },
    error: function (content, callback) {
        this.modal(content, 'エラー', 'bg-danger', callback, false)
    },
    confirm: function (content, okCallback, cancelCallback) {
        this.modal(content, '確認', 'bg-info', okCallback, cancelCallback)
    },
    progress: function (content) {
        this.modal(content, 'お待ちください', 'bg-info', false, false)
    },
    modal: function (content, title, headerClass, okCallback, cancelCallback) {
        if (!this._modal) {
            this._modal = (
                $('<div/>').addClass('modal fade').append(
                    $('<div/>').addClass('modal-dialog').append(
                        $('<div/>').addClass('modal-content')
                            .append($('<div/>').addClass('modal-header p-2 text-white'))
                            .append($('<div/>').addClass('modal-body'))
                            .append($('<div/>').addClass('modal-footer justify-content-center')
                                .append($('<button/>').addClass('btn btn-sm btn-block btn-outline-secondary modal-cancel mt-0').attr('data-dismiss', 'modal').text('キャンセル'))
                                .append($('<button/>').addClass('btn btn-sm btn-block btn-outline-primary modal-ok mt-0').attr('data-dismiss', 'modal').text('OK'))
                            )
                    )
                )
            );
            $(this._modal).on('shown.bs.modal', function (e) {
                $('.modal-footer button:not(.d-none):first', this).focus();
            });
            $(this._modal).on('hidden.bs.modal', function (e) {
                $('.modal-header', this).text(title).removeClass('bg-info bg-warning bg-danger bg-info')
                $('.modal-cancel', this).off('click').removeClass('d-none');
                $('.modal-ok', this).off('click').removeClass('d-none');
            });
        }
        if (this._modal.hasClass('show')) {
            // $(this._modal).one('hidden.bs.modal', function (e) {
            //     common.alert.modal(content, title, headerClass, okCallback, cancelCallback)
            // });
            // return;
            $(this._modal).trigger('hidden.bs.modal');
        }
        $('.modal-header', this._modal).text(title).addClass(headerClass);
        $('.modal-body', this._modal).text(content);
        if (cancelCallback === false) {
            $('.modal-cancel', this._modal).addClass('d-none');
        } else if (cancelCallback) {
            $('.modal-cancel', this._modal).one('click', cancelCallback);
        }
        if (okCallback === false) {
            $('.modal-ok', this._modal).addClass('d-none');
        } else if (okCallback) {
            $('.modal-ok', this._modal).one('click', okCallback);
        }
        this._modal.modal({ show: true, backdrop: 'static', keyboard: false });
    }
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
    if (navigator.msSaveBlob) {
        // IE 10+
        navigator.msSaveOrOpenBlob(blobData, fileName + ".pdf");
    } else {
        var blobUrl = URL.createObjectURL(new Blob([blobData], {
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
    var $spiner = $('<div />').addClass('modal fade').append($('<div />').addClass('ml-auto text-primary spinner-border float-right'));

    $(document).ajaxStart(function () {
        $spiner.modal('show');
    });
    $(document).ajaxStop(function () {
        $spiner.modal('hide')
    });

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
                data: { targets: targets },
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
                data: { targets: targets },
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
            var url = $(this).attr('data-action')
            common.alert.confirm('発行します。よろしいですか？', function () {
                $.ajax({
                    url: url,
                    method: 'post',
                    xhrFields: { responseType: 'blob' },
                    headers: { 'X-CSRF-TOKEN': $('#postForm [name=_csrfToken]').val() },
                    data: { targets: targets },
                }).fail(function (response) {
                    common.fallbackErrorHandler(response);
                }).done(function (response) {
                    common.createFile('password.csv', response);
                }).always(function (response) {
                    location.reload();
                });
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