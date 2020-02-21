<?php

declare(strict_types=1);

namespace App\View;

use Cake\View\View;

/**
 * Application View
 * アプリケーションビュー
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
