<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\I18n\I18n;

/**
 * Application Controller
 * 基底コントローラ
 */
class AppController extends Controller
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
     * 初期化処理
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // 共通コンポーネント
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
        $this->loadComponent('Acl.Acl');

        // 言語設定
        $user = $this->getRequest()->getAttribute('identity');
        if (isset($user) && !empty($user->language)) {
            I18n::setLocale($user->language);
        }
    }

    /**
     * Util: リクエストされた画面が新規登録画面であるかをURLから判定する
     * 
     * @return bool
     */
    protected function isAdd()
    {
        return strpos($this->getRequest()->getRequestTarget(), 'add');
    }

    /**
     * Util: エラーメッセージをセットする
     * 
     * @param Cake\ORM\Entity $entity エンティティ
     * @param bool $isShowDetail true: バリデーションエラーの詳細を1件出力する
     * @return bool false
     */
    protected function failed($entity, $isShowDetail = false) {
        // E-V-WRONG-INPUT: 入力内容に誤りがあります。
        $errorMessage = __('E-V-WRONG-INPUT');
        if ($isShowDetail) {
            $errorMessage .= "\n・" . current(current($entity->getErrors()));
        }
        if ($entity->getError('_lock')) {
            $errorMessage = current($entity->getError('_lock'));
        }
        $this->Flash->error($errorMessage);
        return false;
    }
}
