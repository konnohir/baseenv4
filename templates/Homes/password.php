<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="w-50 mx-auto">
    <h2 class="mb-2"><?= __('Password') ?></h2>
    <div class="form">
        <?= $this->Form->create($user) ?>
        <?= $this->Form->hidden('_lock') ?>
        <?= $this->Form->customControl('current_password', [
            'type' => 'password',
            'label' => __('現在のパスワード'),
            'value' => false,   // バリデーションエラー時に入力欄をクリアする
        ]) ?>
        <?= $this->Form->customControl('password', [
            'type' => 'password',
            'label' => __('新しいパスワード'),
            'value' => false,
        ]) ?>
        <?= $this->Form->customControl('retype_password', [
            'type' => 'password',
            'label' => __('新しいパスワード（再入力）'),
            'value' => false,
        ]) ?>
        <div class="form-group text-center py-4">
            <?= $this->Form->customButton(__('BTN-CANCEL'), [
                // キャンセル
                'data-action' => '/profile',
                'class' => 'btn-secondary btn-jump'
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