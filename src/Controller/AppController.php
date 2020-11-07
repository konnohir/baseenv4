<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\I18n\I18n;
use Konnohir\Controller\PaginateTrait;

/**
 * アプリケーションコントローラ
 */
class AppController extends Controller
{
    use PaginateTrait;

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

        $user = $this->request->getAttribute('identity');

        // 言語設定
        if (isset($user->language)) {
            I18n::setLocale($user->language);
        }
    }

    /**
     * Util: 成功メッセージをセットする
     * 
     * @param mixed $successMessage メッセージ (可変長引数リスト)
     * @return bool true
     */
    protected function success(...$successMessage)
    {
        if (!empty($successMessage)) {
            $this->Flash->success(__(...$successMessage));
        }
        return true;
    }

    /**
     * Util: エラーメッセージをセットする
     * 
     * @param \Cake\Datasource\EntityInterface $entity エンティティ
     * @param bool $isShowDetail true: バリデーションエラーの詳細を1件出力する
     * @return bool false
     */
    protected function failed(?EntityInterface $entity, $isShowDetail = false)
    {
        if ($entity === null) {
            // E-V-NOT-FOUND: 対象の{0}が存在しません
            $this->Flash->error(__('E-NOT-FOUND', __($this->title)));
            return false;
        }
        if ($entity->getError('_lock')) {
            // E-V-LOCK: データが変更されているため、保存できません。
            $this->Flash->error(__('E-V-LOCK'));
            return false;
        }
        // E-V-WRONG-INPUT: 入力内容に誤りがあります。
        $errorMessage = __('E-V-WRONG-INPUT');
        if ($isShowDetail) {
            $errorMessage .= "\n・" . current(current($entity->getErrors()));
        }

        // デバッグログ
        if (Configure::read('debug')) {
            $this->log(var_export($entity->getErrors(), true), 'debug');
        }
        
        $this->Flash->error($errorMessage);
        return false;
    }
}
