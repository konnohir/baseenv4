<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use ArrayObject;

/**
 * RoleDetails Model
 * 権限詳細マスタ
 */
class RoleDetailsTable extends AppTable
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

        $this->setTable('role_details');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Acl.Acos', [
            'joinTable' => 'role_details_acos'
        ]);
        $this->belongsToMany('Roles');
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

        return $query->find('threaded')->where($conditions);
    }

    /**
     * モデルの詳細を取得する
     */
    public function findDetail(Query $query, array $options)
    {
        if (isset($options['id'])) {
            $query->where([$this->getAlias() . '.id' => $options['id']]);
        }
        return $query->contain(['Roles', 'Acos']);
    }

    /**
     * モデルの親権限一覧を取得する
     */
    public function findParentList(Query $query, array $options)
    {
        // $exclude_id: リストから除外する権限詳細のID
        $exclude_id = $options['exclude'] ?? null;

        $query->find('list')->where(['parent_id IS' => null]);
        if (isset($exclude_id)) {
            $query->where(['id NOT IN' => $exclude_id]);
        }

        return $query;
    }

}
