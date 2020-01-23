<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var \App\Model\Entity\RoleDetails $role_details
 */
?>
<section>
    <h2 class="mb-2"><?= __('Roles') ?></h2>
    <?= $this->Form->create($role) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md required"><?= __('Roles.name') ?></dt>
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
            <dt class="col-md"><?= __('Roles.description') ?></dt>
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
            <dt class="col-md"><?= __('Roles.role_details') ?></dt>
            <dd class="col-md">
                <?= $this->Form->hidden('role_details._ids') ?>
                <ul style="list-style-type: none;padding-left: 0px;">
                    <?php foreach ($roleDetails as $roleDetail) : ?>
                        <li>
                            <?=
                                // 権限詳細 (親)
                                $this->Form->customControl('role_details._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => [$roleDetail->id => $roleDetail->name],
                                    'label' => false,
                                    'hiddenField' => false,
                                ])
                            ?>
                            <?php if (!empty($roleDetail->children)) : ?>
                                <ul style="list-style-type: none">
                                    <?php foreach ($roleDetail->children as $child) : ?>
                                        <li>
                                            <?=
                                                // 権限詳細 (子)
                                                $this->Form->customControl('role_details._ids', [
                                                    'type' => 'select',
                                                    'multiple' => 'checkbox',
                                                    'label' => false,
                                                    'options' => [$child->id => $child->name],
                                                    'hiddenField' => false,
                                                ])
                                            ?>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </dd>
        </dl>
    </div>
    <div class="button-wrap py-4">
        <?= $this->Form->customButton(__('BTN-CANCEL'), [
            // キャンセル
            'data-action' => ['controller' => 'Roles', 'action' => 'index'],
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