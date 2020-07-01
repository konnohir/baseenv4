<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Fsi\Model\Behavior\FilterTrait;
use Fsi\Model\Behavior\EditLockTrait;

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
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Fsi.Timestamp');
        $this->addBehavior('Fsi.SoftDelete');
    }

    /**
     * バリデーションルール
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
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
