<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Query;

/**
 * MDepartment3s Model
 * 部門（階層3）マスタ
 */
class MDepartment3sTable extends AppTable
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

        $this->setTable('m_department3s');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('MOrganizations');
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

        // 課コード
        $validator->add('code', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
            // 重複無し
            'uniqueCode' => [
                'message' => __('E-V-UNIQUE'),
                'provider' => 'table',
                'last' => true,
            ],
        ]);

        // 課名
        $validator->add('name', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
            // 重複無し
            'uniqueName' => [
                'message' => __('E-V-UNIQUE'),
                'provider' => 'table',
                'last' => true,
            ],
        ]);

        return $validator;
    }

    /**
     * コードに重複がないか判定する
     * 
     * @param mixed $value 入力値
     * @param array $options オプション
     * @return bool True: OK
     */
    public function uniqueCode($value, array $options): bool
    {
        // $id: レコードID (新規作成ならnull)
        $id = $options['data']['id'] ?? null;

        if ($id !== null) {
            // $oldCode: 編集前のコード
            $oldCode = $this
                ->find()
                ->select('code')
                ->where(['id' => $id])
                ->enableHydration(false)
                ->firstOrFail()['code'];

            // $newCode: 編集後のコード
            $newCode = $options['data']['code'] ?? null;

            if ($oldCode === $newCode) {
                return true;
            }
        }

        // $m_department2_id: 所属部店ID
        $m_department2_id = $options['data']['m_department2_id'] ?? null;

        $count = $this
            ->find('activeRecord')
            ->where(['MOrganizations.m_department2_id' => $m_department2_id])
            ->where(['code' => $value])
            ->count();
        return ($count === 0);
    }

    /**
     * 名称に重複がないか判定する
     * 
     * @param mixed $value 入力値
     * @param array $options オプション
     * @return bool True: OK
     */
    public function uniqueName($value, array $options): bool
    {
        // $id: レコードID (新規作成ならnull)
        $id = $options['data']['id'] ?? null;

        if ($id !== null) {
            // $oldName: 編集前の名称
            $oldName = $this
                ->find()
                ->select('name')
                ->where(['id' => $id])
                ->enableHydration(false)
                ->firstOrFail()['name'];

            // $newName: 編集後の名称
            $newName = $options['data']['name'] ?? null;

            if ($oldName === $newName) {
                return true;
            }
        }

        // $m_department2_id: 所属部店ID
        $m_department2_id = $options['data']['m_department2_id'] ?? null;

        $count = $this
            ->find('activeRecord')
            ->where(['MOrganizations.m_department2_id' => $m_department2_id])
            ->where(['name' => $value])
            ->count();
        return ($count === 0);
    }

    /**
     * 組織マスタに登録されている(削除済みでない)課の一覧を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
     */
    protected function findActiveRecord(Query $query, array $option)
    {
        return $query
            ->matching('MOrganizations', function ($q) {
                return $q;
            });
    }
}
