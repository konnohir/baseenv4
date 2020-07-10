<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Konnohir\Model\Behavior\FilterTrait;
use Konnohir\Model\Behavior\EditLockTrait;

/**
 * App Table
 */
class AppTable extends Table
{
    use FilterTrait;
    use EditLockTrait;

    /**
     * 初期化
     *
     * @param array $config 設定値
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Konnohir.Timestamp');
        $this->addBehavior('Konnohir.SoftDelete');
    }

    /**
     * バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        foreach($this->getSchema()->columns() as $column) {
            $columnInfo = $this->getSchema()->getColumn($column);
            $isRequirePresence = ($columnInfo['null'] === false && $columnInfo['default'] === null && (!isset($columnInfo['autoIncrement']) || $columnInfo['autoIncrement'] === false));
            if ($isRequirePresence) {
                $validator->requirePresence($column, 'create', __('E-V-REQUIRED'));
            }
            if ($columnInfo['null'] === true) {
                $validator->allowEmptyFor($column);
            }
        }
        return $validator;
    }
}
