<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<div class="w-50 mx-auto">
    <h2 class="mb-2"><?= __('Roles') ?></h2>
    <div class="form">
        <?= $this->Form->create($role) ?>
        <?= $this->Form->hidden('_lock') ?>
        <?= $this->Form->customControl('name', [
            'type' => 'text',
            'label' => __('名称'),
        ]) ?>
        <?= $this->Form->customControl('description', [
            'type' => 'text',
            'label' => __('説明'),
        ]) ?>
        <div class="form-group text-center py-4">
            <?= $this->Form->customButton(__('BTN-CANCEL'), [
                // キャンセル
                'data-action' => '/roles',
                'class' => 'btn-secondary btn-cancel'
            ]) ?>
            <?= $this->Form->customButton(__('BTN-SAVE'), [
                // 保存
                'type' => 'submit',
                'class' => 'btn-primary'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>