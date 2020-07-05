<?php
declare(strict_types=1);

namespace Konnohir\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use ArrayObject;

/**
 * Class TimestampBehavior
 */
class TimestampBehavior extends Behavior
{
    /**
     * 保存前にトリガーされるイベント
     * updated_at に値が設定されていない場合、現在の日時を設定する
     * (MySQLの仕様上、保存するデータとテーブルのデータが同一の場合にupdated_at が自動更新されないため)
     * 
     * @param Event $query
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return EntityInterface
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!$entity->isDirty('updated_at')) {
            if (!isset($entity->_lock)) {
                $entity->_lock = '';
            }
            $entity->updated_at = new FrozenTime();
        }
        return $entity;
    }
}