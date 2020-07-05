<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Konnohir\View\Helper;

use Cake\View\Helper\PaginatorHelper as Helper;
use Cake\View\View;

/**
 * Pagination Helper class for easy generation of pagination links.
 *
 * PaginationHelper encloses all methods needed when working with pagination.
 *
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\NumberHelper $Number
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 * @link https://book.cakephp.org/3.0/en/views/helpers/paginator.html
 */
class PaginatorHelper extends Helper
{
    
    /**
     * Constructor. Overridden to merge passed args with URL options.
     *
     * @param \Cake\View\View $view The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config + [
            'templates' => [
                'nextActive' => '<li class="page-item"><a class="page-link" rel="next" href="{{url}}">' . __('次へ >>') . '</a></li>',
                'nextDisabled' => '<li class="page-item disabled"><span class="page-link">' . __('次へ >>') . '</span></li>',
                'prevActive' => '<li class="page-item"><a class="page-link" rel="prev" href="{{url}}">' . __('<< 前へ') . '</a></li>',
                'prevDisabled' => '<li class="page-item disabled"><span class="page-link">' . __('<< 前へ') . '</span></li>',
                'counterPages' => '<li class="page-item disabled"><span class="page-link">' . __('検索結果: {{count}}件') . '</span></li>',
                'first' => '<li class="page-item"><a class="page-link" href="{{url}}">' . ('<<') . '</a></li>',
                'last' => '<li class="page-item"><a class="page-link" href="{{url}}">' . ('>>') . '</a></li>',
                'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'current' => '<li class="page-item active"><a class="page-link" href="">{{text}}</a></li>',
                'checkboxAll' => '<div class="custom-control custom-checkbox"><input type="checkbox" id="{{table}}checkAll" class="custom-control-input"><label class="custom-control-label" for="{{table}}checkAll"></label></div>',
                'checkbox' => '<div class="custom-control custom-checkbox"><input type="checkbox" id="{{table}}checkRow{{id}}" name="{{table}}checkRow{{id}}" value="{{id}}" data-lock="{{lock}}" class="custom-control-input {{table}}checkRow"><label class="custom-control-label" for="{{table}}checkRow{{id}}"></label></div>',
                'radio' => '<div class="custom-control custom-radio"><input type="radio" id="{{table}}checkRow{{id}}" name="{{table}}checkRow{{id}}" value="{{id}}" data-lock="{{lock}}" class="custom-control-input {{table}}checkRow"><label class="custom-control-label" for="{{table}}checkRow{{id}}"></label></div>',
            ],
        ]);
    }

    /**
     * 全選択チェックボックス
     *
     * @var $table テーブル名。一画面で二つ以上のテーブルを使う場合、テーブル毎に異なる値を指定する
     */
    public function checkboxAll($table = '')
    {
        $template = 'checkboxAll';
        $map = [
            'table' => $table,
        ];

        return $this->templater()->format($template, $map);
    }

    /**
     * 行選択チェックボックス
     *
     * @var $id チェックボックスのvalueに指定する値。Entityのidやcodeなどのユニーク値を指定する
     * @var $table テーブル名。一画面で二つ以上のテーブルを使う場合、テーブル毎に異なる値を指定する
     */
    public function checkbox($id, $lock, $table = '')
    {
        $template = 'checkbox';
        $map = [
            'id' => $id,
            'lock' => $lock,
            'table' => $table,
        ];

        return $this->templater()->format($template, $map);
    }

    /**
     * 行選択ラジオボタン
     *
     * @var $id ラジオボタンのvalueに指定する値。Entityのidやcodeなどのユニーク値を指定する
     * @var $table テーブル名。一画面で二つ以上のテーブルを使う場合、テーブル毎に異なる値を指定する
     */
    public function radio($id, $lock, $table = '')
    {
        $template = 'radio';
        $map = [
            'id' => $id,
            'lock' => $lock,
            'table' => $table,
        ];

        return $this->templater()->format($template, $map);
    }
}
