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
 * ユーザーマスタ
 */
class UsersTable extends AppTable
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

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->belongsTo('Roles');
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

        // メールアドレス
        $validator->add('email', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
            // メールアドレス形式
            'email' => [
                'message' => __('E-V-EMAIL-FORMAT'),
                'last' => true,
            ],
        ]);

        // パスワード
        $validator->add('password', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        // 権限
        $validator->add('role_id', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        return $validator;
    }

    /**
     * パスワード変更バリデーション
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return Validator
     */
    public function validationPassword(Validator $validator): Validator
    {
        $this->validationDefault($validator);

        // パスワード変更時の必須入力項目
        $validator->requirePresence([
            'current_password',
            'new_password',
            'password',
        ], 'update');

        // パスワード
        $validator->add('password', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
            // 新しいパスワードと一致 [パスワード変更画面]
            'compareFields' => [
                'rule' => ['compareFields', 'new_password', '==='],
                'message' => __('E-V-RETYPE-WRONG-PASSWORD'),
                'last' => true,
            ],
        ]);

        // 現在のパスワード
        $validator->add('current_password', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
        ]);

        // 新しいパスワード
        $validator->add('new_password', [
            // 入力有
            'notBlank' => [
                'message' => __('E-V-REQUIRED'),
                'last' => true,
            ],
            // 現在のパスワードと異なる
            'compareFields' => [
                'rule' => ['compareFields', 'current_password', '!=='],
                'message' => __('E-V-SAME-PASSWORD'),
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
        $rules->add(function ($entity, $option) {
            if (!$entity->isDirty('email')) {
                return true;
            }
            $model = $option['repository'];

            // 削除済みのデータを含めて既に登録済みのメールアドレスでないこと
            if ($model->find('withInactive')->where(['email' => $entity->email])->count()) {
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
     * モデルの概要を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
     */
    protected function findOverview(Query $query, array $option)
    {
        // $map: 検索マッピング設定 (array)
        $map = [
            'email' => ['type' => 'like'],
        ];

        // $conditions: 検索条件の配列 (array)
        $conditions = $this->buildConditions($map, $option['filter'] ?? []);

        return $query
            ->select([
                'Users.id',
                'Users.email',
                'Users.login_failed_count',
                'Roles.name',
                'Users__password_issue' => 'password is not null',
                'Users.updated_at',
            ])
            ->contain(['Roles'])
            ->where($conditions);
    }

    /**
     * モデルの詳細を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
     */
    protected function findDetail(Query $query, array $option)
    {
        if (isset($option['id'])) {
            $query->where([$this->getAlias() . '.id' => $option['id']]);
        }
        return $query
            ->select($this)
            ->select($this->Roles)
            ->select([
                'password_issue' => 'password is not null',
            ])
            ->contain(['Roles']);
    }

    /**
     * ログイン実行時に必要な要素を取得する
     * 
     * @param \Cake\ORM\Query $query クエリオブジェクト
     * @param array $option オプション
     * @return Query
     */
    protected function findAuthentication(Query $query, array $option)
    {
        return $query
            // Note: ここで取得したカラムは認証後、セッションに格納される
            ->select([
                // プライマリーキー
                'id',
                // 認証のため、email、passwordは必須
                'email',
                'password',
                // アカウントロック判定用
                'login_failed_count',
                // パスワード有効期限判定用
                'password_expired',
                // 認可のため、role_idは必須
                'role_id',
            ])
            // パスワード未発行のユーザーはログイン不可
            ->where(['password is not null']);
    }

    /**
     * エンティティ編集
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     * @return Entity
     */
    public function doEditEntity(Entity $entity, array $input = [])
    {
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // user input
                'email', 'role_id',
                // lock token
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
     * @return Entity
     */
    public function doLockAccount(Entity $entity, array $input = [])
    {
        $input = array_merge_recursive($input, [
            // ログイン失敗回数
            'login_failed_count' => 99,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'login_failed_count',
                // lock token
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
     * @return Entity
     */
    public function doUnlockAccount(Entity $entity, array $input = [])
    {
        $input = array_merge_recursive($input, [
            // ログイン失敗回数
            'login_failed_count' => 0,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'login_failed_count',
                // lock token
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
     * @return Entity
     */
    public function doIssuePassword(Entity $entity, array $input = [])
    {
        // 新しいパスワード
        $password = $this->makePassword();

        $input = array_merge_recursive($input, [
            // パスワード
            'password' => $password,
            // パスワード (平文: CSV出力用)
            'plain_password' => $password,
            // パスワード有効期限
            'password_expired' => $entity->created_at
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'password',
                'plain_password',
                'password_expired',
                // lock token
                '_lock',
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
     * @return Entity
     */
    public function doChangePassword(Entity $entity, array $input = [])
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
                'current_password', 'new_password', 'password',
                // lock token
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
     * @return Entity
     */
    public function doDeleteEntity(Entity $entity, array $input = [])
    {
        $input = array_merge_recursive($input, [
            // 削除日時
            'deleted_at' => new FrozenTime(),
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'deleted_at',
                // lock token
                '_lock',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * ログイン失敗回数リセット
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     * @return Entity
     */
    public function doResetLoginFailedCount(Entity $entity, array $input = [])
    {
        $input = array_merge_recursive($input, [
            // ログイン失敗回数
            'login_failed_count' => 0,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'login_failed_count',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * ログイン失敗回数インクリメント
     * 
     * @param \Cake\ORM\Entity $entity エンティティ
     * @param array $input ユーザー入力
     * @return Entity
     */
    public function doIncrementLoginFailedCount(Entity $entity, array $input = [])
    {
        $input = array_merge_recursive($input, [
            // ログイン失敗回数
            'login_failed_count' => $entity->login_failed_count + 1,
        ]);
        $entity = $this->patchEntity($entity, $input, [
            'fields' => [
                // application input
                'login_failed_count',
            ],
            'associated' => []
        ]);
        return $entity;
    }

    /**
     * パスワード文字列生成
     * 
     * @return string ランダムパスワード
     */
    private function makePassword()
    {
        return 'pass#12345';
    }
}
