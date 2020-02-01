<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\I18n;

/**
 * Application Controller
 * アプリケーションコントローラ
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
        if (!empty($user->language)) {
            I18n::setLocale($user->language);
        }
    }

    /**
     * Handles pagination of records in Table objects.
     *
     * Will load the referenced Table object, and have the PaginatorComponent
     * paginate the query using the request date and settings defined in `$this->paginate`.
     *
     * This method will also make the PaginatorHelper available in the view.
     *
     * @param \Cake\ORM\Table|string|\Cake\ORM\Query|null $object Table to paginate
     * (e.g: Table instance, 'TableName' or a Query object)
     * @param array $settings The settings/configuration used for pagination.
     * @return \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface Query results
     * @link https://book.cakephp.org/4/en/controllers.html#paginating-a-model
     * @throws \RuntimeException When no compatible table object can be found.
     */
    public function paginate($object = null, array $settings = [])
    {
        try {
            return parent::paginate($object, $settings);
        } catch (NotFoundException $e) {
            $obj = $this->getRequest()->getAttribute('paging');
            $page = $obj[key($obj)]['pageCount'];
            if ($page <= 1) {
                $page = null;
            }
            return $this->redirect([
                'action' => $this->getRequest()->getParam('action'),
                '?' => ['page' => $page] + $this->getRequest()->getQuery(),
            ]);
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
     * @param Cake\Datasource\EntityInterface $entity エンティティ
     * @param bool $isShowDetail true: バリデーションエラーの詳細を1件出力する
     * @return bool false
     */
    protected function failed(?EntityInterface $entity, $isShowDetail = false)
    {
        if ($entity === null) {
            // E-V-NOT-FOUND:対象の{0}が存在しません
            $this->Flash->error(__('E-NOT-FOUND', __($this->title)));
            return false;
        }
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
