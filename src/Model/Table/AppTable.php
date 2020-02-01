<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Fsi\Model\Behavior\FilterTrait;
use Fsi\Model\Behavior\EditLockTrait;

/**
 * App Table
 */
class AppTable extends Table
{
    use FilterTrait;
    use EditLockTrait;

    /**
     * 初期化
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Fsi.Timestamp');
        $this->addBehavior('Fsi.SoftDelete');
    }
}
