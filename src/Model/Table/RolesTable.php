<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Roles Model
 * 権限マスタ
 */
class RolesTable extends AppTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('roles');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsToMany('RoleDetails');
        $this->hasMany('Users');
    }

    /**
     * バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        // 新規作成時の必須入力項目
        $validator->requirePresence([
            'name',
        ], 'create');

        // ID
        $validator->naturalNumber('id');

        // 名称
        $column = 'name';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            // 文字列
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            // 最大桁数以内
            'maxLength' => [
                'rule' =>  ['maxLength', 45],
                'message' => __('E-V-MAXLENGTH', $label, 45),
                'last' => true,
            ],
        ]);

        // 説明
        $column = 'description';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            // 文字列
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            // 最大桁数以内
            'maxLength' => [
                'rule' =>  ['maxLength', 45],
                'message' => __('E-V-MAXLENGTH', $label, 45),
                'last' => true,
            ],
        ]);

        // 作成日時
        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        // 更新日時
        $validator
            ->dateTime('updated_at')
            ->notEmptyDateTime('updated_at');

        // 削除日時
        $validator
            ->dateTime('deleted_at')
            ->allowEmptyDateTime('deleted_at');

        return $validator;
    }

    /**
     * モデルの概要を取得するFinder
     */
    public function findOverview(Query $query, array $options)
    {
        // $map: 検索マッピング設定 (array)
        $map = $this->getFilterSettings();

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $options['filter'] ?? []);

        return $query->where($conditions);
    }

    /**
     * モデルの詳細を取得するFinder
     */
    public function findDetail(Query $query, array $options)
    {
        if (isset($options['id'])) {
            $query->where([$this->getAlias() . '.id' => $options['id']]);
        }
        return $query->contain(['RoleDetails', 'RoleDetails.Acos']);
    }

    /**
     * 検索マッピング設定
     * 
     * @return array
     */
    public function getFilterSettings()
    {
        return [];
    }

    /**
     * エンティティ編集
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doEditEntity(Entity $entity, array $input)
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'name', 'description',
                // associated
                'role_details',
                // lock token
                '_lock',
            ],
            'associated' => [
                'RoleDetails' => [
                    'onlyIds' => true
                ],
            ]
        ]);
        return $entity;
    }

    /**
     * 削除
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doDeleteEntity(Entity $entity, array $input)
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
            'associated' => []
        ]);
        return $entity;
    }
}
