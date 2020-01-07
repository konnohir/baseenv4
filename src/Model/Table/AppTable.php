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
use ArrayObject;
use Exception;

/**
 * App Table
 */
class AppTable extends Table
{
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
     * 検索開始前にトリガーされるイベント
     * 削除済みのエンティティを除外する設定を行う
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)
    {
        if (!isset($query->withDeleted)) {
            $query->where([$this->getAlias() . '.deleted_at is null']);
        }
        return $query;
    }

    /**
     * 削除済みのエンティティを含めるscope
     */
    public function findWithDeleted(Query $query, array $options)
    {
        $query->withDeleted = true;
        return $query;
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
                        throw($key);
                        break;
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
