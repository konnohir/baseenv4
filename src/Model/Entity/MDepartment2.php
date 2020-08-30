<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MDepartment2 Entity
 *
 * @property int $id
 * @property int $m_department1_id
 * @property string $code
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 * @property \Cake\I18n\FrozenTime|null $deleted_at
 *
 * @property \App\Model\Entity\MDepartment1 $m_department1
 * @property \App\Model\Entity\MDepartment3[] $m_department3s
 */
class MDepartment2 extends AppEntity
{
}
