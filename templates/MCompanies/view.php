<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MCompany $mCompany
 * @var array $tagList
 */
?>
<section>
    <h2 class="mb-2"><?= __('MCompanies') ?></h2>
    <table class="table mb-2 table-border border">
        <tr>
            <th><?= __('コード') ?></th>
            <td><?= h($mCompany->code) ?></td>
        </tr>
        <tr>
            <th><?= __('名称') ?></th>
            <td><?= h($mCompany->name) ?></td>
        </tr>
        <tr>
            <th><?= __('電話番号') ?></th>
            <td><?= h($mCompany->tel_no) ?></td>
        </tr>
        <tr>
            <th><?= __('従業員数') ?></th>
            <td><?= $mCompany->staff ?></td>
        </tr>
        <tr>
            <th><?= __('創業年月') ?></th>
            <td><?= h($mCompany->established_date) ?></td>
        </tr>
        <tr>
            <th><?= __('備考') ?></th>
            <td><?= h($mCompany->note) ?></td>
        </tr>
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
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => '/m-companies',
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/m-companies/edit',
            'data-id' => $mCompany->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('増員'), [
            // 増員
            'data-action' => '/m-companies/add-staff',
            'data-id' => $mCompany->id,
            'data-lock' => $mCompany->_lock,
            'class' => 'btn-outline-success btn-add-staff'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/m-companies/delete',
            'data-id' => $mCompany->id,
            'data-lock' => $mCompany->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>