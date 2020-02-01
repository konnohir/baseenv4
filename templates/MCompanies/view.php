<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MCompany $mCompany
 * @var array $tagList
 */
?>
<section>
    <h2 class="mb-2"><?= __('MCompanies') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('コード') ?></dt>
            <dd class="col-md"><?= h($mCompany->code) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('名称') ?></dt>
            <dd class="col-md"><?= h($mCompany->name) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('電話番号') ?></dt>
            <dd class="col-md"><?= h($mCompany->tel_no) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('従業員数') ?></dt>
            <dd class="col-md"><?= h($mCompany->staff) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('創業年月') ?></dt>
            <dd class="col-md"><?= h($mCompany->established_date) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('備考') ?></dt>
            <dd class="col-md"><?= h($mCompany->note) ?></dd>
        </dl>
    </div>
    <table class="table mb-2 table-border border">
        <tr>
            <th><?= __('Tag') ?></th>
            <td>
                <?=
                $this->Form->customControl('tags', [
                    'label' => false,
                    'options' => $tagList,
                    'multiple' => 'checkbox',
                    'value' => array_column($mCompany->tags, 'id'),
                    'readonly' => true,
                ])
                ?>
            </td>
        </tr>
    </table>
    <div class="btn-group my-2">
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => ['controller' => 'MCompanies', 'action' => 'index'],
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => ['controller' => 'MCompanies', 'action' => 'edit'],
            'data-id' => $mCompany->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('増員'), [
            // 増員
            'data-action' => ['controller' => 'MCompanies', 'action' => 'add-staff'],
            'data-id' => $mCompany->id,
            'data-lock' => $mCompany->_lock,
            'class' => 'btn-outline-success btn-add-staff'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['controller' => 'MCompanies', 'action' => 'delete'],
            'data-id' => $mCompany->id,
            'data-lock' => $mCompany->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>