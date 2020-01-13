<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="w-50 mx-auto">
    <h2 class="mb-2"><?= __('Profile') ?></h2>
    <table class="table mb-2 table-border border">
        <tr>
            <th><?= __('メールアドレス') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th><?= __('権限') ?></th>
            <td><?= h($user->role->name ?? null) ?></td>
        </tr>
    </table>
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-PASSWORD-CHANGE'), [
            // パスワード変更
            'data-action' => '/password',
            'class' => 'btn-outline-primary btn-jump'
        ]) ?>
    </div>
</div>