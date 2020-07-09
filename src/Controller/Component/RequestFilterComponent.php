<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;

/**
 * RequestFilterComponent
 * 各アクションの実行前にHTTPリクエストをフィルタリングする
 */
class RequestFilterComponent extends Component
{
    /**
     * beforeFilter
     * 各アクションの実行前にトリガーされる
     * 
     * @param Event $event
     * @return \Cake\Http\Response|void
     */
    public function beforeFilter(Event $event)
    {
        // $action: リクエストされたアクション
        $action = $this->getRequest()->getParam('action');

        // $filters: 適用するフィルタの配列
        $filters = $this->getConfig($action);
        if (!isset($filters)) {
            return;
        }

        foreach($filters as $method) {
            $method .= 'Filter';
            if ($result = $this->$method()) {
                $event->stopPropagation();
                return $result;
            }
        }
    }

    /**
     * 検索フィルタ
     * POST送信された検索リクエストをGETリクエストに変換する
     * (PostRedirectGetパターン)
     * 
     * @return \Cake\Http\Response|null
     */
    public function paginateFilter()
    {
        // POST送信された場合
        if ($this->getRequest()->is('post')) {
            // POSTされたデータをクエリ文字列 (URLの?から後ろの部分)にして、生成したURLへリダイレクトする
            $routes = [
                'action' => $this->getRequest()->getParam('action')
            ];
            if ($this->getRequest()->getParam('pass.0')) {
                $routes[] = $this->getRequest()->getParam('pass.0');
            }
            $routes['?'] = array_filter((array)$this->getRequest()->getData('filter'), function ($row) {
                return (is_array($row) || mb_strlen($row) !== 0);
            });
            return $this->getController()->redirect($routes);
        }
            
        // $filterArgs: クエリ文字列(URLの?から後ろの部分)の配列
        $filterArgs = $this->getRequest()->getQuery();

        // $newRequest: 新しいリクエストオブジェクト
        $newRequest = $this->getRequest()->withData('filter', $filterArgs);
        $this->getController()->setRequest($newRequest);

        // paginateプロパティを自動設定
        $this->getController()->paginate['finder']['overview'] = ['filter' => $filterArgs];
    }

    /**
     * IDフィルタ
     * 第1引数が数値であるかチェックする
     * 
     * @throws Cake\Http\Exception\BadRequestException
     * @return void
     */
    public function requestIdFilter()
    {
        // $id: URLの1番目の引数
        $id = $this->getRequest()->getParam('pass.0', '');

        // 数字以外(先頭0不可)ならBadRequestExceptionをスローする
        if (!preg_match('/^[1-9]\d*$/', $id)) {
            throw new BadRequestException();
        }
    }

    /**
     * ターゲットフィルタ
     * targetsキーがPOST送信されているかチェックする
     * 
     * @throws Cake\Http\Exception\BadRequestException
     * @return void
     */
    public function requestTargetFilter()
    {
        // HTTPメソッドを制限する
        $this->getRequest()->allowMethod(['post', 'put', 'patch']);

        // $targets: POSTデータ
        $targets = $this->getRequest()->getData('targets');

        // 要素数が0ならBadRequestExceptionをスローする
        if (!is_array($targets) || count($targets) === 0) {
            throw new BadRequestException();
        }

        // 各要素が配列でなければBadRequestExceptionをスローする
        foreach($targets as $target) {
            if (!is_array($target)) {
                throw new BadRequestException();
            }
        }
        
        // レスポンスをJSON形式にする
        $this->getController()->viewBuilder()
            ->setClassName('Json')
            ->setOption('serialize', true);
    }

    /**
     * ServerRequestイブジェクト取得
     * @return \Cake\Http\ServerRequest
     */
    protected function getRequest()
    {
        return $this->_registry->getController()->getRequest();
    }
}
