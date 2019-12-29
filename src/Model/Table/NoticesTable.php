<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Notices Model
 *
 */
class NoticesTable extends AppTable
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

        $this->setTable('Notices');
        $this->setDisplayField('message');
        $this->setPrimaryKey('id');
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
            ->allowEmptyString('id', null, 'create');

        // 紐づき先の企業エンティティID
        $validator
            ->naturalNumber('m_copmany_id')
            ->notEmptyString('m_copmany_id', null, 'create');

        // メッセージ
        $validator
            ->scalar('message')
            ->maxLength('message', 45)
            ->requirePresence('message', 'create')
            ->notEmptyString('message');

        // カテゴリID
        $validator
            ->naturalNumber('category_id')
            ->requirePresence('category_id', 'create')
            ->notEmptyString('category_id', null, 'create');

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

        return $validator;
    }

}
