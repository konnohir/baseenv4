<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail $parentRoleDetailList
 */
?>
<section>
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <?= $this->Form->create($roleDetail) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md"><?= __('RoleDetails.parent_id') ?></dt>
            <dd class="col-md">
                <?php
                    // 親権限
                    echo $this->Form->customControl('parent_id', [
                        'type' => 'select',
                        'options' => $parentRoleDetailList,
                        'empty' => true,
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required"><?= __('RoleDetails.name') ?></dt>
            <dd class="col-md">
                <?php
                    // 名称
                    echo $this->Form->customControl('name', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('RoleDetails.description') ?></dt>
            <dd class="col-md">
                <?php
                    // 説明文
                    echo $this->Form->customControl('description', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('RoleDetails.acos') ?></dt>
            <dd class="col-md">
                <?= $this->Form->hidden('acos._ids') ?>
                <ul style="list-style-type: none; padding-left: 0px">
                    <?php foreach ($acos as $controller) : ?>
                        <li>
                            <?=
                                // 権限詳細 (親)
                                $this->Form->customControl('acos._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => [$controller->id => $controller->alias],
                                    'label' => false,
                                    'hiddenField' => false,
                                    'data-type' => 'controller',
                                ]) ?>
                                <ul style="list-style-type: none">
                                    <?php foreach ($controller->children as $action) : ?>
                                        <li>
                                            <?=
                                                // 権限詳細 (子)
                                                $this->Form->customControl('acos._ids', [
                                                    'type' => 'select',
                                                    'multiple' => 'checkbox',
                                                    'label' => false,
                                                    'options' => [$action->id => $action->alias],
                                                    'hiddenField' => false,
                                                ]) ?>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                        </li>
                    <?php endforeach ?>
                </ul>
            </dd>
        </dl>
    </div>
    <div class="form-group text-center py-4">
        <?= $this->Form->customButton(__('BTN-CANCEL'), [
            // キャンセル
            'data-action' => '/role-details',
            'class' => 'btn-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-SAVE'), [
            // 保存
            'type' => 'submit',
            'class' => 'btn-primary'
        ]) ?>
    </div>
    <?= $this->Form->end() ?>
</section>