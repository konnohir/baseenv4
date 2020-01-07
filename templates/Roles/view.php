<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var \App\Model\Entity\RoleDetail $roleDetails
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
        <tr>
            <th><?= __('権限') ?></th>
            <td>
                <ul style="list-style-type: none;padding-left:0">
                    <?php foreach ($roleDetails as $roleDetail) : ?>
                        <li>
                            <?=
                                // 権限詳細 (親)
                                $this->Form->customControl('role_details._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => [$roleDetail->id => $roleDetail->name],
                                    'default' => array_column((array)$role->role_details, 'id'),
                                    'readonly' => true,
                                    'label' => false,
                                    'hiddenField' => false,
                                ]) ?>
                            <?php if (!empty($roleDetail->children)) : ?>
                                <ul style="list-style-type: none">
                                    <?php foreach ($roleDetail->children as $child) : ?>
                                        <li>
                                            <?=
                                                // 権限詳細 (子)
                                                $this->Form->customControl('role_details._ids', [
                                                    'type' => 'select',
                                                    'multiple' => 'checkbox',
                                                    'label' => false,
                                                    'options' => [$child->id => $child->name],
                                                    'default' => array_column((array)$role->role_details, 'id'),
                                                    'readonly' => true,
                                                    'hiddenField' => false,
                                                ]) ?>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </td>
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