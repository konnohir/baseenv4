<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadHelper('Authentication.Identity');
        $this->loadHelper('Fsi.Paginator');
        $this->loadHelper('Fsi.Html');
        $this->loadHelper('Fsi.Form');
        $this->loadHelper('Fsi.Permission');
    }
    
    /**
     * 新規登録画面判定
     */
    public function isAdd()
    {
        if (!isset($this->isAdd)) {
            $this->isAdd = strpos($this->getRequest()->getRequestTarget(), 'add');
        }
        return $this->isAdd;
    }
}
