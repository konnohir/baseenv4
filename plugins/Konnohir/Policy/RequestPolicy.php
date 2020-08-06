<?php

declare(strict_types=1);

namespace Konnohir\Policy;

use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

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
     * @var \Cake\Datasource\EntityInterface
     */
    protected static $model = null;

    /**
     * @var array Cache storage
     */
    protected static $cache = [];

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
            // Allow access by special role
            if ($roleId === 1) {
                // return true;
            }
        }

        $controller = $routes['controller'] ?? '';
        $action = $routes['action'] ?? 'index';

        if ($controller === static::$publicController) {
            // Allow access to basic actions
            return true;
        }

        if (!is_callable('App\\Controller\\' . $controller . 'Controller::' . $action)) {
            // will be raised NotFoundException
            return true;
        }

        if (isset(self::$cache[$controller][$action])) {
            // Return cache data
            return self::$cache[$controller][$action];
        }

        if (!isset(self::$model)) {
            self::$model = TableRegistry::getTableLocator()->get('VPermissions');
        }

        $count = self::$model->find()
            ->where([
                'role_id' => $roleId,
                'OR' => [
                    [
                        'controller' => $controller,
                        'action' => $action,
                    ],
                    [
                        'controller' => 'controllers',
                        'action' => $controller,
                    ]
                ]
            ])
            ->count();

        return self::$cache[$controller][$action] = ($count > 0);
    }
}
