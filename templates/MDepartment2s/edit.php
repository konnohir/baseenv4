<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment2 $mDepartment2
 * @var \App\Model\Entity\MDepartment1[] $mDepartment1s
 * @var array $tagList
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment2s') ?></h2>
    <?= $this->Form->create($mDepartment2, ['type' => 'post']) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('email', __('MDepartment2s.m_department1_id')) ?>
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
                <?= $this->Form->label('email', __('MDepartment2s.code')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 部店コード
                    echo $this->Form->customControl('code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('role_id', __('MDepartment2s.name')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 部店名
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