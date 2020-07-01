<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

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

        $this->belongsToMany('Roles');
        $this->belongsToMany('Acos', [
            'joinTable' => 'role_details_acos'
        ]);
        $this->belongsTo('ParentRoleDetails', [
            'className' => 'RoleDetails',
            'foreignKey' => 'parent_id',
        ]);
    }

    /**
     * バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        parent::validationDefault($validator);

        // 新規作成時の必須入力項目
        $validator->requirePresence([
            'name',
            'description',
        ], 'create');

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
     * モデルの概要を取得するFinder
     */
    protected function findOverview(Query $query, array $option)
    {

        // $map: 検索マッピング設定 (array)
        $map = [];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $option['filter'] ?? []);

        return $query->find('threaded')->where($conditions);
    }

    /**
     * モデルの詳細を取得するFinder
     */
    protected function findDetail(Query $query, array $option)
    {
        if (isset($option['id'])) {
            $query->where([$this->getAlias() . '.id' => $option['id']]);
        }
        return $query->contain(['Roles', 'Acos', 'ParentRoleDetails']);
    }

    /**
     * モデルの親権限詳細一覧を取得するFinder
     */
    protected function findParentList(Query $query, array $option)
    {
        // $exclude_id: リストから除外する権限詳細のID
        $exclude_id = $option['exclude'] ?? null;

        $query->find('list')->where(['parent_id IS' => null]);
        if (isset($exclude_id)) {
            $query->where(['id NOT IN' => $exclude_id]);
        }

        return $query;
    }

    /**
     * エンティティ編集
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doEditEntity(Entity $entity, array $input = [])
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'parent_id', 'name', 'description',
                // associated
                'acos',
                // lock token
                '_lock',
            ],
            'associated' => [
                'Acos' => [
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
    public function doDeleteEntity(Entity $entity, array $input = [])
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
