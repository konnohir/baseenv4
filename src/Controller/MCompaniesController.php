<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * 企業マスタ
 * 
 * @property \App\Model\Table\MCompaniesTable $MCompanies
 */
class MCompaniesController extends AppController
{
    public $title = '企業';

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
            'addStaff' => ['requestTarget'],
        ]);

        $this->loadModel('MCompanies');
    }

    /**
     * 一覧画面
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // $mCompanies: 企業マスタの配列
        $mCompanies = $this->paginate($this->MCompanies);

        $this->set(compact('mCompanies'));
    }

    /**
     * 詳細画面
     *
     * @param string|null $id 企業マスタ id.
     * @return \Cake\Http\Response|null
     */
    public function view($id = null)
    {
        // $mCompany: 企業マスタ
        $mCompany = $this->MCompanies->find('detail', compact('id'))->firstOrFail();
        // $tagList: タグ一覧
        $tagList = $this->MCompanies->Tags->find('list')->toArray();

        $this->set(compact('mCompany', 'tagList'));
    }

    /**
     * 新規登録画面
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        return $this->edit();
    }

    /**
     * 編集画面
     *
     * @param string|null $id 企業マスタ id.
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        // $mCompany: 企業マスタ
        if ($id === null) {
            $mCompany = $this->MCompanies->newEmptyEntity();
        } else {
            $mCompany = $this->MCompanies->find('detail', compact('id'))->firstOrFail();
        }

        // POST送信された(保存ボタンが押された)場合
        if ($this->request->is(['post', 'put', 'patch'])) {
            $mCompany = $this->MCompanies->patchEntity($mCompany, $this->getRequest()->getData(), [
                'fields' => [
                    'code', 'name', 'tel_no', 'staff', 'established_date', 'note',
                    // associated
                    'tags',
                    'notices',
                    // lock token
                    '_lock',
                ],
                'associated' => [
                    'Tags' => [
                        'onlyIds' => true
                    ],
                    'Notices' => [
                        'fields' => [
                            'message',
                            'category_id',
                        ]
                    ]
                ],
            ]);

            // DB保存成功時: 詳細画面へ遷移
            if ($this->MCompanies->save($mCompany)) {
                $this->Flash->success(__('I-SAVE', __($this->title)));
                return $this->redirect(['action' => 'view', $mCompany->id]);
            }

            // DB保存失敗時: 画面を再表示
            $this->failed($mCompany);
        }

        // $tagList: タグ一覧
        $tagList = $this->MCompanies->Tags->find('list')->toArray();

        $this->set(compact('mCompany', 'tagList'));
    }

    /**
     * 増員API
     *
     * @return \Cake\Http\Response|null
     */
    public function addStaff()
    {
        // $targets: 対象データの配列 (array)
        $targets = $this->getRequest()->getData('targets');

        // $result: トランザクション実行結果 (boolean)
        $result = $this->MCompanies->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                $mCompany = $this->MCompanies->get($id, [
                    'fields' => [
                        'id',
                        'staff',
                        'updated_at',
                    ]
                ]);

                $inputArray = [
                    // 従業員数
                    'staff' => $mCompany->staff + 1,
                    // 排他制御
                    '_lock' => $requestData['_lock'],
                ];

                $mCompany = $this->MCompanies->patchEntity($mCompany, $inputArray, [
                    'fields' => [
                        'staff',
                        '_lock',
                    ],
                ]);

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->MCompanies->save($mCompany)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($mCompany, true);
            }

            $this->Flash->success(__('該当する{0}を増員しました。', __($this->title)));
            return true;
        });

        // 画面を再表示
        $this->set(compact('result'));
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
        $result = $this->MCompanies->getConnection()->transactional(function () use ($targets) {
            foreach ($targets as $id => $requestData) {
                $mCompany = $this->MCompanies->get($id, [
                    'fields' => [
                        'id',
                        'updated_at',
                        'deleted_at',
                    ]
                ]);

                // 排他制御
                $mCompany->_lock = $requestData['_lock'];

                // 削除日時
                $mCompany->deleted_at = date('Y-m-d h:i:s');

                // DB保存成功時: 次の対象データの処理へ進む
                if ($this->MCompanies->save($mCompany)) {
                    continue;
                }

                // DB保存失敗時: ロールバック
                return $this->failed($mCompany);
            }

            $this->Flash->success(__('I-DELETE', __($this->title)));
            return true;
        });

        $this->set(compact('result'));
    }
}
