<?php
declare(strict_types=1);

namespace Konnohir\Model\Behavior;

use Cake\ORM\Query;
use Cake\ORM\Behavior;
use Cake\Event\Event;
use ArrayObject;

/**
 * Class SoftDeleteBehavior
 */
class SoftDeleteBehavior extends Behavior
{
    /**
     * 検索前にトリガーされるイベント
     * 非アクティブのエンティティを除外する設定を行う
     * 
     * @param Event $query
     * @param Query $query
     * @param ArrayObject $options
     * @param $primary
     * @return Query
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)
    {
        if (!isset($query->withInactive)) {
            $query->find('active');
        }
        return $query;
    }

    /**
     * 有効なエンティティのみ取得するFinder
     * 
     * select時に自動的にこのFinderを使用する.
     * 有効でないエンティティを取得したい場合はfind('withInactive')を使用する
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findActive(Query $query, array $options)
    {
        return $query->where([$this->getTable()->getAlias() . '.deleted_at is null']);
    }

    /**
     * 有効でないエンティティを含めて取得するFinder
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findWithInactive(Query $query, array $options)
    {
        $query->withInactive = true;
        return $query;
    }
}