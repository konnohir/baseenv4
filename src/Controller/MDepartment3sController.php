<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * MDepartment3s Controller
 * 課マスタ
 * 
 * @property \App\Model\Table\Department2sTable $Department2s
 */
class MDepartment3sController extends AppController
{
    public $title = '課';

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
        $this->loadModel('MDepartment3s');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $mDepartment3s: 課一覧
        $mDepartment3s = $this->paginate($this->MDepartment3s, [
            'order' => ['MDepartment3s.code'],
        ]);

        $this->set(compact('mDepartment3s'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 課 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($id)
    {
        // $mDepartment3: 課エンティティ
        $mDepartment3 = $this->MDepartment3s->find('detail', compact('id'))->firstOrFail();

        $this->set(compact('mDepartment3'));
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
     * @param string|null $id 課 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($id = null)
    {
        // $mDepartment3: 課エンティティ
        if ($id === null) {
            $mDepartment3 = $this->MDepartment3s->newEmptyEntity();
        } else {
            $mDepartment3 = $this->MDepartment3s->find('detail', compact('id'))->firstOrFail();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // エンティティ編集
            $mDepartment3 = $this->MDepartment3s->doEditEntity($mDepartment3, $this->getRequest()->getData());

            // DB保存成功時: 詳細画面へ遷移
            if ($this->MDepartment3s->save($mDepartment3)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $mDepartment3->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($mDepartment3);
        }

        // $mDepartment1s: 本部リスト
        $mDepartment1s = $this->MDepartment1s->find('list')->all();

        // $mDepartment1s: 部店リスト
        $mDepartment2s = $this->MDepartment2s->find('list')->all();

        $this->set(compact('mDepartment3', 'mDepartment1s', 'mDepartment2s'));
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
        $result = $this->MDepartment3s->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $mDepartment3: 課エンティティ
                $mDepartment3 = $this->MDepartment3s->find('detail', compact('id'))->firstOrFail();

                // 削除
                $mDepartment3 = $this->MDepartment3s->doDeleteEntity($mDepartment3, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->MDepartment3s->save($mDepartment3)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($mDepartment3);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
