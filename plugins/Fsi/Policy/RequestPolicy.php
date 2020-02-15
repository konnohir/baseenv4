<?php

namespace Fsi\Policy;

use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

/**
 * RequestPolicy class
 * 
 * Note: Plugin and Prefix parameters are not supported.
 */
class RequestPolicy implements RequestPolicyInterface
{
    /**
     * @var string
     */
    public static $publicController = 'Homes';

    /**
     * Method to check if the request can be accessed
     *
     * @param \Authorization\IdentityInterface|null Identity
     * @param \Cake\Http\ServerRequest $request Server Request
     * @return bool
     */
    public function canAccess($identity, ServerRequest $request)
    {
        if (!isset($identity)) {
            // Authentication middleware handle this request
            return true;
        }

        if (Configure::read('debug')) {
            // Allow DebugKit plugin access
            if ($request->getParam('plugin') === 'DebugKit') {
                return true;
            }
        }

        return $this->check($identity->role_id, [
            'controller' => $request->getParam('controller'),
            'action' => $request->getParam('action'),
        ]);
    }

    /**
     * Permission check
     *
     * @param int $roleId
     * @param array $routes
     * @return bool
     */
    public static function check(int $roleId, array $routes)
    {
        if (Configure::read('debug')) {
            // Allow special role access
            if ($roleId === 1) {
                return true;
            }
        }

        $controller = $routes['controller'] ?? '';
        $action = $routes['action'] ?? 'index';

        if (!is_callable('App\\Controller\\' . $controller . 'Controller::' . $action)) {
            // will be raised NotFoundException
            return true;
        }

        if ($controller === static::$publicController) {
            // Allow basic actions access
            return true;
        }

        $model = TableRegistry::getTableLocator()->get('Roles');

        $count = $model->find()
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
                'Roles.id' => $roleId,
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
