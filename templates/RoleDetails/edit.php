<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail $parentRoleDetailList
 */
?>
<div class="w-50 mx-auto">
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <div class="form">
        <?= $this->Form->create($roleDetail) ?>
        <?= $this->Form->hidden('_lock') ?>
        <?= $this->Form->customControl('parent_id', [
            'type' => 'select',
            'options' => $parentRoleDetailList,
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

        <label><?= __('アクション') ?></label>
        <ul class="py-4" style="list-style-type: none; border:solid 1px lightgray;border-radius: 6px">
            <?= $this->Form->hidden('acos._ids') ?>
            <?php foreach ($acos as $controller) : ?>
                <li>
                    <?=
                        // 権限詳細 (親)
                        $this->Form->customControl('acos._ids', [
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'options' => [$controller->id => $controller->alias],
                            'label' => false,
                            'hiddenField' => false,
                            'data-type' => 'controller',
                        ]) ?>
                        <ul style="list-style-type: none">
                            <?php foreach ($controller->children as $action) : ?>
                                <li>
                                    <?=
                                        // 権限詳細 (子)
                                        $this->Form->customControl('acos._ids', [
                                            'type' => 'select',
                                            'multiple' => 'checkbox',
                                            'label' => false,
                                            'options' => [$action->id => $action->alias],
                                            'hiddenField' => false,
                                        ]) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                </li>
            <?php endforeach ?>
        </ul>

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