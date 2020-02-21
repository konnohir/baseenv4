<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 * ユーザーマスタ
 * 
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
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

        $this->loadModel('Users');
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
            'sortWhitelist' => [
                'Users.email',
                'Users.password_issue',
                'Users.login_failed_count',
                'Roles.id',
            ],
        ]);

        $this->set(compact('users'));
    }

    /**
     * 詳細画面
     *
     * @param string $id ユーザー id.
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // $user: ユーザーエンティティ
        $user = $this->Users->find('detail', compact('id'))->first();

        // データ取得失敗時: 一覧画面へ遷移 (検索条件クリア)
        if ($user === null) {
            // E-V-NOT-FOUND:対象の{0}が存在しません
            $this->Flash->error(__('E-NOT-FOUND', __($this->title)), ['clear' => true]);
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('user'));
    }

    /**
     * 新規登録画面
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id ユーザー id.
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $user: ユーザーエンティティ
        if ($this->isAdd()) {
            $user = $this->Users->newEmptyEntity();
        } else {
            $user = $this->Users->find('detail', compact('id'))->first();
        }

        // データ取得失敗時: 一覧画面へ遷移 (検索条件クリア)
        if ($user === null) {
            // E-V-NOT-FOUND:対象の{0}が存在しません
            $this->Flash->error(__('E-NOT-FOUND', __($this->title)), ['clear' => true]);
            return $this->redirect(['action' => 'index']);
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['post', 'put', 'patch'])) {
            // エンティティ編集
            $user = $this->Users->doEditEntity($user, $this->getRequest()->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->Users->save($user)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $user->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($user);
        }

        // $roleList: 権限リスト
        $roleList = $this->Users->Roles->find('list')->toArray();

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
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->find('detail', compact('id'))->first();

                // データ取得失敗時: ロールバック
                if ($user === null) {
                    return $this->failed($user);
                }

                // アカウントロック
                $user = $this->Users->doLockAccount($user, $requestData);

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

        // 画面を再表示
        return $this->redirect($this->referer());
    }

    /**
     * アカウントロック解除API
     *
     * @return \Cake\Http\Response|null
     */
    public function unlockAccount()
    {
        // $targets: 対象データの配列 (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->find('detail', compact('id'))->first();

                // データ取得失敗時: ロールバック
                if ($user === null) {
                    return $this->failed($user);
                }

                // アカウントロック
                $user = $this->Users->doUnlockAccount($user, $requestData);

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

        // 画面を再表示
        return $this->redirect($this->referer());
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
        $targets = $this->getRequest()->getData('targets');

        // $csv: CSV出力データの配列
        $csv = [];

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets, &$csv) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->find('detail', compact('id'))->first();

                // データ取得失敗時: ロールバック
                if ($user === null) {
                    return $this->failed($user);
                }

                // パスワード発行
                $user = $this->Users->doIssuePassword($user, $requestData);

                // CSV へ出力
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
            $this->setResponse($this->getResponse()->withDownload('password.csv'));
            return;
        }

        // 画面を再表示
        return $this->redirect($this->referer());
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $targets: 対象データの配列 (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $user: ユーザーエンティティ
                $user = $this->Users->find('detail', compact('id'))->first();

                // データ取得失敗時: ロールバック
                if ($user === null) {
                    return $this->failed($user);
                }

                // 削除
                $user = $this->Users->doDeleteEntity($user, $requestData);

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

        // 画面を再表示、または一覧画面へ遷移 (検索条件クリア)
        $redirectUrl = $this->referer();
        if (strpos($redirectUrl, 'view') !== false) {
            $redirectUrl = ['action' => 'index'];
        }
        return $this->redirect($redirectUrl);
    }
}
