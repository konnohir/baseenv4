<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Roles Controller
 * 権限マスタ
 * 
 * @property \App\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
{
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

        $this->loadModel('Roles');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $roles: 権限一覧
        $roles = $this->paginate($this->Roles);

        $this->set(compact('roles'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 権限 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($id)
    {
        // $role: 権限エンティティ
        $role = $this->Roles->find('detail', compact('id'))->firstOrFail();

        // $roleDetails: 権限詳細エンティティの配列
        $roleDetails = $this->Roles->RoleDetails->find('overview')->all();

        $this->set(compact('role', 'roleDetails'));
    }

    /**
     * 新規登録画面
     *
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function add()
    {
        return $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id 権限 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($id = null)
    {
        // $role: 権限エンティティ
        if ($id === null) {
            $role = $this->Roles->newEmptyEntity();
        } else {
            $role = $this->Roles->find('detail', compact('id'))->firstOrFail();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // エンティティ編集
            $role = $this->Roles->doEditEntity($role, $this->getRequest()->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $role->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($role);
        }

        // $roleDetails: 権限詳細エンティティの配列
        $roleDetails = $this->Roles->RoleDetails->find('overview')->all();

        $this->set(compact('role', 'roleDetails'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function delete()
    {
        // $targets: 対象データの配列 (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクション実行結果 (boolean)
        $result = $this->Roles->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $role: 権限エンティティ
                $role = $this->Roles->find('detail', compact('id'))->firstOrFail();

                // 削除
                $role = $this->Roles->doDeleteEntity($role, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->Roles->save($role)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($role);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
