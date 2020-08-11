<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment3 $mDepartment3
 * @var array $mDepartment1s
 * @var array $mDepartment2s
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment3s') ?></h2>
    <?= $this->Form->create($mDepartment3, ['type' => 'post']) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('m_department1_id', __('MDepartment3s.m_department1_id')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 本部ID
                    echo $this->Form->customControl('m_department1_id', [
                        'type' => 'select',
                        'options' => $mDepartment1s,
                        'empty' => ' ',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('m_department2_id', __('MDepartment3s.m_department2_id')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 部店ID
                    echo $this->Form->customControl('m_department2_id', [
                        'type' => 'select',
                        'options' => $mDepartment2s,
                        'empty' => ' ',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('code', __('MDepartment3s.code')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 課コード
                    echo $this->Form->customControl('code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('name', __('MDepartment3s.name')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 課名
                    echo $this->Form->customControl('name', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
    </div>
    <div class="button-wrap py-4">
        <?= $this->Form->customButton(__('BTN-CANCEL'), [
            // キャンセル
            'data-action' => ['action' => 'index'],
            'class' => 'btn-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-SAVE'), [
            // 保存
            'class' => 'btn-primary btn-submit'
        ]) ?>
    </div>
    <?= $this->Form->end() ?>
</section>