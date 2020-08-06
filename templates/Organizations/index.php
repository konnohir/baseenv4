<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $organizations
 */
?>
<section>
    <h2 class="mb-2"><?= __('Organizations') ?></h2>
    <div class="btn-group mb-2">
        <?php
            // 新規作成
            echo $this->Form->customButton(__('BTN-ADD'), [
                'data-action' => ['controller' => 'Organizations', 'action' => 'add'],
                'class' => 'btn-outline-primary btn-add'
            ]);
            // 編集
            echo $this->Form->customButton(__('BTN-EDIT'), [
                'data-action' => ['controller' => 'Organizations', 'action' => 'edit'],
                'class' => 'btn-outline-primary btn-edit'
            ]);
            // 削除
            echo $this->Form->customButton(__('BTN-DELETE'), [
                'data-action' => ['controller' => 'Organizations', 'action' => 'delete'],
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
                    <th><?= $this->Paginator->sort('DepartmentLevel1s.name', ['label' => __('Organizations.level1_name')]) ?></th>
                    <th><?= $this->Paginator->sort('DepartmentLevel2s.name', ['label' => __('Organizations.level2_name')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('DepartmentLevel3s.name', ['label' => __('Organizations.level3_name')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($organizations as $organization): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($organization->id, $organization->_lock) ?></td>
                    <td><?= $this->Html->link($organization->department_level1->name, ['controller' => 'level1', 'action' => 'view', $organization->id]) ?></td>
                    <td><?= $this->Html->link($organization->department_level2->name, ['controller' => 'organization2', 'action' => 'view', $organization->id]) ?></td>
                    <td><?= $this->Html->link($organization->department_level3->name, ['controller' => 'organization2', 'action' => 'view', $organization->id]) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
