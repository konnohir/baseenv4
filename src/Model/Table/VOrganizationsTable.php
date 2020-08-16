<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;

/**
 * VOrganizations Model
 * 組織ビュー
 */
class VOrganizationsTable extends AppTable
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

        $this->setTable('v_organizations');
        // $this->setDisplayField(['department_name1', 'department_name2', 'department_name3']);
        // $this->setPrimaryKey('id');

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
        if (isset($option['mDepartment1Id'])) {
            $query->where([$this->getAlias() . '.m_department1_id IS' => $option['mDepartment1Id']]);
            $query->where([$this->getAlias() . '.m_department2_id IS' => $option['mDepartment2Id'] ?? null]);
            $query->where([$this->getAlias() . '.m_department3_id IS' => $option['mDepartment3Id'] ?? null]);
        }
        return $query
            ->select($this);
    }

    /**
     * 有効なエンティティのみ取得するFinder
     * 
     * select時に自動的にこのFinderを使用する.
     * 有効でないエンティティを取得したい場合はfind('withInactive')を使用する
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findActive(Query $query, array $options)
    {
        return $query;
    }
}
