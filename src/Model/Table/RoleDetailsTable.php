<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\RoleDetail;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * 権限詳細マスタ
 */
class RoleDetailsTable extends AppTable
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
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        parent::validationDefault($validator);

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
     * モデルの概要を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
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
     * モデルの詳細を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
     */
    protected function findDetail(Query $query, array $option)
    {
        if (isset($option['id'])) {
            $query->where([$this->getAlias() . '.id' => $option['id']]);
        }
        return $query->contain(['Roles', 'Acos', 'ParentRoleDetails']);
    }

    /**
     * モデルの親権限詳細一覧を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
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
     * @param \App\Model\Entity\RoleDetail $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\RoleDetail|false
     */
    public function doEditEntity(RoleDetail $entity, array $input = [])
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
        return $this->save($entity);
    }

    /**
     * 削除
     * 
     * @param \App\Model\Entity\RoleDetail $entity エンティティ
     * @param array $input ユーザー入力
     * @return \App\Model\Entity\RoleDetail|false
     */
    public function doDeleteEntity(RoleDetail $entity, array $input = [])
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
        return $this->save($entity);
    }
}
