<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * MDepartment2s Model
 * 部門（階層2）マスタ
 */
class MDepartment2sTable extends AppTable
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

        $this->setTable('m_department2s');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('MDepartment1s');
        $this->hasMany('MDepartment3s');
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

        // 本部コード
        $validator->add('code', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        // 本部名
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

        return $query->where($conditions);
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
        return $query
            ->contain(['MDepartment3s']);
    }

    /**
     * エンティティ編集
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     * @return Entity
     */
    public function doEditEntity(Entity $entity, array $input = [])
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'm_department1_id', 'code', 'name',
                // lock token
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }
}
