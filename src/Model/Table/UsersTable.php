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

        // @ACL
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
        // 新規作成時の必須入力項目
        $validator->requirePresence([
            'email',
            'role_id',
        ], 'create');

        // ID
        $validator->naturalNumber('id');

        // メールアドレス
        $column = 'email';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            'email' => [
                'message' => __('E-V-EMAIL', $label),
                'last' => true,
            ],
            'maxLength' => [
                'rule' =>  ['maxLength', 255],
                'message' => __('E-V-MAXLENGTH', $label, 255),
                'last' => true,
            ],  
        ]);

        // パスワード
        $column = 'password';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            'maxLength' => [
                'rule' =>  ['maxLength', 255],
                'message' => __('E-V-MAX-LENGTH', $label),
                'last' => true,
            ],  
        ]);

        // 権限
        $column = 'role_id';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            'naturalNumber' => [
                'message' => __('E-V-NATURAL-NUMBER', $label),
                'last' => true,
            ],
        ]);

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
     * パスワード変更バリデーション.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationPassword(Validator $validator): Validator
    {
        $this->validationDefault($validator);
        
        // パスワード変更時の必須入力項目
        $validator->requirePresence([
            'current_password',
            'password',
            'retype_password',
        ]);

        // 現在のパスワード
        $column = 'current_password';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            'maxLength' => [
                'rule' =>  ['maxLength', 255],
                'message' => __('E-V-MAX-LENGTH', $label),
                'last' => true,
            ],  
        ]);
        
        // 新しいパスワード
        $column = 'password';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            // 現在のパスワードと異なる
            'compareFields' => [
                'rule' => ['compareFields', 'current_password', '!=='],
                'message' => __('E-V-SAME-PASSWORD'),
                'last' => true,
            ],
        ]);

        // 新しいパスワード (再入力)
        $column = 'retype_password';
        $label = __($this->getAlias() . '.' . $column);
        $validator->add($column, [
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            'maxLength' => [
                'rule' =>  ['maxLength', 255],
                'message' => __('E-V-MAX-LENGTH', $label),
                'last' => true,
            ],
            // 新しいパスワードと同一
            'compareFields' => [
                'rule' => ['compareFields', 'password', '==='],
                'message' => __('E-V-RETYPE-WRONG-PASSWORD'),
                'last' => true,
            ],
        ]);
        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add(function($entity) {
            if (!isset($entity->current_password)) {
                return true;
            }
            if (!$entity->comparePassword($entity->current_password)) {
                return __('E-V-WRONG-PASSWORD');
            }
            return true;
        }, ['errorField' => 'current_password']);

        $rules->add(function($entity) {
            if (!isset($entity->retype_password)) {
                return true;
            }
            if ($entity->retype_password == $entity->email) {
                return __('E-V-SAME-PASSWORD-MAIL');
            }
            return true;
        }, ['errorField' => 'password']);
        return $rules;
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
        if (isset($options['id'])) {
            $query->where([$this->getAlias() . '.id' => $options['id']]);
        }
        return $query->contain(['Roles']);
    }
}
