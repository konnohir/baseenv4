<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\RoleDetail;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
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
            ],
            'validate' => 'edit',
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
        ]);
        return $this->save($entity);
    }

    /**
     * 編集バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationEdit(Validator $validator): Validator
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
    protected function findFilteredData(Query $query, array $option)
    {
        // $map: 検索マッピング設定 (array)
        $map = [];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $option['filter'] ?? []);

        return $query->find('threaded')->where($conditions);
    }

    /**
     * 親権限詳細の一覧を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return \Cake\ORM\Query
     */
    protected function findParentList(Query $query, array $option)
    {
        // $exclude_id: リストから除外する権限詳細のID
        $excludeId = $option['excludeId'] ?? null;

        $query->find('list')->where(['parent_id IS' => null]);
        if (isset($excludeId)) {
            $query->where(['id NOT IN' => $excludeId]);
        }

        return $query;
    }
}
