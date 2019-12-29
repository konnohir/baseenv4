<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role[]|\Cake\Collection\CollectionInterface $roles
 */
?>
<div class="w-100 mx-auto">
    <h2 class="mb-2"><?= __('Roles') ?></h2>
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-ADD'), [
            // 新規作成
            'data-action' => '/roles/add',
            'class' => 'btn-outline-primary btn-add'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/roles/edit',
            'class' => 'btn-outline-primary btn-edit'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/roles/delete',
            'class' => 'btn-outline-danger btn-delete'
        ])
        ?>
    </div>
    <div class="pagination-wrap mb-2">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->counter() ?>
            <?= $this->Paginator->first() ?>
            <?= $this->Paginator->numbers(['modulus' => 4]) ?>
            <?= $this->Paginator->last() ?>
        </ul>
    </div>
    <div class="table-wrap mb-2">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->checkboxAll() ?></th>
                    <th><?= $this->Paginator->sort('name', ['label' => __('名称')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('description', ['label' => __('説明')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($role->id, $role->_lock) ?></td>
                    <td><?= $this->Html->link($role->name, ['action' => 'view', $role->id]) ?></td>
                    <td><?= $role->description ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
