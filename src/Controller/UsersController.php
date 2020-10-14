<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * ユーザーマスタ
 * 
 * @property \App\Model\Table\UsersTable $Users
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
            // エンティティ編集
            $this->Users->doEditEntity($user, $this->request->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->Users->save($user)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $user->id]);
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
        // $targets: 対象データの配列 (array)
        $targets = $this->request->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->getEntity($id);

                // アカウントロック
                $this->Users->doLockAccount($user, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->save($user)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            $this->Flash->success(__('I-LOCK-ACCOUNT'));
            return true;
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
        // $targets: 対象データの配列 (array)
        $targets = $this->request->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                $user = $this->Users->find('detail', compact('id'))->firstOrFail();

                // アカウントロック解除
                $this->Users->doUnlockAccount($user, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->save($user)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            $this->Flash->success(__('I-UNLOCK-ACCOUNT'));
            return true;
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
        // $targets: 対象データの配列 (array)
        $targets = $this->request->getData('targets');

        // $csv: CSV出力データの配列
        $csv = [];

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets, &$csv) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->getEntity($id);

                // パスワード発行
                $this->Users->doIssuePassword($user, $requestData);

                // CSV出力用にデータを退避
                $csv[] = [$user->id, $user->email, $user->plain_password];

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->save($user)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            return true;
        });

        if ($result) {
            // CSVヘッダ
            array_unshift($csv, ['ID', 'Email', 'Password']);
            // CSV ダウンロード
            $this->set('csv', $csv);
            $this->viewBuilder()->setClassName('Csv');
            $this->setResponse($this->getResponse()->withDownload('password.csv')->withType('text/csv'));
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
        // $targets: 対象データの配列 (array)
        $targets = $this->request->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->getEntity($id);

                // 削除
                $this->Users->doDeleteEntity($user, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Users->save($user)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($user);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }

    /**
     * ユーザーエンティティを取得する.
     * 
     * @param mixed $id ユーザーID
     * @return \App\Model\Entity\User
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
