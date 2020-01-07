<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 * @property \Cake\I18n\FrozenTime|null $deleted_at
 */
class Role extends AppEntity
{
    /**
     * 親ノードを取得する (ACLプラグイン)
     * 権限は常にトップレベルのため、NULLを返す
     */
    public function parentNode()
    {
        return null;
    }
}
