<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

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
}
