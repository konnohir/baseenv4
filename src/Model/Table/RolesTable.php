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

        // @ACL
        $this->addBehavior('Acl.Acl', ['requester']);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        // ID
        $validator
            ->naturalNumber('id')
            ->allowEmptyString('id', null, 'create');

        // 名称
        $validator
            ->scalar('name')
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->notEmptyString('name', __('{0}は必須です。', '名称'));

        // 説明
        $validator
            ->scalar('description')
            ->maxLength('description', 45)
            ->allowEmptyString('description');

        // 作成日付
        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        // 更新日付
        $validator
            ->dateTime('updated_at')
            ->notEmptyDateTime('updated_at');

        // 削除日付
        $validator
            ->dateTime('deleted_at')
            ->allowEmptyDateTime('deleted_at');

        return $validator;
    }

    /**
     * モデルの概要を取得する
     */
    public function findOverview(Query $query, array $options)
    {
        // $map: 検索マッピング設定 (array)
        $map = [];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $options['filter'] ?? []);

        return $query->where($conditions);
    }

    /**
     * モデルの詳細を取得する
     */
    public function findDetail(Query $query, array $options)
    {
        if (isset($options['id'])) {
            $query->where([$this->getAlias() . '.id' => $options['id']]);
        }
        return $query->contain(['RoleDetails', 'RoleDetails.Acos']);
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
                // lock flag
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
            // 削除日付
            'deleted_at' => new FrozenTime(),
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'deleted_at',
                // lock flag
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }
}
