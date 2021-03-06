<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Exception;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property int $role_id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 * @property \Cake\I18n\FrozenTime|null $deleted_at
 */
class User extends AppEntity
{
    /**
     * 非表示フィールドのリスト
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * パスワードセッター
     * パスワードをハッシュ化する
     */
    protected function _setPassword(string $value)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($value);
    }

    /**
     * パスワード比較
     * 
     * @return boolean
     */
    public function comparePassword(string $password) {
        $hasher = new DefaultPasswordHasher();
        return $hasher->check($password, $this->getOriginal('password'));
    }
}
