<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\Validation\Validator;

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

        $this->belongsTo('MDepartment2s');
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

        // 部店ID
        $validator->add('m_department2_id', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        // 課コード
        $validator->add('code', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
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
        ]);

        return $validator;
    }
}
