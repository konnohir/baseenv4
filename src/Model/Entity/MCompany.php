<?php
declare(strict_types=1);

namespace App\Model\Entity;

/**
 * MCompany Entity
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $tel_no
 * @property int|null $staff
 * @property \Cake\I18n\FrozenTime|null $established_date
 * @property string|null $note
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 * @property \Cake\I18n\FrozenTime|null $deleted_at
 */
class MCompany extends AppEntity
{
}
