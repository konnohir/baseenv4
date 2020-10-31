<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * 権限マスタ
 * 
 * @property \App\Model\Table\RolesTable $Roles
 * @property \App\Model\Table\RoleDetailsTable $RoleDetails
 */
class RolesController extends AppController
{
    /**
     * @var string 画面タイトル
     */
    public $title = '権限';

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

        // 権限マスタ
        $this->loadModel('Roles');

        // 権限詳細マスタ
        $this->loadModel('RoleDetails');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $roles: 権限一覧
        $roles = $this->paginate($this->Roles, [
            // 取得カラム
            'fields' => [
                // 主キー
                'Roles.id',
                // 権限名称
                'Roles.name',
                // 説明文
                'Roles.description',
                // 更新日時
                'Roles.updated_at',
            ],
            // 整列可能カラム
            'sortableFields' => [
                'Roles.id',
                'Roles.name',
                'Roles.description',
            ],
            // 並び順
            'order' => [
                'Roles.id' => 'asc'
            ],
        ]);

        $this->set(compact('roles'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 権限ID
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // $role: 権限エンティティ
        $role = $this->getEntity($id);

        // $roleDetails: 権限詳細エンティティの配列
        $roleDetails = $this->RoleDetails->find('threaded')->all();

        $this->set(compact('role', 'roleDetails'));
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
     * @param string|null $id 権限ID
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $role: 権限エンティティ
        $role = $this->getEntity($id);

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {

            // DB保存成功時: 詳細画面へ遷移
            if ($this->Roles->doEditEntity($role, $this->request->getData())) {
                // I-SAVE: 権限を保存しました。
                $this->success('I-SAVE', $this->title);

                return $this->redirect(['action' => 'view', $role->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($role);
        }

        // $roleDetails: 権限詳細エンティティの配列
        $roleDetails = $this->RoleDetails->find('threaded')->all();

        $this->set(compact('role', 'roleDetails'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $result: トランザクション実行結果 (boolean)
        $result = $this->Roles->getConnection()->transactional(function () {
            // $targets: 対象データの配列 (array)
            $targets = $this->request->getData('targets');
    
            foreach ($targets as $id => $requestData) {
                // $role: 権限エンティティ
                $role = $this->Roles->get($id);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Roles->doDeleteEntity($role, $requestData)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($role);
            }

            // 全データDB保存成功時: コミット
            return $this->success('I-DELETE', $this->title);
        });

        $this->set(compact('result'));
    }

    /**
     * 権限エンティティを取得する.
     * 
     * @param int|string $id 権限ID
     * @return \App\Model\Entity\Role
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    private function getEntity($id)
    {
        if ($id === null) {
            return $this->Roles->newEmptyEntity();
        }
        $role = $this->Roles->get($id, [
            'contain' => ['RoleDetails']
        ]);
        return $role;
    }
}
