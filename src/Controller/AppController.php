<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\I18n\I18n;
use Cake\Core\Configure;

/**
 * Application Controller
 * 基底コントローラ
 */
class AppController extends Controller
{
    /**
     * 初期化処理
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
        $this->loadComponent('Acl.Acl');

        // 言語設定
        $user = $this->getRequest()->getAttribute('identity');
        if (isset($user) && !empty($user->language)) {
            I18n::setLocale($user->language);
        }
    }

}