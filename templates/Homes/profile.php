<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <h2 class="mb-2"><?= __('Profile') ?></h2>
    <div class="dl-wrap mb-2">
        <?php
            // メールアドレス
            echo $this->element('dl', [
                'label' => __('Users.email'),
                'value' => $user->email,
            ]);

            // 権限
            echo $this->element('dl', [
                'label' => __('Users.role_id'),
                'value' => $user->role->name ?? null,
            ]);
        ?>
    </div>
    <div class="btn-group my-2">
        <?php
            // パスワード変更
            echo $this->Form->customButton(__('BTN-PASSWORD-CHANGE'), [
                'data-action' => ['controller' => 'Homes', 'action' => 'password'],
                'class' => 'btn-outline-primary btn-jump'
            ])
        ?>
    </div>
</section>

