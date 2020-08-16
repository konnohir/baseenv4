<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VOrganization[] $tableRows
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
                        echo $this->Paginator->sort('m_department1_name', [
                            'label' => __('VOrganizations.m_department1_name')
                        ]);
                        ?>
                    </th>
                    <th>
                        <?php
                        // 部店名
                        echo $this->Paginator->sort('m_department2_name', [
                            'label' => __('VOrganizations.m_department2_name')
                        ]);
                        ?>
                    </th>
                    <th class="w-100">
                        <?php
                        // 課名
                        echo $this->Paginator->sort('m_department3_name', [
                            'label' => __('VOrganizations.m_department3_name')
                        ]);
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tableRows as $vOrganization) : ?>
                    <tr>
                        <td>
                            <?php
                            // 行選択チェックボックス
                            echo $this->Paginator->checkbox($vOrganization->getId(), $vOrganization->_lock);
                            ?>
                        </td>
                        <td>
                            <?php
                            // 本部名
                            echo $this->Html->link($vOrganization->m_department1_name, [
                                'action' => 'view',
                                $vOrganization->m_department1_id
                            ]);
                            ?>
                        </td>
                        <td>
                            <?php
                            // 部店名
                            if ($vOrganization->m_department2_id !== null) {
                                echo $this->Html->link($vOrganization->m_department2_name, [
                                    'action' => 'view',
                                    $vOrganization->m_department1_id,
                                    $vOrganization->m_department2_id,
                                ]);
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            // 課名
                            if ($vOrganization->m_department3_id != null) {
                                echo $this->Html->link($vOrganization->m_department3_name, [
                                    'action' => 'view',
                                    $vOrganization->m_department1_id,
                                    $vOrganization->m_department2_id,
                                    $vOrganization->m_department3_id,
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