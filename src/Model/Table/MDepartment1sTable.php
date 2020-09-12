<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * MDepartment1s Model
 * 部門（階層1）マスタ
 */
class MDepartment1sTable extends AppTable
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

        $this->setTable('m_department1s');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('MDepartment2s');
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
}
