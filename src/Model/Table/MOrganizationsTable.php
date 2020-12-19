<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\MOrganization;
use Cake\Collection\Collection;
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
            ],
            'associated' => [
                'MDepartment1s' => [
                    'fields' => [
                        'code', 'name',
                    ]
                ]
            ],
            'validate' => 'edit1',
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
            ],
            'validate' => 'edit2',
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
            ],
            'validate' => 'edit3',
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

    /**
     * 編集バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationEdit1(Validator $validator): Validator
    {
        // デフォルトバリデーション適用
        $this->validationDefault($validator);

        $validator->requirePresence('m_department1_id', false);

        return $validator;
    }

    /**
     * 編集バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationEdit2(Validator $validator): Validator
    {
        // デフォルトバリデーション適用
        $this->validationDefault($validator);

        // 本部ID
        $validator->add('m_department1_id', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        return $validator;
    }

    /**
     * 編集バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationEdit3(Validator $validator): Validator
    {
        // デフォルトバリデーション適用
        $this->validationDefault($validator);

        // 本部ID
        $validator->add('m_department1_id', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        // 部店ID
        $validator->add('m_department2_id', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        return $validator;
    }

    /**
     * 検索条件
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

        // $query->formatResults(function ($results) {
        //     $results = $results->toArray();
        //     $prevMDepartment1Id = 0;
        //     $prevMDepartment2Id = 0;
        //     $index1 = 0;
        //     $index2 = 0;
        //     foreach($results as $index => $row) {
        //         if ($row->m_department2_id === $prevMDepartment2Id) {
        //             $results[$index2]->m_department2->rowspan++;
        //         }else {
        //             if (!isset($results[$index]->m_department2)) {
        //                 $results[$index]->m_department2 = new \stdClass();
        //             }
        //             $results[$index]->m_department2->rowspan = 1;
        //             $prevMDepartment2Id = $row->m_department2_id;
        //             $index2 = $index;
        //         }
        //         if ($row->m_department1_id === $prevMDepartment1Id) {
        //             $results[$index1]->m_department1->rowspan++;
        //         }else {
        //             $results[$index]->m_department1->rowspan = 1;
        //             $prevMDepartment1Id = $row->m_department1_id;
        //             $index1 = $index;
        //         }
        //     }

        //     return new Collection($results);
        // });

        return $query->where($conditions);
    }

}
