<?php

/**
 * @var \App\View\AppView $this
 * @var int|null $editType 編集種別 (1: 本部、2: 部店、3: 課、null: 新規)
 * @var \App\Model\Entity\MDepartment1 $mDepartment1
 * @var \App\Model\Entity\MDepartment2 $mDepartment2
 * @var \App\Model\Entity\MDepartment3 $mDepartment3
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
        <?= $this->Form->create($mDepartment1, ['type' => 'post']) ?>
        <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
        // 組織編集区分 (1: 本部、2: 部店、3: 課)
        echo $this->Form->hidden('edit_type', ['value' => 1]);
        ?>
        <div class="dl-wrap dl-wrap-form mb-4">
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment1s.code', __('VOrganizations.m_department1_code')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部コード
                    echo $this->Form->customControl('MDepartment1s.code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment1s.name', __('VOrganizations.m_department1_name')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部名
                    echo $this->Form->customControl('MDepartment1s.name', [
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
        <?= $this->Form->create($mDepartment2, ['type' => 'post']) ?>
        <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
        // 組織編集区分 (1: 本部、2: 部店、3: 課)
        echo $this->Form->hidden('edit_type', ['value' => 2]);
        ?>
        <div class="dl-wrap dl-wrap-form mb-4">
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment2s.m_department1_id', __('VOrganizations.m_department1_id')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部ID
                    echo $this->Form->customControl('MDepartment2s.m_department1_id', [
                        'type' => 'select',
                        'options' => $mDepartment1List,
                        'empty' => ' ',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment2s.code', __('VOrganizations.m_department2_code')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店コード
                    echo $this->Form->customControl('MDepartment2s.code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment2s.name', __('VOrganizations.m_department2_name')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店名
                    echo $this->Form->customControl('MDepartment2s.name', [
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
        <?= $this->Form->create($mDepartment3, ['type' => 'post']) ?>
        <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
        // 組織編集区分 (1: 本部、2: 部店、3: 課)
        echo $this->Form->hidden('edit_type', ['value' => 3]);
        ?>
        <div class="dl-wrap dl-wrap-form mb-4">
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.m_department1_id', __('VOrganizations.m_department1_id')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 本部ID
                    echo $this->Form->customControl('MDepartment3s.m_department1_id', [
                        'type' => 'select',
                        'options' => $mDepartment1List,
                        'empty' => ' ',
                        'default' => $mDepartment3->m_department2->m_department1_id ?? null,
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.m_department2_id', __('VOrganizations.m_department2_id')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店ID
                    echo $this->Form->customControl('MDepartment3s.m_department2_id', [
                        'type' => 'select',
                        'options' => $mDepartment2List,
                        'empty' => ' ',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.code', __('VOrganizations.m_department3_code')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店コード
                    echo $this->Form->customControl('MDepartment3s.code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                    ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="col-md required">
                    <?= $this->Form->label('MDepartment3s.name', __('VOrganizations.m_department3_name')) ?>
                </dt>
                <dd class="col-md">
                    <?php
                    // 部店名
                    echo $this->Form->customControl('MDepartment3s.name', [
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