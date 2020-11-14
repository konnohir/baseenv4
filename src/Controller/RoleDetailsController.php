<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * 権限詳細マスタ
 * 
 * @property \App\Model\Table\AcosTable $Acos
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

        // 権限詳細マスタ
        $this->loadModel('RoleDetails');

        // ACOマスタ
        $this->loadModel('Acos');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $roleDetails: 権限詳細一覧
        $roleDetails = $this->paginate($this->RoleDetails, [
            // 取得カラム
            'fields' => [
                // 主キー
                'RoleDetails.id',
                // 親権限詳細ID　(threaded用)
                'RoleDetails.parent_id',
                // 権限詳細名称
                'RoleDetails.name',
                // 説明文
                'RoleDetails.description',
                // 更新日時
                'RoleDetails.updated_at',
            ],
            // 整列可能カラム
            'sortableFields' => [
                'RoleDetails.id',
                'RoleDetails.name',
                'RoleDetails.description',
            ],
            // 並び順
            'order' => [
                'RoleDetails.id' => 'asc'
            ],
        ]);

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
        $roleDetail = $this->RoleDetails->get($id, [
            'contain' => [
                'Acos',
                'ParentRoleDetails'
            ],
        ]);

        // $acos: コントローラー／アクション(Access Control Object)のリスト (スレッド形式)
        $acos = $this->Acos->find('threadedActions')->all();

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
            $roleDetail = $this->RoleDetails->get($id, [
                'contain' => [
                    'Acos',
                ],
            ]);
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['post', 'put', 'patch'])) {
            // DB保存成功時: 詳細画面へ遷移
            if ($this->RoleDetails->doEditEntity($roleDetail, $this->request->getData())) {
                // I-SAVE: 権限詳細を保存しました。
                return $this->success('I-SAVE', __($this->title), ['action' => 'view', $roleDetail->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($roleDetail);
        }

        // $parentRoleDetailList: 親権限詳細リスト
        $parentRoleDetailList = $this->RoleDetails->find('parentList', ['excludeId' => $id])->toArray();

        // $acos: コントローラー／アクション(Access Control Object)のリスト (スレッド形式)
        $acos = $this->Acos->find('threadedActions')->all();

        $this->set(compact('roleDetail', 'parentRoleDetailList', 'acos'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $result: トランザクション実行結果 (boolean)
        $result = $this->RoleDetails->getConnection()->transactional(function () {
            // $targets: 対象データの配列 (array)
            $targets = $this->request->getData('targets');
    
            foreach ($targets as $id => $requestData) {
                // $roleDetail: 権限詳細エンティティ
                $roleDetail = $this->RoleDetails->get($id);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->RoleDetails->doDeleteEntity($roleDetail, $requestData)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($roleDetail);
            }

            // 全データDB保存成功時: コミット
            return $this->success('I-DELETE', $this->title);
        });

        $this->set(compact('result'));
    }
}
