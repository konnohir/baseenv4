<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\Core\Exception\Exception $error
 * @var string $code
 * @var string $message
 * @var string $url
 */
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Inflector;
?>
<div class="w-50 mx-auto">
    <?php if ($error instanceof RecordNotFoundException): ?>
    <h2><?= __($this->getRequest()->getParam('controller')) ?></h2>
    <p class="error">
        <?= __('該当する{0}は削除されています。', __($this->getRequest()->getParam('controller'))) ?>
        <div class="text-center">
        <?= $this->Form->customButton(__('一覧画面へ戻る'), [
            'data-action' => '/' . Inflector::dasherize($this->getRequest()->getParam('controller')),
            'class' => 'btn-secondary btn-cancel'
        ]) ?>
        </div>
    </p>
    <?php else: ?>
    <h2><?= $code ?> <?= __d('cake',$message) ?></h2>
    <p class="error">
        <?= __d('cake', 'The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?>
    </p>
    <?php endif ?>
</div>