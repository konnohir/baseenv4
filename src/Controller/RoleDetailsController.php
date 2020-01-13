<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * RoleDetails Controller
 * 権限詳細マスタ
 */
class RoleDetailsController extends AppCrudController
{
    public $title = '権限詳細';

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Permission');
        $this->loadModel('Roles');
        $this->loadModel('RoleDetails');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $roleDetails: 権限詳細マスタの配列
        try {
            $roleDetails = $this->paginate($this->RoleDetails);
        } catch (NotFoundException $e) {
            return $this->redirect($this->paginate['firstPage']);
        }
        $this->set(compact('roleDetails'));
    }

    /**
     * 詳細画面
     *
     * @param string|null $id 権限詳細マスタ id.
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        // $roleDetail: 権限詳細マスタ
        $roleDetail = $this->RoleDetails->get($id, [
            'finder' => 'detail',
        ]);

        // $acos: Access Control Objectリスト
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
        $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id 権限詳細マスタ id.
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $roleDetail: 権限詳細マスタ
        if ($this->isAdd()) {
            $roleDetail = $this->RoleDetails->newEmptyEntity();
        } else {
            $roleDetail = $this->RoleDetails->get($id, [
                'finder' => 'detail'
            ]);
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['patch', 'post', 'put'])) {
            $roleDetail = $this->RoleDetails->patchEntity($roleDetail, $this->getRequest()->getData(), [
                'fields' => [
                    'parent_id', 'name', 'description',
                    // associated
                    'acos',
                    // lock flag
                    '_lock',
                ],
                'associated' => [
                    'Acos' => [
                        'onlyIds' => true
                    ],
                ]
            ]);
            
            // $result: トランザクション実行結果 (boolean)
            $result = $this->RoleDetails->getConnection()->transactional(function () use ($roleDetail) {
                if (!$this->RoleDetails->save($roleDetail)) {
                    return false;
                }

                // @ACL
                foreach($roleDetail->roles ?? [] as $role) {
                    if (!$this->Permission->updateACL($role)) {
                        return false;
                    }
                }

                return true;
            });

            // DB保存成功時: 詳細画面へ遷移
            if ($result) {
                $this->Flash->success(__('{0}を保存しました。', __($this->title)));
                return $this->redirect(['action' => 'view', $roleDetail->id]);
            }

            // DB保存失敗時: 画面を再表示
            $errorMessage = __('入力内容に誤りがあります。');
            if ($roleDetail->getError('_lock')) {
                $errorMessage = current($roleDetail->getError('_lock'));
            }
            $this->Flash->error($errorMessage);
        }

        // $roleDetailList: 権限詳細リスト (parent_idがNULLの権限詳細のリスト)
        $roleDetailList = $this->RoleDetails->find('parentList', ['exclude' => $id])->toArray();

        // $Acos: Access Control Objectリスト (スレッド形式)
        $acos = $this->RoleDetails->Acos->find('threaded')->all();
        $acos = array_filter($acos->first()->children ?? [], function ($row) {
            // アクションが一つも無いコントローラーを除外する
            return !empty($row->children);
        });

        $this->set(compact('roleDetail', 'roleDetailList', 'acos'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $targets: 削除する権限詳細マスタの配列 [ID => 更新日付] (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクション実行結果 (boolean)
        $result = $this->RoleDetails->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $_lock) {
                // $roleDetail: 権限詳細マスタ
                $roleDetail = $this->RoleDetails->get($id, [
                    'fields' => [
                        'id',
                        'updated_at',
                        'deleted_at',
                    ],
                    'contain' => ['Roles'],
                ]);

                // 排他制御
                $roleDetail->_lock = $_lock;

                // 削除日付
                $roleDetail->deleted_at = date('Y-m-d h:i:s');

                // DB保存失敗時: ロールバック
                if (!$this->RoleDetails->save($roleDetail)) {
                    $errorMessage = __('入力内容に誤りがあります。');
                    if ($roleDetail->getError('_lock')) {
                        $errorMessage = current($roleDetail->getError('_lock'));
                    }
                    $this->Flash->error($errorMessage);
                    return false;
                }

                foreach($roleDetail->roles ?? [] as $role) {
                    if (!$this->Permission->updateACL($role)) {
                        return false;
                    }
                }

                // DB保存成功時: 次のデータの処理へ進む
            }

            $this->Flash->success(__('{0}を削除しました。', __($this->title)));
            return true;
        });

        // 画面を再表示
        return $this->redirect($this->referer());
    }
}
