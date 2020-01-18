<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <h2 class="mb-2"><?= __('Profile') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <?php // メールアドレス ?>
            <dt class="col-md"><?= __('Users.email') ?></dt>
            <dd class="col-md"><?= h($user->email) ?></dd>
        </dl>
        <dl class="row">
            <?php // 権限 ?>
            <dt class="col-md"><?= __('Users.role_id') ?></dt>
            <dd class="col-md"><?= h($user->role->name ?? null) ?></dd>
        </dl>
    </div>
    <div class="btn-group mb-2">
        <?php
            // パスワード変更
            echo $this->Form->customButton(__('BTN-PASSWORD-CHANGE'), [
                'data-action' => '/password',
                'class' => 'btn-outline-primary btn-jump'
            ])
        ?>
    </div>
</section>