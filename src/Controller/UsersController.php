<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Users Controller
 * ユーザーマスタ
 */
class UsersController extends AppCrudController
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

        $this->loadModel('Users');
    }
    
    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $users: ユーザーマスタの配列
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
     * @param string|null $id ユーザーマスタ id.
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        // $user: ユーザーマスタ
        $user = $this->Users->get($id, [
            'finder' => 'detail',
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
        $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id ユーザーマスタ id.
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $user: ユーザーマスタ
        if ($this->isAdd()) {
            $user = $this->Users->newEmptyEntity();
        } else {
            $user = $this->Users->get($id, [
                'finder' => 'detail'
            ]);
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->getRequest()->getData(), [
                'fields' => [
                    'email', 'role_id',
                    // lock flag
                    '_lock',
                ],
                'associated' => [
                ]
            ]);
            
            // DB保存成功時: 詳細画面へ遷移
            if ($this->Users->save($user)) {
                $this->Flash->success(__('{0}を保存しました。', __($this->title)));
                return $this->redirect(['action' => 'view', $user->id]);
            }

            // DB保存失敗時: 画面を再表示
            $errorMessage = __('入力内容に誤りがあります。');
            if ($user->getError('_lock')) {
                $errorMessage = current($user->getError('_lock'));
            }
            $this->Flash->error($errorMessage);
        }

        $roleList = $this->Users->Roles->find('list')->toArray();

        $this->set(compact('user', 'roleList'));
    }

    /**
     * パスワード再発行API
     *
     * @return \Cake\Http\Response|null
     */
    public function passwordIssue()
    {
        // $targets: 削除するユーザーマスタの配列 [ID => 更新日付] (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean|array)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            $csv = [['Email', 'Password']];
            foreach ($targets as $id => $_lock) {
                // $user: ユーザーマスタ
                $user = $this->Users->get($id, [
                    'fields' => [
                        'id',
                        'email',
                        'role_id',
                        'updated_at',
                        'deleted_at',
                    ]
                ]);

                // 排他制御
                $user->_lock = $_lock;

                // パスワード
                $password = 'pass#12345';
                $user->password = $password;
                $csv[] = [$user->email, $password];

                // DB保存失敗時: ロールバック
                if (!$this->Users->save($user)) {
                    $errorMessage = __('入力内容に誤りがあります。');
                    if ($user->getError('_lock')) {
                        $errorMessage = current($user->getError('_lock'));
                    }
                    $this->Flash->error($errorMessage);
                    return false;
                }

                // DB保存成功時: 次のデータの処理へ進む
            }

            return $csv;
        });

        if ($result) {
            // CSV ダウンロード
            $this->viewBuilder()->setClassName('Csv');
            $this->set('csv', $result);
            return;
        }

        return $this->redirect($this->referer());
    }

    /**
     * アカウントロックAPI
     *
     * @return \Cake\Http\Response|null
     */
    public function accountLock()
    {
        // $targets: 削除するユーザーマスタの配列 [ID => 更新日付] (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $_lock) {
                // $user: ユーザーマスタ
                $user = $this->Users->get($id, [
                    'fields' => [
                        'id',
                        'role_id',
                        'updated_at',
                        'deleted_at',
                    ]
                ]);

                // 排他制御
                $user->_lock = $_lock;

                // ログイン失敗回数
                $user->login_failed_count = 99;

                // DB保存失敗時: ロールバック
                if (!$this->Users->save($user)) {
                    $errorMessage = __('入力内容に誤りがあります。');
                    if ($user->getError('_lock')) {
                        $errorMessage = current($user->getError('_lock'));
                    }
                    $this->Flash->error($errorMessage);
                    return false;
                }

                // DB保存成功時: 次のデータの処理へ進む
            }

            $this->Flash->success(__('アカウントをロックしました。'));
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
    public function accountUnlock()
    {
        // $targets: 削除するユーザーマスタの配列 [ID => 更新日付] (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $_lock) {
                // $user: ユーザーマスタ
                $user = $this->Users->get($id, [
                    'fields' => [
                        'id',
                        'role_id',
                        'updated_at',
                        'deleted_at',
                    ]
                ]);

                // 排他制御
                $user->_lock = $_lock;

                // ログイン失敗回数
                $user->login_failed_count = 0;

                // DB保存失敗時: ロールバック
                if (!$this->Users->save($user)) {
                    $errorMessage = __('入力内容に誤りがあります。');
                    $errorMessage .= "\n・" . current(current($user->getErrors()));
                    if ($user->getError('_lock')) {
                        $errorMessage = current($user->getError('_lock'));
                    }
                    $this->Flash->error($errorMessage);
                    return false;
                }

                // DB保存成功時: 次のデータの処理へ進む
            }

            $this->Flash->success(__('アカウントロックを解除しました。'));
            return true;
        });

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
        // $targets: 削除するユーザーマスタの配列 [ID => 更新日付] (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクションの結果 (boolean)
        $result = $this->Users->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $_lock) {
                // $user: ユーザーマスタ
                $user = $this->Users->get($id, [
                    'fields' => [
                        'id',
                        'role_id',
                        'updated_at',
                        'deleted_at',
                    ]
                ]);

                // 排他制御
                $user->_lock = $_lock;

                // 削除日付
                $user->deleted_at = date('Y-m-d h:i:s');

                // DB保存失敗時: ロールバック
                if (!$this->Users->save($user)) {
                    $errorMessage = __('入力内容に誤りがあります。');
                    if ($user->getError('_lock')) {
                        $errorMessage = current($user->getError('_lock'));
                    }
                    $this->Flash->error($errorMessage);
                    return false;
                }

                // DB保存成功時: 次のデータの処理へ進む
            }

            $this->Flash->success(__('{0}を削除しました。', __($this->title)));
            return true;
        });

        // 画面を再表示
        return $this->redirect($this->referer());
    }
}
