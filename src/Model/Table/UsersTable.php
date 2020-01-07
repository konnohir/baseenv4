<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Users Model
 * ユーザーマスタ
 */
class UsersTable extends AppTable
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

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->belongsTo('Roles');

        $this->addBehavior('Acl.Acl', ['requester']);
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

        // メールアドレス
        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email', __('{0}は必須です。', 'メールアドレス'))
            ->maxLength('password', 255)
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('{0}には重複しない値を入力してください。', 'メールアドレス')]);

        // パスワード
        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->allowEmptyString('password');

        // 権限
        $validator
            ->requirePresence('role_id', 'create')
            ->notEmptyString('role_id', __('{0}は必須です。', '権限'))
            ->naturalNumber('role_id');

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

    /**
     * モデルの概要を取得する
     */
    public function findOverview(Query $query, array $options)
    {

        // $map: 検索マッピング設定 (array)
        $map = [
            'email' => ['type' => 'like'],
        ];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $options['filter'] ?? []);

        return $query
            ->select($this)
            ->select(['password_issue' => 'password is not null'])
            ->select(['Roles.name'])
            ->contain(['Roles'])
            ->where($conditions);
    }

    /**
     * モデルの詳細を取得する
     */
    public function findDetail(Query $query, array $options)
    {
        return $query->contain(['Roles']);
    }
}
