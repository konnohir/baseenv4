<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MDepartment3 Entity
 *
 * @property int $id
 * @property int $m_department2_id
 * @property string $code
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 * @property \Cake\I18n\FrozenTime|null $deleted_at
 *
 * @property \App\Model\Entity\MDepartment2 $m_department2
 */
class MDepartment3 extends AppEntity
{
}
