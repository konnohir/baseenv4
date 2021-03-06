<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MOrganization $mOrganization
 */
?>
<section>
    <h2><?= __('Organizations') ?></h2>
    <div class="dl-wrap">
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment1s.code') ?></dt>
            <dd class="col-md"><?= h($mOrganization->m_department1->code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('MDepartment1s.name') ?></dt>
            <dd class="col-md"><?= h($mOrganization->m_department1->name) ?></dd>
        </dl>
        <?php if ($mOrganization->m_department2 !== null) : ?>
            <dl class="row">
                <dt class="col-md"><?= __('MDepartment2s.code') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department2->code) ?></dd>
            </dl>
            <dl class="row">
                <dt class="col-md"><?= __('MDepartment2s.name') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department2->name) ?></dd>
            </dl>
        <?php endif ?>
        <?php if ($mOrganization->m_department3 !== null) : ?>
            <dl class="row">
                <dt class="col-md"><?= __('MDepartment3s.code') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department3->code) ?></dd>
            </dl>
            <dl class="row">
                <dt class="col-md"><?= __('MDepartment3s.name') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department3->name) ?></dd>
            </dl>
        <?php endif ?>
    </div>
    <div class="btn-group">
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => ['action' => 'index'],
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => ['action' => 'edit'],
            'data-id' => $mOrganization->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['action' => 'delete'],
            'data-id' => $mOrganization->id,
            'data-lock' => $mOrganization->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>