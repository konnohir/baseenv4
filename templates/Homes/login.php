<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section>
    <h2><?= __('Login') ?></h2>
    <?= $this->Form->create() ?>
        <?php 
            // メールアドレス
            echo $this->Form->customControl('email', [
                'type' => 'text',
                'placeholder' => __('Users.email'),
                'label' => false,
            ]);
            // パスワード
            echo $this->Form->customControl('password', [
                'type' => 'password',
                'placeholder' => __('Users.password'),
                'label' => false,
            ]);
            // ログイン
            echo $this->Form->customButton(__('BTN-LOGIN'), [
                'type' => 'submit',
                'class' => 'btn-success btn-login w-100'
            ]);
        ?>
    <?= $this->Form->end() ?>
</section>