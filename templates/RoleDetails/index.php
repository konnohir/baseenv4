<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail[]|\Cake\Collection\CollectionInterface $roleDetails
 */
?>
<section>
    <h2><?= __('RoleDetails') ?></h2>
    <div class="btn-group">
        <?php
        // 新規作成
        echo $this->Form->customButton(__('BTN-ADD'), [
            'data-action' => ['action' => 'add'],
            'class' => 'btn-outline-primary btn-add'
        ]);
        // 編集
        echo $this->Form->customButton(__('BTN-EDIT'), [
            'data-action' => ['action' => 'edit'],
            'class' => 'btn-outline-primary btn-edit'
        ]);
        // 削除
        echo $this->Form->customButton(__('BTN-DELETE'), [
            'data-action' => ['action' => 'delete'],
            'class' => 'btn-outline-danger btn-delete'
        ]);
        ?>
    </div>
    <div class="pagination-wrap">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->counter() ?>
        </ul>
    </div>
    <div class="table-wrap">
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
</section>