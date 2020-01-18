<?php
namespace Fsi\Policy;

use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;
use Cake\Datasource\ModelAwareTrait;

class RequestPolicy implements RequestPolicyInterface
{
    use ModelAwareTrait;

    /**
     * Method to check if the request can be accessed
     *
     * @param \Authorization\IdentityInterface|null Identity
     * @param \Cake\Http\ServerRequest $request Server Request
     * @return bool
     */
    public function canAccess($identity, ServerRequest $request)
    {
        if (!isset($identity->role_id)) {
            return true;
        }

        if ($identity->role_id === 1) {
            return true;
        }

        if ($request->getParam('plugin') === 'debug_kit') {
            return true;
        }
        
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');

        $this->loadModel('Roles');

        $count = $this->Roles->find()
            ->join([
                'table' => 'role_details_roles',
                'alias' => 'a',
                'type' => 'INNER',
                'conditions' => [
                    'a.role_id = Roles.id',
                ],
            ])
            ->join([
                'table' => 'role_details',
                'alias' => 'b',
                'type' => 'INNER',
                'conditions' => [
                    'b.id = a.role_detail_id',
                    'b.deleted_at is null',
                ],
            ])
            ->join([
                'table' => 'role_details_acos',
                'alias' => 'c',
                'type' => 'INNER',
                'conditions' => [
                    'c.role_detail_id = b.id',
                ],
            ])
            ->join([
                'table' => 'acos',
                'alias' => 'd',
                'type' => 'INNER',
                'conditions' => [
                    'd.id = c.aco_id'
                ],
            ])
            ->join([
                'table' => 'acos',
                'alias' => 'e',
                'type' => 'LEFT',
                'conditions' => [
                    'e.id = d.parent_id'
                ],
            ])
            ->where([
                'Roles.id' => $identity->role_id,
                'OR' => [
                    [
                        'e.alias' => $controller,
                        'd.alias' => $action,
                    ],
                    [
                        'e.alias' => 'controllers',
                        'd.alias' => $controller,
                    ]
                ]
            ])
            ->count();

        return ($count > 0);
    }
}
