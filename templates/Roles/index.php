<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role[]|\Cake\Collection\CollectionInterface $roles
 */
?>
<section>
    <h2><?= __('Roles') ?></h2>
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
            <?= $this->Paginator->first() ?>
            <?= $this->Paginator->numbers(['modulus' => 4]) ?>
            <?= $this->Paginator->last() ?>
        </ul>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->checkboxAll() ?></th>
                    <th><?= $this->Paginator->sort('name', ['label' => __('Roles.name')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('description', ['label' => __('Roles.description')]) ?>
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
</section>
