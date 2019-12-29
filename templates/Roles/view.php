<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<div class="w-50 mx-auto">
    <h2 class="mb-2"><?= __('Roles') ?></h2>
    <table class="table mb-2 table-border border">
        <tr>
            <th><?= __('名称') ?></th>
            <td><?= h($role->name) ?></td>
        </tr>
        <tr>
            <th><?= __('説明') ?></th>
            <td><?= nl2br(h($role->description)) ?></td>
        </tr>
    </table>
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => '/roles',
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/roles/edit',
            'data-id' => $role->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/roles/delete',
            'data-id' => $role->id,
            'data-lock' => $role->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</div>