<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment2 $mDepartment2
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment2s') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment2s.code') ?></dt>
            <dd class="col-md"><?= h($mDepartment2->code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment2s.name') ?></dt>
            <dd class="col-md"><?= h($mDepartment2->name) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment2s.MDepartment3s') ?></dt>
            <dd class="col-md">
                <ul class="list-unstyled">
                    <?php foreach ($mDepartment2->m_department3s as $mDepartment3) : ?>
                        <li><?= $this->Html->link($mDepartment3->name, ['controller' => 'MDepartment3s','action' => 'view', $mDepartment3->id]) ?></li>
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
            'data-id' => $mDepartment2->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['action' => 'delete'],
            'data-id' => $mDepartment2->id,
            'data-lock' => $mDepartment2->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>