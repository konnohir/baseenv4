<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * 権限詳細マスタ
 * 
 * @property \App\Model\Table\RoleDetailsTable $RoleDetails
 */
class RoleDetailsController extends AppController
{
    /**
     * @var string 画面タイトル
     */
    public $title = '権限詳細';

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // リクエストフィルタ
        $this->loadComponent('RequestFilter', [
            'index' => ['paginate'],
            'view' => ['requestId'],
            'edit' => ['requestId'],
            'delete' => ['requestTarget'],
        ]);

        $this->loadModel('RoleDetails');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $roleDetails: 権限詳細一覧
        $roleDetails = $this->paginate($this->RoleDetails);

        $this->set(compact('roleDetails'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 権限詳細ID
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // $roleDetail: 権限詳細エンティティ
        $roleDetail = $this->RoleDetails->find('detail', compact('id'))->firstOrFail();

        // $acos: コントローラー／アクション(Access Control Object)のリスト (スレッド形式)
        $acos = $this->RoleDetails->Acos->find('threaded')->all();
        $acos = array_filter($acos->first()->children ?? [], function ($row) {
            return !empty($row->children);
        });

        $this->set(compact('roleDetail', 'acos'));
    }

    /**
     * 新規登録画面
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        return $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id 権限詳細ID
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $roleDetail: 権限詳細エンティティ
        if ($id === null) {
            $roleDetail = $this->RoleDetails->newEmptyEntity();
        } else {
            $roleDetail = $this->RoleDetails->find('detail', compact('id'))->firstOrFail();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['post', 'put', 'patch'])) {
            // エンティティ編集
            $roleDetail = $this->RoleDetails->doEditEntity($roleDetail, $this->request->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->RoleDetails->save($roleDetail)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $roleDetail->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($roleDetail);
        }

        // $parentRoleDetailList: 親権限詳細リスト
        $parentRoleDetailList = $this->RoleDetails->find('parentList', ['exclude' => $id])->toArray();

        // $acos: コントローラー／アクション(Access Control Object)のリスト (スレッド形式)
        $acos = $this->RoleDetails->Acos->find('threaded')->all();
        $acos = array_filter($acos->first()->children ?? [], function ($row) {
            return !empty($row->children);
        });

        $this->set(compact('roleDetail', 'parentRoleDetailList', 'acos'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $targets: 対象データの配列 (array)
        $targets = $this->request->getData('targets');

        // $result: トランザクション実行結果 (boolean)
        $result = $this->RoleDetails->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $roleDetail: 権限詳細エンティティ
                $roleDetail = $this->RoleDetails->find('detail', compact('id'))->firstOrFail();

                // 削除
                $roleDetail = $this->RoleDetails->doDeleteEntity($roleDetail, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->RoleDetails->save($roleDetail)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($roleDetail);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
