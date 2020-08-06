<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * MDepartment1s Controller
 * 組織マスタ
 * 
 * @property \App\Model\Table\Department1sTable $Department1s
 */
class MDepartment1sController extends AppController
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

        $this->loadModel('MDepartment1s');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $mDepartment1s: 組織一覧
        $mDepartment1s = $this->paginate($this->MDepartment1s, [
            'order' => ['code'],
        ]);

        $this->set(compact('mDepartment1s'));
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
        // $mDepartment1: 組織エンティティ
        $mDepartment1 = $this->MDepartment1s->find('detail', compact('id'))->firstOrFail();

        $this->set(compact('mDepartment1'));
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
        // $mDepartment1: 組織エンティティ
        if ($id === null) {
            $mDepartment1 = $this->MDepartment1s->newEmptyEntity();
        } else {
            $mDepartment1 = $this->MDepartment1s->find('detail', compact('id'))->firstOrFail();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // エンティティ編集
            $mDepartment1 = $this->MDepartment1s->doEditEntity($mDepartment1, $this->getRequest()->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->MDepartment1s->save($mDepartment1)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $mDepartment1->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($mDepartment1);
        }

        $this->set(compact('mDepartment1'));
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

        // $result: トランザクションの結果 (boolean)
        $result = $this->MDepartment1s->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $mDepartment1: 組織エンティティ
                $mDepartment1 = $this->MDepartment1s->find('detail', compact('id'))->firstOrFail();

                // 削除
                $mDepartment1 = $this->MDepartment1s->doDeleteEntity($mDepartment1, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->MDepartment1s->save($mDepartment1)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($mDepartment1);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
