<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MCompany $mCompany
 * @var array $tagList
 */
?>
<section>
    <h2 class="mb-2"><?= __('MCompanies') ?></h2>
    <div class="form">
        <?= $this->Form->create($mCompany) ?>
        <?= $this->Form->hidden('_lock') ?>
        <?= $this->Form->customControl('code', [
            'type' => 'text',
            'label' => __('コード'),
            'customButton' => [
                'label' => '自動採番',
                'class' => 'btn btn-sm btn-success',
                'onclick' => '$("#code").val(Math.round(Math.random()*900000+100000))',
                'disabled' => !$mCompany->isNew(),
            ],
            'disabled' => !$mCompany->isNew(),
        ]) ?>
        <?= $this->Form->customControl('name', [
            'type' => 'text',
            'label' => __('名称'),
        ]) ?>
        <?= $this->Form->customControl('tel_no', [
            'type' => 'text',
            'label' => __('電話番号'),
        ]) ?>
        <?= $this->Form->customControl('staff', [
            'type' => 'text',
            'label' => __('従業員数'),
        ]) ?>
        <?= $this->Form->customControl('established_date', [
            'type' => 'text',
            'label' => __('創業年月日'),
        ]) ?>
        <?= $this->Form->customControl('note', [
            'type' => 'textarea',
            'label' => __('備考'),
        ]) ?>
        <?= $this->Form->customControl('tags._ids', [
            'label' => __('タグ'),
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $tagList,
        ]) ?>
        <?php foreach((array)$mCompany->notices as $key => $notice): ?>
            <?= $this->Form->hidden('notices.'. $key .'.id') ?>
            <?= $this->Form->hidden('notices.'. $key .'.category_id', ['default' => 1]) ?>
            <?= $this->Form->customControl('notices.'. $key .'.message', [
                'label' => __('通知'.$notice->id),
            ]) ?>
        <?php endforeach ?>
        <div class="d-none" id="noticeAddForm">
            <?= $this->Form->hidden('notices.'. 999 .'.category_id', ['default' => 1, 'disabled' => true]) ?>
            <?= $this->Form->customControl('notices.'. 999 .'.message', [
                'label' => __('通知+'),
                'value' => '',
                'disabled' => true,
            ]) ?>
        </div>
        <?= $this->Form->customButton(__('追加'), ['class' => 'btn-success', 'onclick' => '$("#noticeAddForm").removeClass("d-none");$("#noticeAddForm input").prop("disabled", false);$(this).addClass("d-none");']) ?>

        <div class="button-wrap">
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