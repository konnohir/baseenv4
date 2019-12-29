<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;

/**
 * FilterComponent
 * 各アクションの実行前に共通処理を実行する
 */
class FilterComponent extends Component
{
    /**
     * 設定値
     * @var array
     */
    protected $config;

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->config = $config;
    }

    /**
     * beforeFilter
     * 各アクションの実行前にフレームワークが呼び出す。
     */
    public function beforeFilter(Event $event)
    {
        $action = $this->getRequest()->getParam('action');

        if (isset($this->config['paginate'][$action])) {
            $this->filterPaginate();
        }
        if (isset($this->config['requestId'][$action])) {
            $this->filterRequestId();
        }
        if (isset($this->config['requestTarget'][$action])) {
            $this->filterRequestTarget();
        }
    }

    // indexアクションの場合
    public function filterPaginate()
    {
        // POST送信された場合
        if ($this->getRequest()->is('post')) {
            // POSTされたデータをクエリ文字列 (URLの?から後ろの部分)にして、そのURLへリダイレクトする
            return $this->getController()->redirect([
                'action' => $this->getRequest()->getParam('action'),
                '?' => array_filter((array)$this->getRequest()->getData('filter'), function ($row) {
                    return (is_array($row) || mb_strlen($row) !== 0);
                })
            ]);
        }
            
        // $filterArgs: クエリ文字列(URLの?から後ろの部分)の配列
        $filterArgs = $this->getRequest()->getQuery();

        // $newRequest: 新しいリクエストオブジェクト
        $newRequest = $this->getRequest()->withData('filter', $filterArgs);
        $this->getController()->setRequest($newRequest);

        // paginateプロパティを更新
        $this->getController()->paginate['finder']['overview'] = ['filter' => $filterArgs];
        $this->getController()->paginate['firstPage'] = [
            'action' => $this->getRequest()->getParam('action'),
            '?' => ['page' => null] + $filterArgs,
        ];
    }

    // view, edit アクションの場合
    public function filterRequestId()
    {
        // $id: URLの1番目の引数
        $id = $this->getRequest()->getParam('pass.0', '');

        // 数字以外(先頭0不可)ならBadRequestExceptionをスローする
        if (!preg_match('/^[1-9]\d*$/', $id)) {
            throw new BadRequestException();
        }
    }

    // ajax アクションの場合
    public function filterRequestTarget()
    {
        // アクセスするHTTPメソッドを制限する
        $this->getRequest()->allowMethod(['post']);

        // $targets: POSTデータ
        $targets = $this->getRequest()->getData('targets');

        // 要素数が0ならBadRequestExceptionをスローする
        if (!is_array($targets) || count($targets) === 0) {
            throw new BadRequestException();
        }
    }

    /**
     * ServerRequestイブジェクト取得
     * @return \Cake\Http\ServerRequest
     */
    protected function getRequest()
    {
        return $this->getController()->getRequest();
    }
}
