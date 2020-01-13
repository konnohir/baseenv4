<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use InvalidArgumentException;
use ArrayObject;
use Exception;

/**
 * App Table
 */
class AppTable extends Table
{
    use \Cake\Log\LogTrait;
    /**
     * 初期化
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    /**
     * 保存前にトリガーされるイベント
     * updated_at に値が設定されていない場合、現在の日時を設定する
     * (MySQLの仕様上、保存するデータに変更がない場合にupdated_at が自動更新されないため)
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!$entity->isDirty('updated_at')) {
            $entity->updated_at = new FrozenTime();
        }
        return $entity;
    }

    /**
     * 検索前にトリガーされるイベント
     * 削除済みのエンティティを除外する設定を行う
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)
    {
        if (!isset($query->withNoActive)) {
            $query->find('active');
        }
        return $query;
    }

    /**
     * 有効なエンティティのみ取得するscope
     */
    public function findActive(Query $query, array $options)
    {
        return $query->where([$this->getAlias() . '.deleted_at is null']);
    }

    /**
     * 有効でないエンティティを含めて取得するscope
     */
    public function findWithNoActive(Query $query, array $options)
    {
        $query->withNoActive = true;
        return $query;
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
                $entity->setError('_lock', __('データが変更されているため、編集内容を保存できません。'));
            }
        }
        $statement->closeCursor();

        return $success;
    }

    /**
     * util: 検索条件を組み立てる
     *
     * @var array $map
     * @var array $options
     */
    public function buildConditions(array $map, array $options)
    {
        $conditions = [];
        foreach ($options as $key => $value) {
            if (!isset($map[$key]['type'])) {
                continue;
            }
            $field = $map[$key]['field'] ?? $this->getAlias() . '.' . $key;
            switch ($map[$key]['type']) {
                // 完全一致
                case 'value':
                    if (is_array($value)) {
                        $conditions[] = ['OR' => array_map(function ($value) use ($field) {
                            return [$field => $value];
                        }, $value)];
                    } else {
                        $conditions[$field] = $value;
                    }
                    break;
                // 部分一致
                case 'like':
                    if (is_array($value)) {
                        $conditions[] = ['OR' => array_map(function ($value) use ($field) {
                            return [$field. ' LIKE' => '%' . $value . '%'];
                        }, $value)];
                    } else {
                        $conditions[$field. ' LIKE'] = '%' . $value . '%';
                    }
                    break;
                // 範囲
                case 'range':
                    if (is_scalar($value)) {
                        list($min, $max) = explode('-', $value);

                        if (is_numeric($min)) {
                            $conditions[$field . ' >='] = $min;
                        }
                        if (is_numeric($max)) {
                            $conditions[$field . ' <='] = $max;
                        }
                    }
                    break;
                // カスタムクエリ
                case 'query':
                    if (!isset($map[$key]['method'])) {
                        throw new Exception('Not implemented');
                    }
                    $result = $this->{$map[$key]['method']}($field, $value, $map[$key] + ['filter' => $options]);
                    if (is_array($result)) {
                        $conditions = array_merge($conditions, $result);
                    }
                    break;
                default:
                    throw new Exception('Not implemented');
            }
        }
        return $conditions;
    }
}
