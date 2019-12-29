<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail $roleDetail
 */
?>
<div class="w-50 mx-auto">
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <div class="form">
        <?= $this->Form->create($roleDetail) ?>
        <?= $this->Form->hidden('_lock') ?>
        <?= $this->Form->customControl('parent_id', [
            'type' => 'select',
            'options' => $roleDetailList,
            'empty' => true,
            'label' => __('親権限'),
        ]) ?>
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