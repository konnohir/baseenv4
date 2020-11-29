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
 * @since         2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Konnohir\Error;

use Cake\Error\ExceptionRenderer as BaseExceptionRenderer;
use Throwable;

/**
 * Exception Renderer.
 *
 * Captures and handles all unhandled exceptions. Displays helpful framework errors when debug is true.
 * When debug is false a ExceptionRenderer will render 404 or 500 errors. If an uncaught exception is thrown
 * and it is a type that ExceptionHandler does not know about it will be treated as a 500 error.
 *
 * ### Implementing application specific exception rendering
 *
 * You can implement application specific exception handling by creating a subclass of
 * ExceptionRenderer and configure it to be the `exceptionRenderer` in config/error.php
 *
 * #### Using a subclass of ExceptionRenderer
 *
 * Using a subclass of ExceptionRenderer gives you full control over how Exceptions are rendered, you
 * can configure your class in your config/app.php.
 */
class ExceptionRenderer extends BaseExceptionRenderer
{
    protected $redirectToNotFoundExceptions = [
        \Cake\Datasource\Exception\RecordNotFoundException::class,
        \Cake\Http\Exception\MissingControllerException::class,
        \Cake\Controller\Exception\MissingActionException::class,
    ];

    /**
     * Get error message.
     *
     * @param \Throwable $exception Exception.
     * @param int $code Error code.
     * @return string Error message
     */
    protected function _message(Throwable $exception, int $code): string
    {
        foreach($this->redirectToNotFoundExceptions as $item) {
            if ($exception instanceof $item) {
                return 'Not Found';
            }
        }
        return parent::_message($exception, $code);
    }

    /**
     * Get template for rendering exception info.
     *
     * @param \Throwable $exception Exception instance.
     * @param string $method Method name.
     * @param int $code Error code.
     * @return string Template name
     */
    protected function _template(Throwable $exception, string $method, int $code): string
    {
        $builder = $this->controller->viewBuilder();
        $builder->setTemplatePath('Error');

        if ($this->controller->getRequest()->is('json')) {
            $builder->setClassName('Json');
        }

        foreach($this->redirectToNotFoundExceptions as $item) {
            if ($exception instanceof $item) {
                return $this->template = 'error400';
            }
        }

        return parent::_template($exception, $method, $code);
    }
}
