<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\I18n\FrozenDate;

/**
 * Homes Controller
 * ホーム
 * 
 * @property \App\Model\Table\UsersTable $Users
 */
class HomesController extends AppController
{
    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadModel('Users');

        // 未認証ユーザーがアクセス出来るアクション
        // logout はセッションタイムアウト時にログアウトボタンを押した場合に
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

            if (isset($user->login_failed_count)) {
                if ($user->login_failed_count >= 5) {
                    // ログイン失敗回数が規定値以上: ログアウトする
                    // E-LOCK-ACCOUNT: アカウントがロックされています。
                    $this->Flash->error(__('E-LOCK-ACCOUNT'));
                    return $this->logout();
                }
                if ($user->login_failed_count >= 1) {
                    // ログイン失敗回数が1回以上: ログイン失敗回数をリセットする
                    $user->login_failed_count = 0;
                    $this->Users->saveOrFail($user);
                }
            }

            if (isset($user->password_expired)) {
                if ($user->password_expired->diffInDays($user->created_at) === 0) {
                    // 初期パスワード: パスワード変更画面へ遷移する
                    // I-INITIAL-PASSWORD: 初期パスワードでログインしました。パスワードを変更してください。
                    $this->Flash->success(__('I-INITIAL-PASSWORD'));
                    return $this->redirect('/password');
                }
                if ($user->password_expired->isPast()) {
                    // パスワード有効期限切れ: パスワード変更画面へ遷移する
                    // I-EXPIRED-PASSWORD: パスワードの有効期限が切れています。パスワードを変更してください。
                    $this->Flash->success(__('I-EXPIRED-PASSWORD'));
                    return $this->redirect('/password');
                }
            }

            // 画面遷移：リダイレクト先URL、またはトップ画面
            return $this->redirect($this->Authentication->getLoginRedirect() ?? '/');
        }
        if ($this->getRequest()->is('post') && !$result->isValid()) {
            // ログイン失敗
            // E-LOGIN-FAILED: 入力内容に誤りがあります。
            $errorMessage = __('E-LOGIN-FAILED');

            // $user: ログインを試みたユーザー
            $user = $this->Users->find()
                ->select(['id', 'role_id', 'login_failed_count'])
                ->where(['email' => $this->getRequest()->getData('email')])
                ->first();

            // アカウントロックチェック
            if (isset($user->login_failed_count)) {
                if ($user->login_failed_count < 5) {
                    // ログイン失敗回数が規定値未満: ログイン失敗回数をインクリメント
                    $user->login_failed_count++;
                    $this->Users->saveOrFail($user);
                }
                if ($user->login_failed_count >= 5) {
                    // ログイン失敗回数が規定値以上: アカウントロックメッセージ表示
                    // E-LOCK-ACCOUNT: アカウントがロックされています。
                    $errorMessage = __('E-LOCK-ACCOUNT');
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
        // $user: ユーザー
        $user = $this->getRequest()->getAttribute('identity');

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

        // $user: ユーザー
        $user = $this->Users->find('detail', compact('id'))->first();
        if ($user === null) {
            return $this->logout();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['patch', 'post', 'put'])) {
            // パスワード変更
            $user = $this->Users->doChangePassword($user, $this->getRequest()->getData());

            // DB保存成功時: プロファイル画面へ遷移
            if ($this->Users->save($user)) {
                // I-PASSWORD-CHANGE: パスワードを変更しました。
                $this->Flash->success(__('I-PASSWORD-CHANGE'));
                return $this->redirect(['action' => 'profile']);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($user);
        }

        $this->set(compact('user'));
    }

    /**
     * キャッシュ削除API (デバッグ用/非公開)
     *
     * @return \Cake\Http\Response|null
     */
    public function refresh()
    {
        foreach (glob(CACHE . 'models' . DS . '*_*') as $file) {
            @unlink($file);
        }
        foreach (glob(CACHE . 'persistent' . DS . '*_*') as $file) {
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
