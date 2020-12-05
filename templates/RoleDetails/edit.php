<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail $roleDetail
 * @var \App\Model\Entity\RoleDetail[] $parentRoleDetailList
 */
$this->Html->script('role-details/role-details', ['block' => true]);
?>
<section>
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <?= $this->Form->create($roleDetail, ['type' => 'post']) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('parent_id', __('RoleDetails.parent_id')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 親権限詳細
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
            <dt class="col-md required">
                <?= $this->Form->label('name', __('RoleDetails.name')) ?>
            </dt>
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
            <dt class="col-md required">
                <?= $this->Form->label('description', __('RoleDetails.description')) ?>
            </dt>
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
            <dt class="col-md">
                <?= $this->Form->label('acos', __('RoleDetails.acos')) ?>
            </dt>
            <dd class="col-md">
                <?= $this->Form->hidden('acos._ids') ?>
                <ul class="list-unstyled">
                    <?php foreach ($acos as $controller) : ?>
                        <li>
                            <?=
                                // Acos (controller)
                                $this->Form->customControl('acos._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => [$controller->id => $controller->alias],
                                    'label' => false,
                                    'hiddenField' => false,
                                    'data-type' => 'controller',
                                ]) ?>
                                <ul class="list-unstyled ml-4">
                                    <?php foreach ($controller->children as $action) : ?>
                                        <li>
                                            <?=
                                                // Acos (action)
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
</section>