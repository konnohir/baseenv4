<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section>
    <h2><?= __($this->getName()) ?></h2>
    <p class="error">
        <?= __('E-NOT-FOUND', 'データ') ?>
    </p>
    <div class="button-wrap py-4">
        <?= $this->Form->customButton(__('BTN-BACK-TO-INDX'), [
            // 一覧画面へ戻る
            'data-action' => ['action' => 'index'],
            'class' => 'btn-secondary btn-back-to-index'
        ]) ?>
    </div>
</section>