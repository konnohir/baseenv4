<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

/**
 * Homes Controller
 * ホーム
 */
class HomesController extends AppController
{
    public $title = 'ホーム';

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadModel('Users');

        // 未認証時にアクセスを許可するアクション
        // logout はセッションタイムアウト時にログアウトボタンを押したときに
        // ログイン後のリダイレクト先がlogoutアクションになるのを防ぐため.
        $this->Authentication->allowUnauthenticated(['login', 'logout']);
    }

    /**
     * トップ画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
    }

    /**
     * ログイン画面
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        // $result: 認証結果 (認証処理はミドルウェアが自動的に実行する)
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            // ログイン成功、または既にログイン済み

            // $user: ログイン中のユーザー
            $user = $result->getData();

            // アカウントロックチェック
            if ($user->login_failed_count >= 5) {
                // ログイン失敗回数が規定値以上: ログアウトする
                $this->Flash->error(__('アカウントがロックされています。'));
                return $this->logout();
            }elseif($user->login_failed_count >= 1) {
                // ログイン失敗回数が1回以上: ログイン失敗回数をリセット
                $user->login_failed_count = 0;
                $this->Users->saveOrFail($user);
            }

            // 画面遷移：リダイレクト先URL、またはトップ画面
            return $this->redirect($this->Authentication->getLoginRedirect() ?? '/');
        }
        if ($this->getRequest()->is('post') && !$result->isValid()) {
            // ログイン失敗
            $errorMessage = __('入力内容に誤りがあります。');

            // $user: ログインを試みたユーザー
            $user = $this->Users->find()
                ->where(['email' => $this->getRequest()->getData('email')])
                ->first();

            // アカウントロックチェック
            if ($user) {
                if ($user->login_failed_count < 5) {
                    // ログイン失敗回数が規定値未満: ログイン失敗回数をインクリメント
                    $user->login_failed_count++;
                    $this->Users->saveOrFail($user);
                }
                if ($user->login_failed_count >= 5) {
                    // ログイン失敗回数が規定値以上: アカウントロックメッセージ表示
                    $errorMessage = __('アカウントがロックされています。');
                }
            }
            $this->Flash->error($errorMessage);
        }
    }

    /**
     * プロファイル画面
     *
     * @return \Cake\Http\Response|null
     */
    public function profile()
    {
        // $id: ログイン中のユーザーのユーザーID
        $id = $this->getRequest()->getAttribute('identity')->id;

        // $user: ユーザーマスタ
        $user = $this->Users->find('detail', compact('id'))->first();
        if ($user === null) {
            return $this->logout();
        }

        $this->set(compact('user'));
    }

    /**
     * パスワード変更画面
     *
     * @return \Cake\Http\Response|null
     */
    public function password()
    {
        // $id: ログイン中のユーザーのユーザーID
        $id = $this->getRequest()->getAttribute('identity')->id;

        // $user: ユーザーマスタ
        $user = $this->Users->find('detail', compact('id'))->first();
        if ($user === null) {
            return $this->logout();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->getRequest()->getData(), [
                'fields' => [
                    'current_password', 'password', 'retype_password',
                    // lock flag
                    '_lock',
                ],
                'associated' => [
                ],
                'validate' => 'password',
            ]);
            
            // DB保存成功時: プロファイル画面へ遷移
            if ($this->Users->save($user)) {
                $this->Flash->success(__('パスワードを変更しました。'));
                return $this->redirect(['action' => 'profile']);
            }

            // DB保存失敗時: 画面を再表示
            $errorMessage = __('入力内容に誤りがあります。');
            if ($user->getError('_lock')) {
                $errorMessage = current($user->getError('_lock'));
            }
            $this->Flash->error($errorMessage);
        }

        $this->set(compact('user'));
    }

    /**
     * キャッシュ削除 (デバッグ用)
     */
    public function refresh() {
        foreach(glob(CACHE . 'models' . DS . '*_*') as $file) {
            @unlink($file);
        }foreach(glob(CACHE . 'persistent' . DS . '*_*') as $file) {
            @unlink($file);
        }
        return $this->redirect($this->referer());
    }

    /**
     * ログアウト
     *
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        $this->Authentication->logout();
        return $this->redirect('/login');
    }
}
