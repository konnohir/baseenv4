<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail[]|\Cake\Collection\CollectionInterface $roleDetails
 */
?>
<div class="w-100 mx-auto">
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <div class="btn-group mb-2">
        <?php
            // 新規作成
            echo $this->Form->customButton(__('BTN-ADD'), [
                'data-action' => '/role-details/add',
                'class' => 'btn-outline-primary btn-add'
            ]);
            // 編集
            echo $this->Form->customButton(__('BTN-EDIT'), [
                'data-action' => '/role-details/edit',
                'class' => 'btn-outline-primary btn-edit'
            ]);
            // 削除
            echo $this->Form->customButton(__('BTN-DELETE'), [
                'data-action' => '/role-details/delete',
                'class' => 'btn-outline-danger btn-delete'
            ]);
        ?>
    </div>
    <div class="pagination-wrap mb-2">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->counter() ?>
        </ul>
    </div>
    <div class="table-wrap mb-2">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->checkboxAll() ?></th>
                    <th class="w-100">
                        <?= __('権限詳細') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roleDetails as $roleDetail) : ?>
                    <tr>
                        <td rowspan="2"><?= $this->Paginator->checkbox($roleDetail->id, $roleDetail->_lock) ?></td>
                        <td>
                            <?= $this->Html->link($roleDetail->name, ['action' => 'view', $roleDetail->id]) ?>
                            <?php if (!empty($roleDetail->description)) : ?>
                                (<?= $roleDetail->description ?>)
                            <?php endif ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="pl-4">
                            <?php foreach ($roleDetail->children as $child) : ?>
                                <?= $this->Html->link($child->name, ['action' => 'view', $child->id]) ?>
                            <?php endforeach ?>
                            <?php if (empty($roleDetail->children)) : ?>
                                -
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>