<?php
declare(strict_types=1);

namespace Fsi\Model\Behavior;

use Exception;

/**
 * FilterTrait
 */
trait FilterTrait
{
    /**
     * util: 検索条件を組み立てる
     *
     * @var array $map 検索マッピング
     * @var array $options オプション
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
                case '==':
                    if (is_array($value)) {
                        $conditions[] = ['OR' => array_map(function ($value) use ($field) {
                            return [$field => $value];
                        }, $value)];
                    } else {
                        $conditions[$field] = $value;
                    }
                    break;
                // 以上
                case '>=':
                    if (is_scalar($value)) {
                        $conditions[$field.' >='] = $value;
                    }
                    break;
                // 以下
                case '<=':
                    if (is_scalar($value)) {
                        $conditions[$field.' <='] = $value;
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

    public function valueFilter() {

    }
}