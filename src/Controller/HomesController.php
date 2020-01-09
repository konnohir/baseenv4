<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;

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
        $this->Authentication->allowUnauthenticated(['login', 'logout']);
    }

    /**
     * ログイン画面
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        $result = $this->Authentication->getResult();
        // If the user is logged in send them away.
        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? '/';
            return $this->redirect($target);
        }
        if ($this->getRequest()->is('post') && !$result->isValid()) {
            $this->Flash->error('Invalid username or password');
        }
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
