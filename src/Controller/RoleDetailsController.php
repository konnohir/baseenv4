<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;

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

        $this->set(compact('roleDetail'));
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
                    // lock flag
                    '_lock,'
                ],
                'associated' => [
                ]
            ]);

            $roleDetail->updated_at = new FrozenTime();
            
            // DB保存成功時: 詳細画面へ遷移
            if ($this->RoleDetails->save($roleDetail)) {
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

        // $roleDetailList: 権限詳細リスト (parent_idがNULLの権限詳細のみ)
        $roleDetailList = $this->RoleDetails->find('parentList', ['exclude' => $id])->toArray();

        $this->set(compact('roleDetail', 'roleDetailList'));
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

        // $result: トランザクションの結果 (boolean)
        $result = $this->RoleDetails->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $_lock) {
                $roleDetail = $this->RoleDetails->get($id, [
                    'fields' => [
                        'id',
                        'updated_at',
                        'deleted_at',
                    ]
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

                // DB保存成功時: 次のデータの処理へ進む
            }

            $this->Flash->success(__('{0}を削除しました。', __($this->title)));
            return true;
        });

        // 画面を再表示
        return $this->redirect($this->referer());
    }
}