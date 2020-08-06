<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $mDepartment1s
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment1s') ?></h2>
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
                    <th><?= $this->Paginator->sort('code', ['label' => __('MDepartment1s.code')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('name', ['label' => __('MDepartment1s.name')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mDepartment1s as $mDepartment1): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($mDepartment1->id, $mDepartment1->_lock) ?></td>
                    <td><?= $this->Html->link($mDepartment1->code, ['action' => 'view', $mDepartment1->id]) ?></td>
                    <td><?= $mDepartment1->name ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
