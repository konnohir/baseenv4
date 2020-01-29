<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

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
            'accountLock' => ['requestTarget'],
            'accountUnlock' => ['requestTarget'],
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
        try {
            $users = $this->paginate($this->Users, [
                'sortWhitelist' => [
                    'Users.email',
                    'Users.password_issue',
                    'Users.login_failed_count',
                    'Roles.id',
                ],
            ]);
        } catch (NotFoundException $e) {
            return $this->redirect($this->paginate['firstPage']);
        }
        $this->set(compact('users'));
    }

    /**
     * 詳細画面
     *
     * @param string $id ユーザーエンティティ id.
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // $user: ユーザーエンティティ
        $user = $this->Users->find('detail', compact('id'))->first();

        // データ取得失敗時: 共通エラー画面を表示
        if ($user === null) {
            return $this->render('/Error/not_found');
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
     * @param string|null $id ユーザーエンティティ id.
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

        // データ取得失敗時: 共通エラー画面を表示
        if ($user === null) {
            return $this->render('/Error/not_found');
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['patch', 'post', 'put'])) {
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
            $this->viewBuilder()->setClassName('Csv');
            $this->set('csv', $csv);
            return;
        }

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

        // 画面を再表示
        return $this->redirect($this->referer());
    }
}
