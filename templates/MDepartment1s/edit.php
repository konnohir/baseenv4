<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MDepartment1 $mDepartment1
 */
?>
<section>
    <h2 class="mb-2"><?= __('MDepartment1s') ?></h2>
    <?= $this->Form->create($mDepartment1, ['type' => 'post']) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('code', __('MDepartment1s.code')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 本部コード
                    echo $this->Form->customControl('code', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('name', __('MDepartment1s.name')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 本部名
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