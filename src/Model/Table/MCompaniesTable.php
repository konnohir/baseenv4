<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * MCompanies Model
 * 企業マスタ
 */
class MCompaniesTable extends AppTable
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

        $this->setTable('m_companies');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Tags');
        $this->hasMany('Notices');
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
            ->allowEmptyString('id', 'create');

        // コード
        $validator
            ->scalar('code')
            ->maxLength('code', 6)
            ->requirePresence('code', 'create')
            ->notEmptyString('code', __('{0}は必須です。', '企業コード'))
            ->naturalNumber('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('{0}には重複しない値を入力してください。', 'コード')])
            ->add('code', 'nop', ['on' => 'update', 'message' => __('{0}は編集できません。', 'コード')]);

        // 名称
        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name', __('{0}は必須です。', '名称'));

        // 電話番号
        $validator
            ->scalar('tel_no')
            ->maxLength('tel_no', 13)
            ->allowEmptyString('tel_no');

        // 創業年月日
        $validator
            ->date('established_date', ['ymd'], __('{0}は日付を入力してください。', '創業年月日'))
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

        // アソシエーション：タグ
        $validator
            ->requirePresence('tags', 'create')
            ->isArray('tags')
            ->hasAtLeast('tags', 0, __('{0}を選択してください。', 'タグ'));

        return $validator;
    }

    /**
     * モデルの概要を取得する
     */
    public function findOverview(Query $query, array $options)
    {
        // $map: 検索マッピング設定 (array)
        $map = [
            'code' => ['type' => 'like'],
            'name' => ['type' => 'like'],
            'staff' => ['type' => 'range']
        ];

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
        return $query->contain([
            'Tags',
            'Notices',
        ]);
    }
}
