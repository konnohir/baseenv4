<?php
namespace App\Model\Validation;

use Cake\Validation\Validation;
use Authentication\PasswordHasher\DefaultPasswordHasher;

/**
 * CustomValidation
 */
class CustomValidation extends Validation
{
    /**
     * 常にバリデーションエラーにする (on句と併用する)
     * @param string $value
     * @return bool
     */
    public static function nop($value)
    {
        return false;
    }

    public static function sameCurrentPassword(string $password, array $context) 
    {
        $dbPassword = $context['data']['_password'];
        $hasher = new DefaultPasswordHasher();
        return $hasher->check($password, $dbPassword);
    }

    public static function notSameEmail(string $password, array $context) 
    {
        $dbMailAddress = $context['data']['_email'];
        return $password !== $dbMailAddress;
    }

    public static function uniqueEmail(string $email, array $context) 
    {
        $id = $context['data']['id'] ?? null;
        $model = $context['providers']['table'];

        // 削除済みのデータを含めて既に登録済みのメールアドレスでないこと
        $query = $model->find('withInactive')->where(['email' => $email]);
        if ($id) {
            $query->where(['id <>' => $id]);
        }

        return $query->count() === 0;
    }
}
