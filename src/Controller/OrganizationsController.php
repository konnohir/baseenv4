<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;

/**
 * OrganizationsController Controller
 * 組織マスタ
 * 
 * @property \App\Model\Table\MDepartment1sTable $MDepartment1s
 * @property \App\Model\Table\MDepartment2sTable $MDepartment1s
 * @property \App\Model\Table\MDepartment3sTable $MDepartment1s
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
            'view' => ['requestId', 'organizationId'],
            'edit' => ['requestId', 'organizationId'],
            'delete' => ['requestTarget'],
        ]);

        $this->loadModel('VOrganizations');
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
        // $tableRows: 組織一覧
        $tableRows = $this->paginate($this->VOrganizations, [
            'order' => [],
        ]);

        $this->set(compact('tableRows'));
    }

    /**
     * 詳細画面
     *
     * @param string $mDepartment1Id 本部 id.
     * @param string $mDepartment2Id 部店 id.
     * @param string $mDepartment3Id 課 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($mDepartment1Id, $mDepartment2Id = null, $mDepartment3Id = null)
    {
        // $vOrganization: 組織エンティティ
        $vOrganization = $this->VOrganizations->find('detail', compact('mDepartment1Id', 'mDepartment2Id', 'mDepartment3Id'))->firstOrFail();

        $this->set(compact('vOrganization'));
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
     * @param string $mDepartment1Id 本部 id.
     * @param string $mDepartment2Id 部店 id.
     * @param string $mDepartment3Id 課 id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($mDepartment1Id = null, $mDepartment2Id = null, $mDepartment3Id = null)
    {
        // $editType: 編集種別 (1: 本部、2: 部店、3: 課、null: なし)
        $editType = $this->request->getData('edit_type');
        if ($mDepartment3Id !== null) {
            $editType = '3';
        } elseif ($mDepartment2Id !== null) {
            $editType = '2';
        } elseif ($mDepartment1Id !== null) {
            $editType = '1';
        }

        // $mDepartment1: 本部エンティティ
        if ($editType === '1' && $mDepartment1Id !== null) {
            $mDepartment1 = $this->MDepartment1s->find('detail', ['id' => $mDepartment1Id])->firstOrFail();
        } else {
            $mDepartment1 = $this->MDepartment1s->newEmptyEntity();
        }

        // $mDepartment2: 部店エンティティ
        if ($editType === '2' && $mDepartment2Id !== null) {
            $mDepartment2 = $this->MDepartment2s->find('detail', ['id' => $mDepartment2Id])->firstOrFail();
            if ($mDepartment2->m_department1_id !== (int)$mDepartment1Id) {
                throw new RecordNotFoundException();
            }
        } else {
            $mDepartment2 = $this->MDepartment2s->newEmptyEntity();
        }

        // $mDepartment3: 課エンティティ
        if ($editType === '3' && $mDepartment3Id !== null) {
            $mDepartment3 = $this->MDepartment3s->find('detail', ['id' => $mDepartment3Id])->firstOrFail();
            if ($mDepartment3->m_department2_id !== (int)$mDepartment2Id) {
                throw new RecordNotFoundException();
            }
        } else {
            $mDepartment3 = $this->MDepartment3s->newEmptyEntity();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // エンティティ編集
            switch ($editType) {
                case '1':
                    $mDepartment1 = $this->MDepartment1s->doEditEntity($mDepartment1, $this->getRequest()->getData());

                    // DB保存成功時: 詳細画面へ遷移
                    if ($this->MDepartment1s->save($mDepartment1)) {
                        $this->Flash->success(__('I-SAVE', __($this->title)));
                        return $this->redirect(['action' => 'view', $mDepartment1->id]);
                    }

                    // DB保存失敗時: 画面を再表示
                    $this->failed($mDepartment1);

                    break;
                case '2':
                    $mDepartment2 = $this->MDepartment2s->doEditEntity($mDepartment2, $this->getRequest()->getData());

                    // DB保存成功時: 詳細画面へ遷移
                    if ($this->MDepartment2s->save($mDepartment2)) {
                        $this->Flash->success(__('I-SAVE', __($this->title)));
                        return $this->redirect(['action' => 'view', $mDepartment2->m_department1_id, $mDepartment2->id]);
                    }

                    // DB保存失敗時: 画面を再表示
                    $this->failed($mDepartment2);

                    break;
                case '3':
                    $mDepartment3 = $this->MDepartment3s->doEditEntity($mDepartment3, $this->getRequest()->getData());
                    dbg($mDepartment3);

                    // DB保存成功時: 詳細画面へ遷移
                    if ($this->MDepartment3s->save($mDepartment3)) {
                        $this->Flash->success(__('I-SAVE', __($this->title)));
                        return $this->redirect([
                            'action' => 'view',
                            $mDepartment3->m_department2->m_department1_id ?? $this->request->getData('MDepartment3s.m_department1_id'),
                            $mDepartment3->m_department2_id, $mDepartment3->id
                        ]);
                    }

                    // DB保存失敗時: 画面を再表示
                    $this->failed($mDepartment3);

                    break;
                default:
                    throw new BadRequestException();
            }
        }

        // $mDepartment1List: 本部リスト
        $mDepartment1List = $this->MDepartment1s->find('list')->all();

        // $mDepartment2List: 部店リスト
        $mDepartment2List = $this->MDepartment2s->find('list')->all();

        $this->set(compact('editType', 'mDepartment1', 'mDepartment2', 'mDepartment3', 'mDepartment1List', 'mDepartment2List'));
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

        foreach ($targets as $id => $requestData) {
        }

        // $result: トランザクションの結果 (boolean)
        $result = $this->MDepartment1s->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $deleteType: 削除種別(1: 本部, 2: 部店, 3: 課)
                // $mDepartment1Id: 本部ID
                // $mDepartment2Id: 部店ID
                // $mDepartment3Id: 課ID
                $explodeId = explode('/', (string)$id);
                $deleteType = count($explodeId);

                switch ($deleteType) {
                    case '1':
                        list($mDepartment1Id) = $explodeId;

                        // $mDepartment1: 本部エンティティ
                        $mDepartment1 = $this->MDepartment1s->find('detail', ['id' => $mDepartment1Id])->firstOrFail();

                        // 削除
                        $mDepartment1 = $this->MDepartment1s->doDeleteEntity($mDepartment1, $requestData);

                        // DB保存成功時: 次の対象データの処理へ進む
                        if ($this->MDepartment1s->save($mDepartment1)) {
                            break;
                        }

                        // DB保存失敗時: ロールバック
                        return $this->failed($mDepartment1);
                    case '2':
                        list(, $mDepartment2Id) = $explodeId;

                        // $mDepartment2: 部店エンティティ
                        $mDepartment2 = $this->MDepartment2s->find('detail', ['id' => $mDepartment2Id])->firstOrFail();

                        // 削除
                        $mDepartment2 = $this->MDepartment2s->doDeleteEntity($mDepartment2, $requestData);

                        // DB保存成功時: 次の対象データの処理へ進む
                        if ($this->MDepartment2s->save($mDepartment2)) {
                            break;
                        }

                        // DB保存失敗時: ロールバック
                        return $this->failed($mDepartment2);
                    case '3':
                        list(,, $mDepartment3Id) = $explodeId;

                        // $mDepartment3: 課エンティティ
                        $mDepartment3 = $this->MDepartment3s->find('detail', ['id' => $mDepartment3Id])->firstOrFail();

                        // 削除
                        $mDepartment3 = $this->MDepartment3s->doDeleteEntity($mDepartment3, $requestData);

                        // DB保存成功時: 次の対象データの処理へ進む
                        if ($this->MDepartment3s->save($mDepartment3)) {
                            break;
                        }

                        // DB保存失敗時: ロールバック
                        return $this->failed($mDepartment3);
                    default:
                        throw new BadRequestException();
                }
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
