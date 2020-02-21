<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Error Controller
 * エラー画面
 */
class ErrorController extends AppController
{
    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null|void
     */
    public function beforeRender(EventInterface $event)
    {
        $builder = $this->viewBuilder();
        $builder->setTemplatePath('Error');

        if ($this->getRequest()->is('json')) {
            $builder->setClassName('Json');
        }
    }
}
