<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * ホーム
 * 
 * @property \App\Model\Table\UsersTable $Users
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class HomesController extends AppController
{
    /**
     * @var アカウントロック閾値
     *      (Note: 変更時、DBのv_user_remarksビュー定義も更新が必要)
     */
    const ACCOUNT_LOCK_VALUE = 5;

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
        // $result: 認証結果 (認証処理の実行はAuthenticationMiddlewareの役割)
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            // ログイン成功、または既にログイン済み
            return $this->loginSuccess($result->getData());
        }

        if ($this->request->is('post') && !$result->isValid()) {
            // ログイン失敗
            $this->loginFailed();
        }
    }

    /**
     * プロファイル画面
     * ユーザー情報はセッションから取得する
     *
     * @return \Cake\Http\Response|null
     */
    public function profile()
    {
        // $id: ログイン中のユーザーのユーザーID
        $id = $this->Authentication->getIdentityData('id');

        // $user: ユーザー
        $user = $this->Users->find('detail', compact('id'))->firstOrFail();

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
        $id = $this->Authentication->getIdentityData('id');

        // $user: ユーザー
        $user = $this->Users->find('detail', compact('id'))->firstOrFail();

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // DB保存成功時: プロファイル画面へ遷移
            if ($this->Users->doChangePassword($user, $this->request->getData())) {
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
        foreach (glob(CACHE . '*') as $file) {
            @unlink($file);
        }
        return $this->redirect($this->referer());
    }

    /**
     * ログアウトAPI
     *
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        $this->Authentication->logout();
        return $this->redirect(['action' => 'login']);
    }

    /**
     * ログイン成功時の処理
     * 
     * @var \App\Model\Entity\User $user ログインユーザー
     * @return \Cake\Http\Response
     */
    protected function loginSuccess($user)
    {
        if (isset($user->login_failed_count)) {
            if ($user->login_failed_count >= self::ACCOUNT_LOCK_VALUE) {
                // ログイン失敗回数が規定値以上: ログアウトする
                // E-LOCK-ACCOUNT: アカウントがロックされています。
                $this->Flash->error(__('E-LOCK-ACCOUNT'));
                return $this->logout();
            }
            if ($user->login_failed_count >= 1) {
                // ログイン失敗回数が1回以上: ログイン失敗回数をリセットする
                $this->Users->doResetLoginFailedCount($user);
            }
        }

        if (isset($user->password_expired)) {
            if ($user->password_expired->diffInDays($user->created_at) === 0) {
                // 初期パスワード: パスワード変更画面へ遷移する
                // I-INITIAL-PASSWORD: 初期パスワードでログインしました。パスワードを変更してください。
                $this->Flash->success(__('I-INITIAL-PASSWORD'));
                return $this->redirect(['action' => 'password']);
            }
            if ($user->password_expired->isPast()) {
                // パスワード有効期限切れ: パスワード変更画面へ遷移する
                // I-EXPIRED-PASSWORD: パスワードの有効期限が切れています。パスワードを変更してください。
                $this->Flash->success(__('I-EXPIRED-PASSWORD'));
                return $this->redirect(['action' => 'password']);
            }
        }

        // 画面遷移：リダイレクト先URL、またはトップ画面
        return $this->redirect($this->Authentication->getLoginRedirect() ?? '/');
    }

    /**
     * ログイン失敗時の処理
     * 
     * @return void
     */
    protected function loginFailed()
    {
        // E-LOGIN-FAILED: ログインに失敗しました。
        $errorMessage = __('E-LOGIN-FAILED');

        // $user: ログインを試みたユーザー
        $user = $this->Users->find()
            ->select(['id', 'login_failed_count'])
            ->where(['email' => $this->request->getData('email')])
            ->first();

        // アカウントロックチェック
        if (isset($user->login_failed_count)) {
            if ($user->login_failed_count < self::ACCOUNT_LOCK_VALUE) {
                // ログイン失敗回数が規定値未満: ログイン失敗回数をインクリメント
                $this->Users->doIncrementLoginFailedCount($user);
            }
            if ($user->login_failed_count >= self::ACCOUNT_LOCK_VALUE) {
                // ログイン失敗回数が規定値以上: アカウントロックメッセージ表示
                // E-LOCK-ACCOUNT: アカウントがロックされています。
                $errorMessage = __('E-LOCK-ACCOUNT');
            }
        }

        // エラーメッセージ表示
        $this->Flash->error($errorMessage);
    }
}
