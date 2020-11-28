<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Konnohir\Model\Entity\LockPropertyTrait;

/**
 * App Entity
 * エンティティ基底クラス
 */
class AppEntity extends Entity
{
    use LockPropertyTrait;   
}
