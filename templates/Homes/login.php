<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="w-50 mx-auto">
    <div class="form">
        <?= $this->Form->create(null) ?>
        <?= $this->Form->customControl('email', [
            'type' => 'text',
            'label' => __('メールアドレス'),
        ]) ?>
        <?= $this->Form->customControl('password', [
            'type' => 'password',
            'label' => __('パスワード'),
        ]) ?>
        <div class="form-group text-center py-4">
            <?= $this->Form->customButton(__('BTN-LOGIN'), [
                // ログイン
                'type' => 'submit',
                'class' => 'btn-secondary btn-login'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>