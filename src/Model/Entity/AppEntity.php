<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\FrozenTime;

/**
 * App Entity
 * 基盤エンティティ
 */
class AppEntity extends Entity
{

    /**
     * _lockプロパティGetter
     * 
     * @return string updated_atプロパティの初期値
     */
    public function _get_lock(?string $_lock) {
        if (!isset($_lock) && isset($this->updated_at)) {
            $_lock = $this->_lock = $this->getOriginal('updated_at')->format('Y-m-d H:i:s');
        }
        return $_lock;
    }

    /**
     * _lockプロパティSetter
     */
    public function _set_lock($value) {
        return (string)$value;
    }
    
    // /**
    //  * created_at
    //  */
    // public function _set_created_at($value) {
    //     return $this->created_at = new FrozenTime($value);
    // }
    
    // /**
    //  * updated_at
    //  */
    // public function _set_updated_at($value) {
    //     return $this->updated_at = new FrozenTime($value);
    // }
}
