<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * MDepartment2s Controller
 * 部店マスタ
 * 
 * @property \App\Model\Table\Department2sTable $Department2s
 */
class MDepartment2sController extends AppController
{
    public $title = '部店';

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
        $this->loadModel('MDepartment2s');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $mDepartment2s: 部店一覧
        $mDepartment2s = $this->paginate($this->MDepartment2s, [
            'order' => ['code'],
        ]);

        $this->set(compact('mDepartment2s'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 部店 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($id)
    {
        // $mDepartment2: 部店エンティティ
        $mDepartment2 = $this->MDepartment2s->find('detail', compact('id'))->firstOrFail();

        $this->set(compact('mDepartment2'));
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
     * @param string|null $id 部店 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($id = null)
    {
        // $mDepartment2: 部店エンティティ
        if ($id === null) {
            $mDepartment2 = $this->MDepartment2s->newEmptyEntity();
        } else {
            $mDepartment2 = $this->MDepartment2s->find('detail', compact('id'))->firstOrFail();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // エンティティ編集
            $mDepartment2 = $this->MDepartment2s->doEditEntity($mDepartment2, $this->getRequest()->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->MDepartment2s->save($mDepartment2)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $mDepartment2->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($mDepartment2);
        }

        // $mDepartment1s: 本部リスト
        $mDepartment1s = $this->MDepartment1s->find('list')->all();

        $this->set(compact('mDepartment2', 'mDepartment1s'));
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
        $result = $this->MDepartment2s->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $mDepartment2: 部店エンティティ
                $mDepartment2 = $this->MDepartment2s->find('detail', compact('id'))->firstOrFail();

                // 削除
                $mDepartment2 = $this->MDepartment2s->doDeleteEntity($mDepartment2, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->MDepartment2s->save($mDepartment2)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($mDepartment2);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
