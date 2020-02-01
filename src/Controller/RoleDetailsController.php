<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * RoleDetails Controller
 * 権限詳細マスタ
 * 
 * @property \App\Model\Table\RoleDetailsTable $RoleDetails
 */
class RoleDetailsController extends AppController
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

        // リクエストフィルタ
        $this->loadComponent('RequestFilter', [
            'index' => ['paginate'],
            'view' => ['requestId'],
            'edit' => ['requestId'],
            'delete' => ['requestTarget'],
        ]);

        $this->loadModel('RoleDetails');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $roleDetails: 権限詳細一覧
        $roleDetails = $this->paginate($this->RoleDetails);

        $this->set(compact('roleDetails'));
    }

    /**
     * 詳細画面
     *
     * @param string $id 権限詳細 id.
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // $roleDetail: 権限詳細エンティティ
        $roleDetail = $this->RoleDetails->find('detail', compact('id'))->first();

        // データ取得失敗時: 一覧画面へ遷移 (検索条件クリア)
        if ($roleDetail === null) {
            // E-V-NOT-FOUND:対象の{0}が存在しません
            $this->Flash->error(__('E-NOT-FOUND', __($this->title)), ['clear' => true]);
            return $this->redirect(['action' => 'index']);
        }

        // $acos: コントローラー／アクション(Access Control Object)のリスト (スレッド形式)
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
     * @param string|null $id 権限詳細 id.
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $roleDetail: 権限詳細エンティティ
        if ($this->isAdd()) {
            $roleDetail = $this->RoleDetails->newEmptyEntity();
        } else {
            $roleDetail = $this->RoleDetails->find('detail', compact('id'))->first();
        }

        // データ取得失敗時: 一覧画面へ遷移 (検索条件クリア)
        if ($roleDetail === null) {
            // E-V-NOT-FOUND:対象の{0}が存在しません
            $this->Flash->error(__('E-NOT-FOUND', __($this->title)), ['clear' => true]);
            return $this->redirect(['action' => 'index']);
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['post', 'put', 'patch'])) {
            // エンティティ編集
            $roleDetail = $this->RoleDetails->doEditEntity($roleDetail, $this->getRequest()->getData());

            // $result: トランザクション実行結果 (boolean)
            $result = $this->RoleDetails->getConnection()->transactional(function () use ($roleDetail) {
                if (!$this->RoleDetails->save($roleDetail)) {
                    return false;
                }

                // @ACL
                $this->loadComponent('Permission');
                foreach ($roleDetail->roles ?? [] as $role) {
                    if (!$this->Permission->updateACL($role)) {
                        return false;
                    }
                }

                return true;
            });

            // DB保存成功時: 詳細画面へ遷移
            if ($result) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $roleDetail->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($roleDetail);
        }

        // $parentRoleDetailList: 親権限詳細リスト
        $parentRoleDetailList = $this->RoleDetails->find('parentList', ['exclude' => $id])->toArray();

        // $acos: コントローラー／アクション(Access Control Object)のリスト (スレッド形式)
        $acos = $this->RoleDetails->Acos->find('threaded')->all();
        $acos = array_filter($acos->first()->children ?? [], function ($row) {
            return !empty($row->children);
        });

        $this->set(compact('roleDetail', 'parentRoleDetailList', 'acos'));
    }

    /**
     * 削除API
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // $targets: 対象データの配列 (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクション実行結果 (boolean)
        $result = $this->RoleDetails->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                // $roleDetail: 権限詳細エンティティ
                $roleDetail = $this->RoleDetails->find('detail', compact('id'))->first();

                // データ取得失敗時: ロールバック
                if ($roleDetail === null) {
                    return $this->failed($roleDetail);
                }

                // 削除
                $roleDetail = $this->RoleDetails->doDeleteEntity($roleDetail, $requestData);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->RoleDetails->save($roleDetail)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($roleDetail);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        // 画面を再表示、または一覧画面へ遷移 (検索条件クリア)
        $redirectUrl = $this->referer();
        if (strpos($redirectUrl, 'view') !== false) {
            $redirectUrl = ['action' => 'index'];
        }
        return $this->redirect($redirectUrl);
    }
}
