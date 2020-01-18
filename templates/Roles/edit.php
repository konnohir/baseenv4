<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var \App\Model\Entity\RoleDetails $role_details
 */
?>
<section>
    <h2 class="mb-2"><?= __('Roles') ?></h2>
    <div class="form">
        <?= $this->Form->create($role) ?>
        <?= $this->Form->hidden('_lock') ?>

        <?=
            // 名称
            $this->Form->customControl('name', [
                'type' => 'text',
                'label' => __('名称'),
            ]),

            // 説明
            $this->Form->customControl('description', [
                'type' => 'text',
                'label' => __('説明'),
            ])
        ?>

        <label><?= __('権限') ?></label>
        <ul class="py-4" style="list-style-type: none; border:solid 1px lightgray;border-radius: 6px">
            <?= $this->Form->hidden('role_details._ids') ?>
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
                        ]) ?>
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
                                        ]) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>

        <div class="form-group text-center py-4">
            <?= $this->Form->customButton(__('BTN-CANCEL'), [
                // キャンセル
                'data-action' => '/roles',
                'class' => 'btn-secondary btn-cancel'
            ]) ?>
            <?= $this->Form->customButton(__('BTN-SAVE'), [
                // 保存
                'type' => 'submit',
                'class' => 'btn-primary'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</section>