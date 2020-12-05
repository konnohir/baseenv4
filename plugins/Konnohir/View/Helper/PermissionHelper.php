<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Konnohir\View\Helper;

use Cake\View\Helper;
use Konnohir\Policy\RequestPolicy;

/**
 * Permission helper library.
 */
class PermissionHelper extends Helper
{
    public function check(array $routes = []): bool
    {
        $request = $this->getView()->getRequest();
        $routes += [
            'controller' => $request->getParam('controller'),
            'action' => 'index',
        ];
        $identity = $request->getAttribute('identity');

        return RequestPolicy::check($identity->role_id, $routes);
    }
}
