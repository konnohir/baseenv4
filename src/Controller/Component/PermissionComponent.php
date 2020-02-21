<?php

declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ModelAwareTrait;
use Cake\I18n\FrozenTime;
use App\Model\Entity\Role;

/**
 * PermissionComponent
 * 権限コンポーネント
 */
class PermissionComponent extends Component
{
    use ModelAwareTrait;

    public $components = ['Acl.Acl'];

    /**
     * 設定値
     * @var array
     */
    protected $config;

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->config = $config;
        $this->loadModel('Roles');
    }

    /**
     * ACL更新
     * 
     * @var \App\Model\Entity\Role $role
     * @return bool
     */ 
    public function updateACL(Role $role)
    {
        $role->updated_at = new FrozenTime();
        if (!$this->Roles->save($role)) {
            return false;
        }

        // $aroId
        $aroId = $this->Roles->node($role)->first()->id ?? null;

        // 削除
        $this->Acl->adapter()->Permission->deleteAll(['aro_id' => $aroId]);

        if (!empty($role->deleted_at)) {
            return true;
        }

        // $role
        $role = $this->Roles->get($role->id, [
            'finder' => 'detail',
        ]);

        foreach ($role->role_details as $roleDetail) {
            foreach ($roleDetail->acos as $aco) {
                $this->Acl->allow($aroId, $aco->id);
            }
        }

        return true;
    }

    /**
     * ServerRequestオブジェクト取得
     * @return \Cake\Http\ServerRequest
     */
    protected function getRequest()
    {
        return $this->_registry->getController()->getRequest();
    }
}
