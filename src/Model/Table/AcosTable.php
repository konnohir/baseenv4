<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;

/**
 * 権限詳細マスタ
 */
class AcosTable extends AppTable
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

        $this->setTable('acos');
        $this->setPrimaryKey('id');
        $this->belongsToMany('RoleDetails');
    }

    /**
     * モデルの親権限詳細一覧を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return \Cake\ORM\Query
     */
    protected function findThreadedActions(Query $query, array $option)
    {
        $query->find('threaded');
        $query->formatResults(function ($results) {
            return array_filter($results->first()->children ?? [], function ($row) {
                return !empty($row->children);
            });
        });

        return $query;
    }

}
