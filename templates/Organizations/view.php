<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VOrganization $mOrganization
 */
?>
<section>
    <h2 class="mb-2"><?= __('Organizations') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('VOrganizations.m_department1_code') ?></dt>
            <dd class="col-md"><?= h($mOrganization->m_department1->code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('VOrganizations.m_department1_name') ?></dt>
            <dd class="col-md"><?= h($mOrganization->m_department1->name) ?></dd>
        </dl>
        <?php if ($mOrganization->m_department2 !== null) : ?>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department2_code') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department2->code) ?></dd>
            </dl>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department2_name') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department2->name) ?></dd>
            </dl>
        <?php endif ?>
        <?php if ($mOrganization->m_department3 !== null) : ?>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department3_code') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department3->code) ?></dd>
            </dl>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department3_name') ?></dt>
                <dd class="col-md"><?= h($mOrganization->m_department3->name) ?></dd>
            </dl>
        <?php endif ?>
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