<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use InvalidArgumentException;
use ArrayObject;
use Exception;
use Fsi\Model\Behavior\FilterTrait;

/**
 * App Table
 */
class AppTable extends Table
{
    use FilterTrait;

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
        $primaryColumns = (array)$this->getPrimaryKey();
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
            }else {
                $entity->setError('_lock', __('E-V-LOCK'));
            }
        }
        $statement->closeCursor();

        return $success;
    }
}
