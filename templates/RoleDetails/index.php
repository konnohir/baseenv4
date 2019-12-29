<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail[]|\Cake\Collection\CollectionInterface $roleDetails
 */
?>
<div class="w-100 mx-auto">
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-ADD'), [
            // 新規作成
            'data-action' => '/role-details/add',
            'class' => 'btn-outline-primary btn-add'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/role-details/edit',
            'class' => 'btn-outline-primary btn-edit'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/role-details/delete',
            'class' => 'btn-outline-danger btn-delete'
        ])
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
                    <th><?= $this->Paginator->sort('name', ['label' => __('名称')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('description', ['label' => __('説明')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roleDetails as $roleDetail): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($roleDetail->id, $roleDetail->_lock) ?></td>
                    <td><?= $this->Html->link($roleDetail->name, ['action' => 'view', $roleDetail->id]) ?></td>
                    <td>
                        <?= $roleDetail->description ?>
                        <p class="mb-0">
                        <?php foreach($roleDetail->children as $child): ?>
                            <?= $this->Html->link($child->name, ['action' => 'view', $child->id,'class' => 'mr-2']) ?>
                        <?php endforeach ?>
                        </p>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
