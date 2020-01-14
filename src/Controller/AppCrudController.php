<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

/**
 * AppCrud Controller
 * CRUDコントローラー
 */
class AppCrudController extends AppController
{
    /**
     * PaginatorComponent デフォルト設定
     * @var array
     */
    public $paginate = [
        // 検索スコープ (FilterComponentが自動設定)
        'finder' => [],
        // 1ページ当たりの表示件数
        'limit' => 20,
        'maxLimit' => 20,
        // 並び順
        'order' => ['id' => 'asc']
    ];

    /**
     * FilterComponent デフォルト設定
     */
    public $filter = [
        'paginate' => [
            'index' => true,
        ],
        'requestId' => [
            'view' => true,
            'edit' => true,
        ],
        'requestTarget' => [
            'delete' => true,
            'accountLock' => true,
            'accountUnlock' => true,
            'passwordIssue' => true,
            'addStaff' => true,
        ],
    ];

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Filter', $this->filter);
    }

    /**
     * リクエストされた画面が新規登録画面であるかをURLから判定する
     * 
     * @return bool
     */
    protected function isAdd()
    {
        return strpos($this->getRequest()->getRequestTarget(), 'add');
    }
}
