<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MOrganization[] $mOrganizations
 */
?>
<section>
    <h2 class="mb-2"><?= __('Organizations') ?></h2>
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
                    <th>
                        <?php
                        // 全選択チェックボックス
                        echo $this->Paginator->checkboxAll();
                        ?>
                    </th>
                    <th>
                        <?php
                        // 本部名
                        echo $this->Paginator->sort('MDepartment1s.name', [
                            'label' => __('MDepartment1s.name')
                        ]);
                        ?>
                    </th>
                    <th>
                        <?php
                        // 部店名
                        echo $this->Paginator->sort('MDepartment2s.name', [
                            'label' => __('MDepartment2s.name')
                        ]);
                        ?>
                    </th>
                    <th class="w-100">
                        <?php
                        // 課名
                        echo $this->Paginator->sort('MDepartment3s.name', [
                            'label' => __('MDepartment3s.name')
                        ]);
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mOrganizations as $mOrganization) : ?>
                    <tr>
                        <td>
                            <?php
                            // 行選択チェックボックス
                            echo $this->Paginator->checkbox($mOrganization->id, $mOrganization->_lock);
                            ?>
                        </td>
                        <td>
                            <?php
                            // 本部名
                            echo $this->Html->link($mOrganization->m_department1->name, [
                                'action' => 'view',
                                $mOrganization->id,
                            ]);
                            ?>
                        </td>
                        <td>
                            <?php
                            // 部店名
                            if ($mOrganization->m_department2 !== null) {
                                echo $this->Html->link($mOrganization->m_department2->name, [
                                    'action' => 'view',
                                    $mOrganization->id,
                                ]);
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            // 課名
                            if ($mOrganization->m_department3 != null) {
                                echo $this->Html->link($mOrganization->m_department3->name, [
                                    'action' => 'view',
                                    $mOrganization->id,
                                ]);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>