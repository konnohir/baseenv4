<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Organizations Model
 * 組織マスタ
 */
class OrganizationsTable extends AppTable
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

        $this->setTable('department_level3s');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('DepartmentLevel3s', [
            'conditions' => 'DepartmentLevel3s.id = Organizations.id',
            'foreignKey' => false,
        ]);
        $this->belongsTo('DepartmentLevel2s', [
            'conditions' => 'DepartmentLevel2s.id = DepartmentLevel3s.department_level2_id',
            'foreignKey' => false,
        ]);
        $this->belongsTo('DepartmentLevel1s', [
            'conditions' => 'DepartmentLevel1s.id = DepartmentLevel2s.department_level1_id',
            'foreignKey' => false,
        ]);
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

        return $query
            ->select(['id'])
            ->select($this->DepartmentLevel1s)
            ->select($this->DepartmentLevel2s)
            ->select($this->DepartmentLevel3s)
            ->contain(['DepartmentLevel3s'])
            ->contain(['DepartmentLevel2s'])
            ->contain(['DepartmentLevel1s'])
            ->order('DepartmentLevel1s.code')
            ->order('DepartmentLevel2s.code')
            ->order('DepartmentLevel3s.code')
            ->where($conditions);
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
        return $query;
    }
}
