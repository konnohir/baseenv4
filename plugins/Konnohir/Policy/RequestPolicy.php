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
     * @var string Public controller name
     */
    public const PUBLIC_CONTROLLER = 'Homes';

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
        if ($identity === null) {
            // Authentication middleware will handle this request
            return true;
        }

        if ($request->getParam('plugin') === 'DebugKit') {
            // Allow DebugKit plugin access if debug mode
            return Configure::read('debug');
        }

        $controller = $request->getParam('controller');
        $action = $request->getParam('action');

        if (!is_callable('App\\Controller\\' . $controller . 'Controller::' . $action)) {
            // will be raised NotFoundException
            return true;
        }

        return $this->check($identity->role_id, [
            'controller' => $controller,
            'action' => $action,
        ]);
    }

    /**
     * Permission check
     *
     * @param int $roleId
     * @param array $route
     * @return bool
     */
    public static function check(int $roleId, array $route)
    {
        $controller = $route['controller'] ?? '';
        if ($controller === self::PUBLIC_CONTROLLER) {
            // Allow access to basic actions
            return true;
        }
        $action = $route['action'] ?? 'index';
        
        if (!isset(self::$cache[$controller][$action])) {
            $count = TableRegistry::getTableLocator()->get('VPermissions')->find()
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
            self::$cache[$controller][$action] = ($count > 0);
        }

        return self::$cache[$controller][$action];
    }
}
