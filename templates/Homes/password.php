<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <!-- タイトル -->
    <h2><?= __('Password') ?></h2>
    <!-- データ入力領域-->
    <?= $this->Form->create($user, ['type' => 'post', 'templates' => $this->Form->formTemplates]) ?>
    <?= $this->Form->hidden('_lock') ?>
    <div class="dl-wrap dl-wrap-form">
        <?php
            // 現在のパスワード
            echo $this->Form->customControl('current_password', [
                'type' => 'password',
                'label' => __('Users.current_password'),
                'required' => true,
            ]);
            // 新しいパスワード
            echo $this->Form->customControl('new_password', [
                'type' => 'password',
                'label' => __('Users.new_password'),
                'required' => true,
            ]);
            // 新しいパスワード（再入力）
            echo $this->Form->customControl('password', [
                'type' => 'password',
                'label' => __('Users.password.2'),
                'value' => '',
                'required' => true,
            ]);
        ?>
    </div>
    <!-- ボタン表示領域 -->
    <div class="button-wrap">
        <?php
            // キャンセル
            echo $this->Form->customButton(__('BTN-CANCEL'), [
                'data-action' => ['controller' => 'Homes', 'action' => 'profile'],
                'class' => 'btn-secondary btn-jump'
            ]);
            // 保存
            echo $this->Form->customButton(__('BTN-SAVE'), [
                'class' => 'btn-primary btn-submit'
            ]);
        ?>
    </div>
    <?= $this->Form->end() ?>
</section>