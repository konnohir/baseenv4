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
        $this->loadHelper('Konnohir.Paginator');
        $this->loadHelper('Konnohir.Html');
        $this->loadHelper('Konnohir.Form');
        $this->loadHelper('Konnohir.Permission');
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
