<?php
declare(strict_types=1);

namespace App\Model\Entity;

/**
 * Notice Entity
 *
 * @property int $id
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 * @property \Cake\I18n\FrozenTime|null $deleted_at
 */
class Notice extends AppEntity
{
}
