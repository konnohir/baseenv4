<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var \App\Model\Entity\RoleDetail $roleDetails
 */
$roleIds = array_column((array) $role->role_details, 'id', 'id');
?>
<section>
    <h2><?= __('Roles') ?></h2>
    <div class="dl-wrap">
        <dl class="row">
            <?php // 名称 
            ?>
            <dt class="col-md"><?= __('Roles.name') ?></dt>
            <dd class="col-md"><?= h($role->name) ?></dd>
        </dl>
        <dl class="row">
            <?php // 説明 ?>
            <dt class="col-md"><?= __('Roles.description') ?></dt>
            <dd class="col-md"><?= h($role->description) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('Roles.role_details') ?></dt>
            <dd class="col-md">
                <ul class="list-unstyled">
                    <?php foreach ($roleDetails as $roleDetail) : ?>
                        <li>
                            <?=
                                // 権限詳細 (親)
                                $this->Form->customControl('role_details._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => [$roleDetail->id => $roleDetail->name],
                                    'default' => $roleIds,
                                    'readonly' => true,
                                    'label' => false,
                                    'hiddenField' => false,
                                ])
                            ?>
                            <?php if (!empty($roleDetail->children)) : ?>
                                <ul class="list-unstyled ml-4">
                                    <?php foreach ($roleDetail->children as $child) : ?>
                                        <li>
                                            <?=
                                                // 権限詳細 (子)
                                                $this->Form->customControl('role_details._ids', [
                                                    'type' => 'select',
                                                    'multiple' => 'checkbox',
                                                    'label' => false,
                                                    'options' => [$child->id => $child->name],
                                                    'default' => $roleIds,
                                                    'readonly' => true,
                                                    'hiddenField' => false,
                                                ]) ?>
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
    <div class="btn-group">
        <?php
        // 戻る
        echo $this->Form->customButton(__('BTN-BACK'), [
            'data-action' => ['action' => 'index'],
            'class' => 'btn-outline-secondary btn-cancel'
        ]);
        // 編集
        echo $this->Form->customButton(__('BTN-EDIT'), [
            'data-action' => ['action' => 'edit'],
            'data-id' => $role->id,
            'data-lock' => $role->_lock,
            'class' => 'btn-outline-primary btn-edit'
        ]);
        // 削除
        echo $this->Form->customButton(__('BTN-DELETE'), [
            'data-action' => ['action' => 'delete'],
            'data-id' => $role->id,
            'data-lock' => $role->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]);
        ?>
    </div>
</section>