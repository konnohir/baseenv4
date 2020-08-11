<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment1 $mDepartment1
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment1s') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment1s.code') ?></dt>
            <dd class="col-md"><?= h($mDepartment1->code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment1s.name') ?></dt>
            <dd class="col-md"><?= h($mDepartment1->name) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment1s.MDepartment2s') ?></dt>
            <dd class="col-md">
                <ul class="list-unstyled">
                    <?php foreach ($mDepartment1->m_department2s as $mDepartment2) : ?>
                        <li><?= $this->Html->link($mDepartment2->name, ['controller' => 'MDepartment2s','action' => 'view', $mDepartment2->id]) ?></li>
                    <?php endforeach ?>
                </ul>
            </dd>
        </dl>
    </div>
    <div class="btn-group my-2">
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => ['action' => 'index'],
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => ['action' => 'edit'],
            'data-id' => $mDepartment1->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['action' => 'delete'],
            'data-id' => $mDepartment1->id,
            'data-lock' => $mDepartment1->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>