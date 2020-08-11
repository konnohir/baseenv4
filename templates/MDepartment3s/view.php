<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment3 $mDepartment3
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment3s') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment3s.code') ?></dt>
            <dd class="col-md"><?= h($mDepartment3->code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment3s.name') ?></dt>
            <dd class="col-md"><?= h($mDepartment3->name) ?></dd>
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
            'data-id' => $mDepartment3->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['action' => 'delete'],
            'data-id' => $mDepartment3->id,
            'data-lock' => $mDepartment3->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>