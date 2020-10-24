<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Role;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * 権限マスタ
 */
class RolesTable extends AppTable
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

        $this->setTable('roles');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');
        $this->belongsToMany('RoleDetails');
        $this->hasMany('Users');
    }

    /**
     * エンティティ編集
     * 
     * @param \App\Model\Entity\Role $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\Role|false
     */
    public function doEditEntity(Role $entity, array $input = [])
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'name',
                'description',
                // associated
                'role_details',
                // lock token
                '_lock',
            ],
            'associated' => [
                'RoleDetails' => [
                    'onlyIds' => true
                ],
            ],
            'validate' => 'edit',
        ]);
        return $this->save($entity);
    }

    /**
     * 削除
     * 
     * @param \App\Model\Entity\Role $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\Role|false
     */
    public function doDeleteEntity(Role $entity, array $input = [])
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
     * 編集時バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    protected function validationEdit(Validator $validator): Validator
    {
        // デフォルトバリデーション適用
        $this->validationDefault($validator);

        // 名称
        $validator->add('name', [
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
    protected function findFilteredData(Query $query, array $option): Query
    {
        // $map: 検索マッピング設定 (array)
        $map = [];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $option['filter'] ?? []);

        return $query->where($conditions);
    }
}
