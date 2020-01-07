<?php
namespace App\Model\Validation;

use Cake\Validation\Validation;

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
}
