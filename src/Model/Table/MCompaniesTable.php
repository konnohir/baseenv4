<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * 企業マスタ
 */
class MCompaniesTable extends AppTable
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

        $this->setTable('m_companies');
        $this->setPrimaryKey('id');
        $this->belongsToMany('Tags');
        $this->hasMany('Notices');
    }

    /**
     * バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        // ID
        $validator
            ->naturalNumber('id')
            ->allowEmptyString('id', 'create');

        // コード
        $validator
            ->scalar('code')
            ->maxLength('code', 6)
            ->requirePresence('code', 'create')
            ->notEmptyString('code', __('E-V-REQUIRED', '企業コード'))
            ->naturalNumber('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('E-V-UNIQUE', 'コード')]);

        // 名称
        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name', __('E-V-REQUIRED', '名称'));

        // 電話番号
        $validator
            ->scalar('tel_no')
            ->maxLength('tel_no', 13)
            ->allowEmptyString('tel_no');

        // 創業年月日
        $validator
            ->date('established_date', ['ymd'], __('{0}は日時を入力してください。', '創業年月日'))
            ->allowEmptyString('established_date');

        // 従業員数
        $validator
            ->integer('staff', __('{0}は整数を入力してください。', '従業員数', 100))
            ->range('staff', [0, 100], __('{0}は{1}から{2}の値を入力してください。', '従業員数', 0, 100))
            ->allowEmptyString('staff');

        // 備考
        $validator
            ->scalar('note')
            ->maxLength('note', 255)
            ->allowEmptyString('note');

        // 作成日時
        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        // 更新日時
        $validator
            ->dateTime('updated_at')
            ->notEmptyDateTime('updated_at');

        // 削除日時
        $validator
            ->dateTime('deleted_at')
            ->allowEmptyDateTime('deleted_at');

        // アソシエーション：タグ
        $validator
            ->requirePresence('tags', 'create')
            ->isArray('tags')
            ->hasAtLeast('tags', 0, __('{0}を選択してください。', 'タグ'));

        return $validator;
    }

    /**
     * モデルの概要を取得する
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     * @return Entity
     */
    protected function findFilteredData(Query $query, array $option)
    {
        // $map: 検索マッピング設定 (array)
        $map = [
            'id' => ['type' => '=='],
            'code' => ['type' => 'like'],
            'name' => ['type' => 'like'],
            'established_date' => ['=='],
            'established_date_from' => ['type' => '>=', 'field' => 'established_date'],
            'established_date_to' => ['type' => '<=', 'field' => 'established_date'],
            'staff' => ['type' => 'range'],
            'staff2' => ['type' => '==', 'field' => 'staff'],
        ];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $option['filter'] ?? []);

        return $query->where($conditions);
    }

    /**
     * モデルの詳細を取得する
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     * @return Entity
     */
    protected function findDetail(Query $query, array $option)
    {
        if (isset($option['id'])) {
            $query->where([$this->getAlias() . '.id' => $option['id']]);
        }
        return $query->contain([
            'Tags',
            'Notices',
        ]);
    }

}
