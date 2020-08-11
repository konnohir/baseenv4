<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment3[]|\Cake\Collection\CollectionInterface $mDepartment3s
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment3s') ?></h2>
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
                    <th><?= $this->Paginator->sort('code', ['label' => __('MDepartment3s.code')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('name', ['label' => __('MDepartment3s.name')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mDepartment3s as $MDepartment3): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($MDepartment3->id, $MDepartment3->_lock) ?></td>
                    <td><?= $this->Html->link($MDepartment3->code, ['action' => 'view', $MDepartment3->id]) ?></td>
                    <td><?= $MDepartment3->name ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
