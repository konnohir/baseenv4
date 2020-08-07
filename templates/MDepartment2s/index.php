<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $mDepartment2s
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment2s') ?></h2>
    <div class="btn-group mb-2">
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
                    <th><?= $this->Paginator->sort('code', ['label' => __('MDepartment2s.code')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('name', ['label' => __('MDepartment2s.name')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mDepartment2s as $mDepartment2): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($mDepartment2->id, $mDepartment2->_lock) ?></td>
                    <td><?= $this->Html->link($mDepartment2->code, ['action' => 'view', $mDepartment2->id]) ?></td>
                    <td><?= $mDepartment2->name ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
