<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * ユーザーマスタ
 * 
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\RolesTable $Roles
 */
class UsersController extends AppController
{
    /**
     * @var string 画面タイトル
     */
    public $title = 'ユーザー';

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // リクエストフィルタ
        $this->loadComponent('RequestFilter', [
            'index' => ['paginate'],
            'view' => ['requestId'],
            'edit' => ['requestId'],
            'delete' => ['requestTarget'],
            'lockAccount' => ['requestTarget'],
            'unlockAccount' => ['requestTarget'],
            'passwordIssue' => ['requestTarget'],
        ]);

        // ユーザーマスタ
        $this->loadModel('Users');

        // 権限マスタ
        $this->loadModel('Roles');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $users: ユーザー一覧
        $users = $this->paginate($this->Users, [
            // 取得カラム
            'fields' => [
                // 主キー
                'Users.id',
                // メールアドレス
                'Users.email',
                // 権限名称
                'Roles.name',
                // アカウントロックフラグ
                'VUserRemarks.is_account_locked',
                // パスワード発行フラグ
                'VUserRemarks.is_password_issued',
                // 更新日時
                'Users.updated_at',
            ],
            // 整列可能カラム
            'sortableFields' => [
                'Users.id',
                'Users.email',
                'Roles.id',
                'VUserRemarks.is_account_locked',
                'VUserRemarks.is_password_issued',
            ],
            // 結合テーブル
            'contain' => [
                'Roles',
                'VUserRemarks',
            ],
            // 並び順
            'order' => [
                'Users.id' => 'asc'
            ],
        ]);

        $this->set(compact('users'));
    }

    /**
     * 詳細画面
     *
     * @param string $id ユーザーID
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // $user: ユーザーエンティティ
        $user = $this->Users->get($id, [
            // 結合テーブル
            'contain' => [
                'Roles',
                'VUserRemarks',
            ],
        ]);

        $this->set(compact('user'));
    }

    /**
     * 新規登録画面
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        return $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id ユーザーID
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $user: ユーザーエンティティ
        $user = $this->getEntity($id);

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // DB保存成功時: 詳細画面へ遷移
            if ($this->Users->doEditEntity($user, $this->request->getData())) {
                // I-SAVE: ユーザーを保存しました。
                return $this->success('I-SAVE', __($this->title), ['action' => 'view', $user->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($user);
        }

        // $roleList: 権限リスト
        $roleList = $this->Roles->find('list')->toArray();

        $this->set(compact('user', 'roleList'));
    }

    /**
     * アカウントロックAPI
     *
     * @return \Cake\Http\Response|null
     */
    public function lockAccount()
    {
        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () {
            // $targets: 対象データの配列 (array)
            $targets = $this->request->getData('targets');

            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->get($id);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->doLockAccount($user, $requestData)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            // 全データDB保存成功時: コミット
            return $this->success('I-LOCK-ACCOUNT');
        });

        $this->set(compact('result'));
    }

    /**
     * アカウントロック解除API
     *
     * @return \Cake\Http\Response|null
     */
    public function unlockAccount()
    {
        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () {
            // $targets: 対象データの配列 (array)
            $targets = $this->request->getData('targets');

            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->get($id);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->doUnlockAccount($user, $requestData)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            // 全データDB保存成功時: コミット
            return $this->success('I-UNLOCK-ACCOUNT');
        });

        $this->set(compact('result'));
    }

    /**
     * パスワード発行API
     * 既にパスワード発行済みの場合、パスワードを上書き（再発行）する
     *
     * @return \Cake\Http\Response|null
     */
    public function passwordIssue()
    {
        // $users: CSV出力データの配列
        $users = [];

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use (&$users) {
            // $targets: 対象データの配列 (array)
            $targets = $this->request->getData('targets');

            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->get($id);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->doIssuePassword($user, $requestData)) {
                    // CSV出力用にデータを退避する
                    $users[] = $user;
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            // 全データDB保存成功時: コミット
            return $this->success();
        });

        if ($result) {
            // CSV ダウンロード
            $this->viewBuilder()->setClassName('Csv');
            $this->set(compact('users'));
            return;
        }
        $this->set(compact('result'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () {
            // $targets: 対象データの配列 (array)
            $targets = $this->request->getData('targets');

            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->get($id);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->doDelete($user, $requestData)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            // 全データDB保存成功時: コミット
            return $this->success('I-DELETE');
        });

        $this->set(compact('result'));
    }

    /**
     * ユーザーエンティティを取得する.
     * 
     * @param int|string $id ユーザーID
     * @return \App\Model\Entity\User
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    private function getEntity($id)
    {
        if ($id === null) {
            return $this->Users->newEmptyEntity();
        }
        $user = $this->Users->get($id);
        return $user;
    }
}
