<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\BadRequestException;

/**
 * 組織マスタ
 * 
 * @property \App\Model\Table\MOrganizationsTable $MOrganizations
 * @property \App\Model\Table\MDepartment1sTable $MDepartment1s
 * @property \App\Model\Table\MDepartment2sTable $MDepartment2s
 * @property \App\Model\Table\MDepartment3sTable $MDepartment3s
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

        $this->loadModel('MOrganizations');
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
        $tableRows = $this->paginate($this->MOrganizations, [
            'sortableFields' => [
                'MDepartment1s.name',
                'MDepartment1s.code',
                'MDepartment2s.name',
                'MDepartment2s.code',
                'MDepartment3s.name',
                'MDepartment3s.code',
            ],
            'order' => [
                'MDepartment1s.code' => 'asc',
                'MDepartment2s.code' => 'asc',
                'MDepartment3s.code' => 'asc',
            ],
        ]);

        $this->set(compact('tableRows'));
    }

    /**
     * 詳細画面
     *
     * @param string $mOrganizationId 組織id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($mOrganizationId)
    {
        // $mOrganization: 組織エンティティ
        $mOrganization = $this->MOrganizations->find('detail', ['id' => $mOrganizationId])->firstOrFail();

        $this->set(compact('mOrganization'));
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
     * @param string $ 組織id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($mOrganizationId = null)
    {
        if ($mOrganizationId !== null) {
            $mOrganization = $this->MOrganizations->find()
                ->contain('MDepartment1s')
                ->contain('MDepartment2s')
                ->contain('MDepartment3s')
                ->where([
                    'MOrganizations.id' => $mOrganizationId
                ])->firstOrFail();
        } else {
            $mOrganization = $this->MOrganizations->newEmptyEntity();
        }

        // $editType: 編集種別 (1: 本部、2: 部店、3: 課、null: なし)
        $editType = $this->request->getData('edit_type');
        if ($mOrganization->m_department3_id !== null) {
            $editType = '3';
        } else if ($mOrganization->m_department2_id !== null) {
            $editType = '2';
        } else if ($mOrganization->m_department1_id !== null) {
            $editType = '1';
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is('post')) {
            // エンティティ編集
            switch ($editType) {
                case '1':
                    $mOrganization = $this->MOrganizations->doEditDepartment1Entity($mOrganization, $this->getRequest()->getData());
                    break;
                case '2':
                    $mOrganization = $this->MOrganizations->doEditDepartment2Entity($mOrganization, $this->getRequest()->getData());
                    break;
                case '3':
                    $mOrganization = $this->MOrganizations->doEditDepartment3Entity($mOrganization, $this->getRequest()->getData());
                    break;
                default:
                    throw new BadRequestException();
            }
            // DB保存成功時: 詳細画面へ遷移
            if ($this->MOrganizations->save($mOrganization)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $mOrganization->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($mOrganization);
        }

        // $mDepartment1List: 本部リスト
        $mDepartment1List = $this->MDepartment1s
            ->find('activeRecord')
            ->find('list')
            ->all();

        // $mDepartment2List: 部店リスト
        $mDepartment2List = $this->MDepartment2s
            ->find('activeRecord')
            ->find('list')
            ->all();

        $this->set(compact('editType', 'mOrganization', 'mDepartment1List', 'mDepartment2List'));
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
        $result = $this->MOrganizations->getConnection()->transactional(function () use ($targets) {
            // 削除対象のIDの配列
            $targetIds = [];
            foreach(array_keys($targets) as $id) {
                $targetIds[$id] = $id;
            }

            foreach ($targets as $id => $requestData) {
                // $mOrganization: 組織エンティティ
                $mOrganization = $this->MOrganizations->get($id);

                // 本部／部店を削除した場合、子組織も削除する
                if ($mOrganization->m_department3_id === null) {
                    $relatedOrganizations = $this->MOrganizations->find();
                    if ($mOrganization->m_department1_id !== null) {
                        $relatedOrganizations->where(['m_department1_id' => $mOrganization->m_department1_id]);
                    }
                    if ($mOrganization->m_department2_id !== null) {
                        $relatedOrganizations->where(['m_department2_id' => $mOrganization->m_department2_id]);
                    }
                    foreach ($relatedOrganizations as $relatedOrganization) {
                        // 削除対象のデータなら後続処理で削除するため、次の対象データの処理へ進む
                        if (isset($targetIds[$relatedOrganization->id])) {
                            continue;
                        }

                        // 削除
                        $relatedOrganization = $this->MOrganizations->doDeleteEntity($relatedOrganization, ['_lock' => $relatedOrganization->_lock]);
        
                        // DB保存成功時: 次の対象データの処理へ進む
                        if ($this->MOrganizations->save($relatedOrganization)) {
                            continue;
                        }
        
                        // DB保存失敗時: ロールバック
                        return $this->failed($relatedOrganization);
                    }
                }

                // 削除
                $mOrganization = $this->MOrganizations->doDeleteEntity($mOrganization, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->MOrganizations->save($mOrganization)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($mOrganization);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
