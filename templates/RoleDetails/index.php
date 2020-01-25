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
            'data-action' => ['controller' => 'RoleDetails', 'action' => 'add'],
            'class' => 'btn-outline-primary btn-add'
        ]);
        // 編集
        echo $this->Form->customButton(__('BTN-EDIT'), [
            'data-action' => ['controller' => 'RoleDetails', 'action' => 'edit'],
            'class' => 'btn-outline-primary btn-edit'
        ]);
        // 削除
        echo $this->Form->customButton(__('BTN-DELETE'), [
            'data-action' => ['controller' => 'RoleDetails', 'action' => 'delete'],
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
                    <th><?= __('RoleDetails.name') ?></th>
                    <th class="w-100">
                        <?= __('RoleDetails.description') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roleDetails as $roleDetail) : ?>
                    <tr>
                        <td rowspan="1"><?= $this->Paginator->checkbox($roleDetail->id, $roleDetail->_lock) ?></td>
                        <td>
                            <?= $this->Html->link($roleDetail->name, ['action' => 'view', $roleDetail->id]) ?>
                        </td>
                        <td>
                            <?= $roleDetail->description ?>
                        </td>
                    </tr>
                    <?php foreach ($roleDetail->children as $child) : ?>
                        <tr>
                            <td rowspan="1"><?= $this->Paginator->checkbox($child->id, $child->_lock) ?></td>
                            <td class="pr-2">
                                └ <?= $this->Html->link($child->name, ['action' => 'view', $child->id]) ?>
                            </td>
                            <td>
                                <?= $child->description ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>