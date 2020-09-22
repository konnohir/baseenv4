<?php

/**
 * @var \App\View\AppView $this
 * @var int|null $editType 編集種別 (1: 本部、2: 部店、3: 課、null: 新規)
 * @var \App\Model\Entity\MOrganization $mOrganization
 * @var array $mDepartment1List 本部リスト
 * @var array $mDepartment2List 部店リスト
 */
$this->Html->script('organizations/organizations', ['block' => true]);

?>
<section>
    <h2 class="mb-2"><?= __('Organizations') ?></h2>

    <?php
    // 編集種別選択
    echo $this->Form->customControl('edit_type', [
        'type' => 'radio',
        'options' => [
            '1' => '本部',
            '2' => '部店',
            '3' => '課',
        ],
        'default' => $editType,
        'disabled' => isset($editType),
        'hiddenField' => false,
        'label' => false,
    ]);
    ?>

    <div id="EditForm0">
        <?= $this->Form->create(null, ['type' => 'post']) ?>
        <div class="button-wrap py-4">
            <?php
            // キャンセル
            echo $this->Form->customButton(__('BTN-CANCEL'), [
                'data-action' => ['action' => 'index'],
                'class' => 'btn-secondary btn-cancel',
            ]);
            // 保存
            echo $this->Form->customButton(__('BTN-SAVE'), [
                'class' => 'btn-primary btn-submit',
            ]);
            ?>
        </div>
        <?= $this->Form->end() ?>
    </div>

    <div id="EditForm1" class="d-none">
        <?= $this->Form->create($mOrganization, ['type' => 'post']) ?>
        <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
        // 組織編集区分 (1: 本部、2: 部店、3: 課)
        echo $this->Form->hidden('edit_type', ['value' => 1]);
        ?>
        <div class="dl-wrap dl-wrap-form mb-4">
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment1s.code', __('MDepartment1s.code')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部コード
                    echo $this->Form->customControl('m_department1.code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment1s.name', __('MDepartment1s.name')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部名
                    echo $this->Form->customControl('m_department1.name', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
        </div>
        <div class="button-wrap py-4">
            <?php
            // キャンセル
            echo $this->Form->customButton(__('BTN-CANCEL'), [
                'data-action' => ['action' => 'index'],
                'class' => 'btn-secondary btn-cancel',
            ]);
            // 保存
            echo $this->Form->customButton(__('BTN-SAVE'), [
                'class' => 'btn-primary btn-submit',
            ]);
            ?>
        </div>
        <?= $this->Form->end() ?>
    </div>

    <div id="EditForm2" class="d-none">
        <?= $this->Form->create($mOrganization, ['type' => 'post']) ?>
        <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
        // 組織編集区分 (1: 本部、2: 部店、3: 課)
        echo $this->Form->hidden('edit_type', ['value' => 2]);
        ?>
        <div class="dl-wrap dl-wrap-form mb-4">
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment2s.m_department1_id', __('MOrganizations.m_department1_id')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部ID
                    echo $this->Form->customControl('MOrganizations.m_department1_id', [
                        'type' => 'select',
                        'options' => $mDepartment1List,
                        'empty' => ' ',
                        'disabled' => !$mOrganization->isNew(),
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment2s.code', __('MDepartment2s.code')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店コード
                    echo $this->Form->customControl('m_department2.code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment2s.name', __('MDepartment2s.name')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店名
                    echo $this->Form->customControl('m_department2.name', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
        </div>
        <div class="button-wrap py-4">
            <?php
            // キャンセル
            echo $this->Form->customButton(__('BTN-CANCEL'), [
                'data-action' => ['action' => 'index'],
                'class' => 'btn-secondary btn-cancel',
            ]);
            // 保存
            echo $this->Form->customButton(__('BTN-SAVE'), [
                'class' => 'btn-primary btn-submit',
            ]);
            ?>
        </div>
        <?= $this->Form->end() ?>
    </div>

    <div id="EditForm3" class="d-none">
        <?= $this->Form->create($mOrganization, ['type' => 'post']) ?>
        <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
        // 組織編集区分 (1: 本部、2: 部店、3: 課)
        echo $this->Form->hidden('edit_type', ['value' => 3]);
        ?>
        <div class="dl-wrap dl-wrap-form mb-4">
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.m_department1_id', __('MOrganizations.m_department1_id')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部ID
                    echo $this->Form->customControl('MOrganizations.m_department1_id', [
                        'type' => 'select',
                        'options' => $mDepartment1List,
                        'empty' => ' ',
                        'default' => $mOrganization->m_department2->m_department1_id ?? null,
                        'disabled' => !$mOrganization->isNew(),
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MOrganizations.m_department2_id', __('MOrganizations.m_department2_id')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店ID
                    echo $this->Form->customControl('MOrganizations.m_department2_id', [
                        'type' => 'select',
                        'options' => $mDepartment2List,
                        'empty' => ' ',
                        'disabled' => !$mOrganization->isNew(),
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.code', __('MDepartment3s.code')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店コード
                    echo $this->Form->customControl('m_department3.code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.name', __('MDepartment3s.name')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店名
                    echo $this->Form->customControl('m_department3.name', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
        </div>
        <div class="button-wrap py-4">
            <?php
            // キャンセル
            echo $this->Form->customButton(__('BTN-CANCEL'), [
                'data-action' => ['action' => 'index'],
                'class' => 'btn-secondary btn-cancel',
            ]);
            // 保存
            echo $this->Form->customButton(__('BTN-SAVE'), [
                'class' => 'btn-primary btn-submit',
            ]);
            ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</section>