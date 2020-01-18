<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array $roleList
 */
?>
<section>
    <h2 class="mb-2"><?= __('Users') ?></h2>
    <div class="form">
        <?= $this->Form->create($user) ?>
        <?= $this->Form->hidden('_lock') ?>
        <?= $this->Form->customControl('email', [
            'type' => 'text',
            'label' => __('メールアドレス'),
        ]) ?>
        <?= $this->Form->customControl('role_id', [
            'type' => 'select',
            'label' => __('権限'),
            'options' => $roleList,
            'empty' => true,
        ]) ?>
        <div class="form-group text-center py-4">
            <?= $this->Form->customButton(__('BTN-CANCEL'), [
                // キャンセル
                'data-action' => '/users',
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
</section>