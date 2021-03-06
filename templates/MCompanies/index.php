<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MCompany[]|\Cake\Collection\CollectionInterface $mCompanies
 */
?>
<section>
    <h2><?= __('MCompanies') ?></h2>
    <div class="row">
    <div class="col-lg-4">
    <div class="card mb-2">
        <div class="card-body py-1">
            <?= $this->Form->create() ?>
            <?= $this->Form->customControl('filter.id', ['label' => 'ID']) ?>
            <?= $this->Form->customControl('filter.code', ['label' => 'コード']) ?>
            <?= $this->Form->customControl('filter.name', ['label' => '名称']) ?>
            <?= $this->Form->customControl('filter.established_date', ['label' => '創業年月日']) ?>
            <?= $this->Form->customControl('filter.established_date_from', ['label' => '創業年月日(From)']) ?>
            <?= $this->Form->customControl('filter.established_date_to', ['label' => '創業年月日(To)']) ?>
            <?= $this->Form->customControl('filter.staff', [
                'label' => '従業員数',
                'type' => 'radio',
                'default' => '',
                'empty' => 'すべて',
                'options' => [
                    '0-20' => '～20',
                    '21-50' => '21～50',
                    '51-100' => '51～100',
                    '101-' => '101～',
                ]
            ]) ?>
            <?= $this->Form->customControl('filter.staff2', [
                'label' => '従業員数',
                'type' => 'select',
                'multiple' => 'checkbox',
                'default' => '',
                'empty' => 'すべて',
                'options' => [
                    '1' => '1',
                    '15' => '15',
                    '25' => '25',
                ]
            ]) ?>
            <?= $this->Form->customButton(__('BTN-CLEAR'), ['data-action' => ['action' => 'index'], 'class' => 'btn-outline-secondary btn-clear']) ?>
            <?= $this->Form->customButton(__('BTN-SEARCH'), ['type' => 'submit', 'class' => 'btn-outline-info btn-search']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
    </div>
    <div class="col-lg-8">

    <div class="btn-group">
        <?= $this->Form->customButton(__('BTN-ADD'), [
            // 新規作成
            'data-action' => ['action' => 'add'],
            'class' => 'btn-outline-primary btn-add'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => ['action' => 'edit'],
            'class' => 'btn-outline-primary btn-edit'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-CSV'), [
            // CSV出力
            'data-action' => ['action' => 'csv'],
            'class' => 'btn-outline-success btn-csv'
        ])
        ?>
        <?= $this->Form->customButton(__('増員'), [
            // 増員
            'data-action' => ['action' => 'add-staff'],
            'class' => 'btn-outline-success btn-jump-api'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => ['action' => 'delete'],
            'class' => 'btn-outline-danger btn-delete'
        ])
        ?>
    </div>
    <div class="pagination-wrap">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->counter() ?>
            <?= $this->Paginator->first() ?>
            <?= $this->Paginator->numbers(['modulus' => 4]) ?>
            <?= $this->Paginator->last() ?>
        </ul>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->checkboxAll() ?></th>
                    <th><?= $this->Paginator->sort('code', ['label' => __('コード')]) ?></th>
                    <th><?= $this->Paginator->sort('name', ['label' => __('名称')]) ?></th>
                    <th><?= $this->Paginator->sort('tel_no', ['label' => __('電話番号')]) ?></th>
                    <th><?= $this->Paginator->sort('staff', ['label' => __('従業員数')]) ?></th>
                    <th><?= $this->Paginator->sort('established_date', ['label' => __('創業年月日')]) ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('note', ['label' => __('備考')]) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mCompanies as $mCompany): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($mCompany->id, $mCompany->_lock) ?></td>
                    <td><?= $this->Html->link($mCompany->code, ['action' => 'view', $mCompany->id]) ?></td>
                    <td><?= h($mCompany->name) ?></td>
                    <td><?= h($mCompany->tel_no) ?></td>
                    <td><?= h($mCompany->staff) ?></td>
                    <td><?= h($mCompany->established_date) ?></td>
                    <td><?= h($mCompany->note) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    </div>
    </div>
</section>
