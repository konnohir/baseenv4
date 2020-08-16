var app = app || {};

app.paginator = {
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
            app.modal.error('1件選択してください。');
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
            app.modal.error('1件以上選択してください。');
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

app.modal = {
    info: function (content, callback) {
        this.modal(content, '情報', 'p-2 text-white bg-info', callback, false)
    },
    error: function (content, callback) {
        this.modal(content, 'エラー', 'p-2 text-white bg-danger', callback, false)
    },
    confirm: function (content, okCallback, cancelCallback) {
        this.modal(content, '確認', 'p-2 text-white bg-info', okCallback, cancelCallback)
    },
    confirmDanger: function (content, okCallback, cancelCallback) {
        this.modal(content, '確認', 'p-2 text-white bg-danger', okCallback, cancelCallback)
    },
    modal: function (content, title, headerClass, okCallback, cancelCallback) {
        if (!this.$modal) {
            this.$modal = (
                $('<div/>').addClass('modal fade').attr('tabindex', -1).append(
                    $('<div/>').addClass('modal-dialog').append(
                        $('<div/>').addClass('modal-content')
                            .append($('<div/>').addClass('modal-header'))
                            .append($('<div/>').addClass('modal-body'))
                            .append($('<div/>').addClass('modal-footer justify-content-center'))
                    )
                )
            );
            this.$modal.on('shown.bs.modal', function () {
                $('.modal-footer button:first', this).focus();
            });
            this.$modal.on('hidden.bs.modal', function () {
                $('.modal-header', this).empty();
                $('.modal-body', this).empty();
                $('.modal-footer', this).empty();
            });
        }
        if ($('body').hasClass('modal-open')) {
            this.$modal.one('hidden.bs.modal', function () {
                app.modal.modal(content, title, headerClass, okCallback, cancelCallback);
            });
            return;
        }
        $('.modal-header', this.$modal).removeClass().addClass('modal-header ' + headerClass).text(title);
        $('.modal-body', this.$modal).text(content);
        if (cancelCallback !== false) {
            $('.modal-footer', this.$modal).append($('<button/>').addClass('btn btn-sm btn-block btn-outline-secondary modal-cancel mt-0').attr('data-dismiss', 'modal').text('キャンセル'))
            if (cancelCallback) {
                $('.modal-cancel', this.$modal).one('click', cancelCallback);
            }
        }
        if (okCallback !== false) {
            $('.modal-footer', this.$modal).append($('<button/>').addClass('btn btn-sm btn-block btn-outline-primary modal-ok mt-0').attr('data-dismiss', 'modal').text('OK'))
            if (okCallback) {
                $('.modal-ok', this.$modal).one('click', okCallback);
            }
        }
        this.$modal.modal({ show: true, backdrop: 'static', keyboard: false });
        document.activeElement.blur();
    }
}

app.fallbackErrorHandler = function (response) {
    var message = {
        401: '認証が必要です。',
        403: '許可されていない操作です。',
        404: '既に削除されています。',
    }
    app.modal.error(message[response.status] || 'システムエラーが発生しました。');
}

app.createFile = function (fileName, blobData) {
    if (navigator.msSaveBlob) {
        // IE 10+
        navigator.msSaveOrOpenBlob(blobData, fileName);
    } else {
        var blobUrl = URL.createObjectURL(new Blob([blobData]));
        var a = $('<a/>').css('display', 'none').attr('href', blobUrl).attr('download', fileName)[0];
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
}

app.parentPage = {
    watch: function () {
        // Record index url
        if ($('.index').length > 0) {
            sessionStorage.indexUrl = location.href;
            sessionStorage.viewUrl = '';
        }

        // Record view url
        if ($('.view').length > 0) {
            sessionStorage.viewUrl = location.href;
        }
    },
    get: function (self) {
        if (sessionStorage.viewUrl && $('.view').length === 0) {
            return sessionStorage.viewUrl;
        } else if (sessionStorage.indexUrl) {
            return sessionStorage.indexUrl;
        }
        return $(self).attr('data-action');
    }
}

app.loadingControl = {
    init: function () {
        $(document).ajaxStart(this.show);
        $(document).ajaxError(this.hide);
        $(document).submit(this.show);
    },

    show: function () {
        $('body').removeClass('fadein');
        var $spinner = $('<div/>').addClass('spinner-border loading');
        $('body').append($spinner);
        document.activeElement.blur();
    },

    hide: function () {
        $('body').addClass('fadein');
        $('.loading').remove();
    }
}

$(function () {

    // Watch table
    app.paginator.watch();

    // Watch index, view url
    app.parentPage.watch();

    // Loading control
    app.loadingControl.init();

    // Ajax setup
    $.ajaxSetup({
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('#postForm [name=_csrfToken]').val() },
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
        var targets = app.paginator.getTargets(this);
        if (targets) {
            $.ajax({
                url: $(this).attr('data-action'),
                method: 'post',
                data: { targets: targets },
            }).fail(function (response) {
                app.fallbackErrorHandler(response);
            }).done(function (response) {
                location.reload();
            });
        }
    });

    // 編集ボタン
    $('.btn-edit').click(function () {
        var id = app.paginator.getId(this);
        if (id) {
            location.href = $(this).attr('data-action') + '/' + id;
        }
    });

    // 削除ボタン
    $('.btn-delete').click(function () {
        var targets = app.paginator.getTargets(this);
        if (targets) {
            var url = $(this).attr('data-action')
            app.modal.confirmDanger('削除します。よろしいですか？', function () {
                $.ajax({
                    url: url,
                    method: 'post',
                    data: { targets: targets },
                }).fail(function (response) {
                    app.fallbackErrorHandler(response);
                }).done(function (e) {
                    if ($('.btn-cancel').length > 0) {
                        $('.btn-cancel').click();
                    } else {
                        location.reload();
                    }
                });
            });
        }
    });

    // パスワード再発行ボタン
    $('.btn-password-issue').click(function () {
        var targets = app.paginator.getTargets(this);
        if (targets) {
            var url = $(this).attr('data-action')
            app.modal.confirm('発行します。よろしいですか？', function () {
                $.ajax({
                    url: url,
                    method: 'post',
                    xhrFields: { responseType: 'blob' },
                    dataType: '',
                    data: { targets: targets },
                }).fail(function (response) {
                    app.fallbackErrorHandler(response);
                }).done(function (response) {
                    app.createFile('password.csv', response);
                }).done(function () {
                    // setTimeout for IE
                    setTimeout(function () {
                        location.reload();
                    }, 0);
                });
            });
        }
    });

    // キャンセルボタン
    $('.btn-cancel').click(function () {
        location.href = app.parentPage.get(this);
    });

    // フォーム送信ボタン
    $('.btn-submit').click(function () {
        var $target = $(this).closest('form');
        app.modal.confirm('送信します。よろしいですか？', function () {
            $target.submit();
        });
    });

});
