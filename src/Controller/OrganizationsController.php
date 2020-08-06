<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Organizations Controller
 * 組織マスタ
 * 
 * @property \App\Model\Table\OrganizationsTable $Organizations
 */
class OrganizationsController extends AppController
{
    public $title = '組織';

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

        $this->loadModel('Organizations');
        $this->loadModel('DepartmentLevel3s');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $organizations: 組織一覧
        $organizations = $this->paginate($this->Organizations, [
            'sortableFields' => [
                'DepartmentLevel1s.name',
                'DepartmentLevel2s.name',
                'DepartmentLevel3s.name',
            ],
            'order' => [],
        ]);

        $this->set(compact('organizations'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 組織 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($id)
    {
        // $organization: 組織エンティティ
        $organization = $this->Organizations->find('detail', compact('id'))->firstOrFail();

        $this->set(compact('organization'));
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
     * @param string|null $id 組織 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($id = null)
    {
    }
}
