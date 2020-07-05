<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Konnohir\Model\Behavior;

use Cake\Datasource\EntityInterface;
use InvalidArgumentException;

/**
 * EditLockTrait
 */
trait EditLockTrait
{

    /**
     * Auxiliary function to handle the update of an entity's data in the table
     *
     * @param \Cake\Datasource\EntityInterface $entity the subject entity from were $data was extracted
     * @param array $data The actual data that needs to be saved
     * @return \Cake\Datasource\EntityInterface|false
     * @throws \InvalidArgumentException When primary key data is missing.
     */
    protected function _update(EntityInterface $entity, array $data)
    {
        $primaryColumns = (array) $this->getPrimaryKey();
        $primaryKey = $entity->extract($primaryColumns);

        $data = array_diff_key($data, $primaryKey);
        if (empty($data)) {
            return $entity;
        }

        if (count($primaryColumns) === 0) {
            $entityClass = get_class($entity);
            $table = $this->getTable();
            $message = "Cannot update `$entityClass`. The `$table` has no primary key.";
            throw new InvalidArgumentException($message);
        }

        if (!$entity->has($primaryColumns)) {
            $message = 'All primary key value(s) are needed for updating, ';
            $message .= get_class($entity) . ' is missing ' . implode(', ', $primaryColumns);
            throw new InvalidArgumentException($message);
        }

        $query = $this->query();
        $query->update()
            ->set($data)
            ->where($primaryKey);

        if (!empty($entity->_lock)) {
            $query->where([$this->getAlias() . '.updated_at' => $entity->_lock]);
        }

        $statement = $query->execute();

        $success = false;
        if ($statement->errorCode() === '00000') {
            if ($statement->rowCount()) {
                $entity->_lock = null;
                $success = $entity;
            } else {
                $entity->setError('_lock', __('E-V-LOCK'));
            }
        }
        $statement->closeCursor();

        return $success;
    }
}
