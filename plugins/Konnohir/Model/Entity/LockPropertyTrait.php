<?php
declare(strict_types=1);

namespace Konnohir\Model\Entity;

/**
 * LockPropertyTrait
 */
trait LockPropertyTrait
{
    /**
     * _lockプロパティGetter(Readonly)
     * 
     * @return string updated_atプロパティの初期値
     */
    public function _get_lockReadonly() {
        return $this->_fields['_lock'] ?? null;
    }

    /**
     * _lockプロパティGetter
     * 
     * @return string updated_atプロパティの初期値
     */
    public function _get_lock(?string $_lock) {
        if (!isset($_lock) && isset($this->updated_at)) {
            $_lock = $this->_lock = $this->getOriginal('updated_at')->format('Y-m-d H:i:s.u');
        }
        return $_lock;
    }

    /**
     * _lockプロパティSetter
     */
    public function _set_lock($value) {
        if (isset($value)) {
            return (string)$value;
        }
        return $value;
    }
    
}
