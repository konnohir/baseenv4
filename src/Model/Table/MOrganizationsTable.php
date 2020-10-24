<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\MOrganization;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * 組織マスタ
 */
class MOrganizationsTable extends AppTable
{
    /**
     * 初期化
     *
     * @param array $config 設定値
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('m_organizations');
        $this->setPrimaryKey('id');
        $this->belongsTo('MDepartment1s');
        $this->belongsTo('MDepartment2s');
        $this->belongsTo('MDepartment3s');
    }

    /**
     * バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        parent::validationDefault($validator);

        $validator->requirePresence('m_department1_id', false);

        return $validator;
    }

    /**
     * モデルの概要を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return \Cake\ORM\Query
     */
    protected function findFilteredData(Query $query, array $option)
    {
        // $map: 検索マッピング設定 (array)
        $map = [];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $option['filter'] ?? []);

        return $query
            ->contain('MDepartment1s')
            ->contain('MDepartment2s')
            ->contain('MDepartment3s')
            ->where($conditions);
    }

    /**
     * モデルの詳細を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return \Cake\ORM\Query
     */
    protected function findDetail(Query $query, array $option)
    {
        if (isset($option['id'])) {
            $query->where([$this->getAlias() . '.id' => $option['id']]);
        }
        return $query
            ->contain('MDepartment1s')
            ->contain('MDepartment2s')
            ->contain('MDepartment3s');
    }

    /**
     * エンティティ編集 (部門階層1: 本部)
     * 
     * @param \App\Model\Entity\MOrganization $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\MOrganization
     */
    public function doEditDepartment1Entity(MOrganization $entity, array $input = [])
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // lock token
                '_lock',
                // association
                'm_department1',
                'm_department1s',
                'MDepartment1s',
            ],
            'associated' => [
                'MDepartment1s' => [
                    'fields' => [
                        'code', 'name',
                    ]
                ]
            ]
        ]);
        return $this->save($entity);
    }

    /**
     * エンティティ編集 (部門階層2: 部店)
     * 
     * @param \App\Model\Entity\MOrganization $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\MOrganization
     */
    public function doEditDepartment2Entity(MOrganization $entity, array $input = [])
    {
        // バリデーション用add
        $input['m_department2']['m_department1_id']
            = $entity->m_department1_id ?? $input['MOrganizations']['m_department1_id'] ?? null;

        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'm_department1_id',
                // lock token
                '_lock',
                // association
                'm_department2',
            ],
            'associated' => [
                'MDepartment2s' => [
                    'fields' => [
                        'code', 'name',
                    ],
                ]
            ]
        ]);
        return $this->save($entity);
    }

    /**
     * エンティティ編集 (部門階層3: 課)
     * 
     * @param \App\Model\Entity\MOrganization $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\MOrganization
     */
    public function doEditDepartment3Entity(MOrganization $entity, array $input = [])
    {
        // バリデーション用add
        $input['m_department3']['m_department2_id']
            = $entity->m_department2_id ?? $input['MOrganizations']['m_department2_id'] ?? null;

        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'm_department1_id',
                'm_department2_id',
                // lock token
                '_lock',
                // association
                'm_department3',
            ],
            'associated' => [
                'MDepartment3s' => [
                    'fields' => [
                        'code', 'name',
                    ]
                ]
            ]
        ]);
        return $this->save($entity);
    }

    /**
     * 削除
     * 
     * @param \App\Model\Entity\MOrganization $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\MOrganization
     */
    public function doDeleteEntity(MOrganization $entity, array $input = [])
    {
        $input = array_merge_recursive($input, [
            // 削除日時
            'deleted_at' => new FrozenTime(),
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'deleted_at',
                // lock token
                '_lock',
            ],
        ]);
        return $this->save($entity);
    }
}
