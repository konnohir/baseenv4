var common = common || [];

common.paginator = {
    watch: function (table) {
        table = table || '';
        var checkAll = '#' + table + 'checkAll';
        var checkRow = '.' + table + 'checkRow';

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
    getId: function (table) {
        table = table || '';
        var checkRow = '.' + table + 'checkRow';
        var $rows = $(checkRow + ':checked');
        switch ($rows.length) {
            case 1:
                return $rows.first().prop('value');
            default:
                common.alert.error('1件選択してください。');
                break;
        }
    },
    getIds: function (table) {
        table = table || '';
        var checkRow = '.' + table + 'checkRow';
        var $rows = $(checkRow + ':checked');
        switch ($rows.length) {
            case 0:
                common.alert.error('1件以上選択してください。');
                break;
            default:
                results = [];
                $rows.each(function (idx, value) {
                    results.push($(value).prop('value'));
                })
                return results;
        }
    },
    getTargets: function (table) {
        table = table || '';
        var checkRow = '.' + table + 'checkRow';
        var $rows = $(checkRow + ':checked');
        switch ($rows.length) {
            case 0:
                common.alert.error('1件以上選択してください。');
                break;
            default:
                var results = {};
                $rows.each(function (idx, value) {
                    var id = $(value).prop('value');
                    var lock = $(value).attr('data-lock');
                    results[id] = lock;
                })
                return results;
        }
    }
}

common.alert = {
    info: function (s) {alert(s)},
    error: function (s) {alert(s)},
    confirm: function (s) {confirm(s)},
}

$(function () {

    // イベントリスナ
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
        $(window).bind("beforeunload", function () {
            sessionStorage.viewUrl = location.href;
        });
    }

    // Readonly checkbox
    $('input[type=checkbox][readonly]').click(function () {
        return false;
    })

    // 新規作成ボタン
    $('.btn-add').click(function () {
        location.href = $(this).attr('data-action');
    });

    // 編集ボタン
    $('.btn-edit').click(function () {
        var id = $(this).attr('data-id') || common.paginator.getId();
        if (id) {
            location.href = $(this).attr('data-action') + '/' + id;
        }
    });

    // 削除ボタン
    $('.btn-delete').click(function () {
        var $form = $('#postForm');
        var targets = {}
        var id = $(this).attr('data-id');
        if (id) {
            var lock = $(this).attr('data-lock');
            targets[id] = lock;
        } else {
            targets = common.paginator.getTargets();
        }
        if (targets) {
            Object.keys(targets).forEach(function (id) {
                var lock = targets[id];
                $form.append('<input type="hidden" name="targets[' + id + ']" value="' + lock + '" />');
            });
            $form.attr('action', $(this).attr('data-action')).submit();
        }
    });

    // 増員ボタン
    $('.btn-add-staff').click(function () {
        var $form = $('#postForm');
        var targets = {}
        var id = $(this).attr('data-id');
        if (id) {
            var lock = $(this).attr('data-lock');
            targets[id] = lock;
        } else {
            targets = common.paginator.getTargets();
        }
        if (targets) {
            Object.keys(targets).forEach(function (id) {
                var lock = targets[id];
                $form.append('<input type="hidden" name="targets[' + id + ']" value="' + lock + '" />');
            });
            $form.attr('action', $(this).attr('data-action')).submit();
        }
    });
    
    // ACO更新ボタン
    $('.btn-refresh').click(function () {
        var template = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        $(this).append(template).prop('disabled', true);
    });
    
    // 遷移ボタン
    $('.btn-jump').click(function () {
        location.href = $(this).attr('data-action');
    });
    
    // 遷移ボタン (API)
    $('.btn-jump-api').click(function () {
        var $form = $('#postForm');
        var targets = {}
        var id = $(this).attr('data-id');
        if (id) {
            var lock = $(this).attr('data-lock');
            targets[id] = lock;
        } else {
            targets = common.paginator.getTargets();
        }
        if (targets) {
            Object.keys(targets).forEach(function (id) {
                var lock = targets[id];
                $form.append('<input type="hidden" name="targets[' + id + ']" value="' + lock + '" />');
            });
            $form.attr('action', $(this).attr('data-action')).submit();
        }
    });

    // キャンセルボタン
    $('.btn-cancel').click(function () {
        if (sessionStorage.viewUrl) {
            location.href = sessionStorage.viewUrl;
            sessionStorage.removeItem('viewUrl');
        }else if (sessionStorage.indexUrl) {
            location.href = sessionStorage.indexUrl;
            sessionStorage.removeItem('indexUrl');
        }else {
            location.href = $(this).attr('data-action');
        }
    });

    // 検索条件クリアボタン
    $('.btn-clear').click(function () {
        location.href = $(this).attr('data-action');
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
    }).each(function() {
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