<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VOrganization $vOrganization
 */
?>
<section>
    <h2 class="mb-2"><?= __('Organizations') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('VOrganizations.m_department1_code') ?></dt>
            <dd class="col-md"><?= h($vOrganization->m_department1_code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('VOrganizations.m_department1_name') ?></dt>
            <dd class="col-md"><?= h($vOrganization->m_department1_name) ?></dd>
        </dl>
        <?php if ($vOrganization->m_department2_id !== null) : ?>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department2_code') ?></dt>
                <dd class="col-md"><?= h($vOrganization->m_department2_code) ?></dd>
            </dl>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department2_name') ?></dt>
                <dd class="col-md"><?= h($vOrganization->m_department2_name) ?></dd>
            </dl>
        <?php endif ?>
        <?php if ($vOrganization->m_department3_id !== null) : ?>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department3_code') ?></dt>
                <dd class="col-md"><?= h($vOrganization->m_department3_code) ?></dd>
            </dl>
            <dl class="row">
                <dt class="col-md"><?= __('VOrganizations.m_department3_name') ?></dt>
                <dd class="col-md"><?= h($vOrganization->m_department3_name) ?></dd>
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
            'data-id' => $vOrganization->getId(),
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['action' => 'delete'],
            'data-id' => $vOrganization->getId(),
            'data-lock' => $vOrganization->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>