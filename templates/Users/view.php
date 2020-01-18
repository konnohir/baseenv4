<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <h2 class="mb-2"><?= __('Users') ?></h2>
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
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => '/users',
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/users/edit',
            'data-id' => $user->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/users/delete',
            'data-id' => $user->id,
            'data-lock' => $user->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>