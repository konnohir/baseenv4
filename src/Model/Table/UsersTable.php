<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
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
     * バリデーションルール
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
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            // 文字列
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            // メールアドレス形式
            'email' => [
                'message' => __('E-V-EMAIL', $label),
                'last' => true,
            ],
            // 最大桁数以内
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
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            // 文字列
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            // 最大桁数以内
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
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            // 自然数
            'naturalNumber' => [
                'message' => __('E-V-NATURAL-NUMBER', $label),
                'last' => true,
            ],
        ]);

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

        return $validator;
    }

    /**
     * パスワード変更バリデーション
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
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            // 文字列
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            // 最大桁数以内
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
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED', $label),
                'last' => true,
            ],
            // 文字列
            'isScalar' => [
                'message' => __('E-V-SCALAR', $label),
                'last' => true,
            ],
            // 最大桁数以内
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

    /**
     * ルール構築
     * DBのデータを使ったルールを定義する
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // メールアドレス
        $rules->add(function ($entity, $options) {
            if (!$entity->isDirty('email')) {
                return true;
            }
            $model = $options['repository'];

            // 削除済みのデータを含めて既に登録済みのメールアドレスでないこと
            if ($model->find('withDeleted')->where(['email' => $entity->email])->count()) {
                return __('E-V-UNIQUE', __('Users.email'));
            }
            return true;
        }, ['errorField' => 'email']);

        // 現在のパスワード
        $rules->add(function ($entity) {
            if (!$entity->isDirty('current_password')) {
                return true;
            }

            // 現在のパスワードが一致すること
            if (!$entity->comparePassword($entity->current_password)) {
                return __('E-V-WRONG-PASSWORD');
            }
            return true;
        }, ['errorField' => 'current_password']);

        // 新しいパスワード
        $rules->add(function ($entity) {
            if (!$entity->isDirty('password')) {
                return true;
            }

            // 新しいパスワードがメールアドレスと異なること
            if ($entity->retype_password == $entity->email) {
                return __('E-V-SAME-PASSWORD-MAIL');
            }
            return true;
        }, ['errorField' => 'password']);
        
        return $rules;
    }

    /**
     * モデルの概要を取得するFinder
     */
    public function findOverview(Query $query, array $options)
    {
        // $map: 検索マッピング設定 (array)
        $map = $this->getFilterSettings();

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $options['filter'] ?? []);

        return $query
            ->select($this)
            ->select(['password_issue' => 'password is not null'])  // 「パスワード発行」一覧画面でソートするためselectで取得する
            ->select(['Roles.name'])
            ->contain(['Roles'])
            ->where($conditions);
    }

    /**
     * モデルの詳細を取得するFinder
     */
    public function findDetail(Query $query, array $options)
    {
        if (isset($options['id'])) {
            $query->where([$this->getAlias() . '.id' => $options['id']]);
        }
        return $query->contain(['Roles']);
    }

    /**
     * ユーザー認証のためのモデルを取得するFinder
     */
    public function findAuthentication(Query $query, array $options)
    {
        // ここで取得したエンティティは認証後、セッションに格納される
        $query
            ->select($this)
            // パスワード未発行のユーザーはログイン不可
            ->where(['password is not null']);
        return $query;
    }

    /**
     * 検索マッピング設定
     * 
     * @return array
     */
    public function getFilterSettings() {
        return [
            'email' => ['type' => 'like'],
        ];
    }

    /**
     * エンティティ編集
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doEditEntity(Entity $entity, array $input)
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'email', 'role_id',
                // lock flag
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * アカウントロック
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doLockAccount(Entity $entity, array $input)
    {
        $input = array_merge_recursive($input, [
            // ログイン失敗回数
            'login_failed_count' => 99,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'login_failed_count',
                // lock flag
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * アカウントロック解除
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doUnlockAccount(Entity $entity, array $input)
    {
        $input = array_merge_recursive($input, [
            // ログイン失敗回数
            'login_failed_count' => 0,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'login_failed_count',
                // lock flag
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * パスワード発行
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doIssuePassword(Entity $entity, array $input)
    {
        // 新しいパスワード
        $password = 'pass#12345';

        $input = array_merge_recursive($input, [
            // パスワード
            'password' => $password,
            // パスワード (平文: CSV出力用)
            'plain_password' => $password,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'password',
                'plain_password',
                // lock flag
                // '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * パスワード変更
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doChangePassword(Entity $entity, array $input)
    {
        $input = array_merge_recursive($input, [
            // パスワード有効期限
            'password_expired' => (new FrozenDate())->addMonths(3)
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'password_expired',
                // user input
                'current_password', 'password', 'retype_password',
                // lock flag
                '_lock',
            ],
            'associated' => [],
            'validate' => 'password',
        ]);
        return $entity;
    }

    /**
     * 削除
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     */
    public function doDeleteEntity(Entity $entity, array $input)
    {
        $input = array_merge_recursive($input, [
            // 削除日時
            'deleted_at' => new FrozenTime(),
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'deleted_at',
                // lock flag
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }
}
