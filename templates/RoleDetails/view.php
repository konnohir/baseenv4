<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail $roleDetail
 */
?>
<section>
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <table class="table mb-2 table-border border">
        <tr>
            <th><?= __('名称') ?></th>
            <td><?= h($roleDetail->name) ?></td>
        </tr>
        <tr>
            <th><?= __('説明') ?></th>
            <td><?= nl2br(h($roleDetail->description)) ?></td>
        </tr>
        <tr>
            <th><?= __('アクション') ?></th>
            <td>
                <ul style="list-style-type: none;padding-left:0">
                    <?php foreach ($acos as $controller) : ?>
                        <li>
                            <?=
                                // コントローラー
                                $this->Form->customControl('acos._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'label' => false,
                                    'options' => [$controller->id => $controller->alias],
                                    'default' => array_column((array) $roleDetail->acos, 'id'),
                                    'readonly' => true,
                                    'hiddenField' => false,
                                    'data-type' => 'controller',
                                ])
                            ?>
                            <ul style="list-style-type: none">
                                <?php foreach ($controller->children as $action) : ?>
                                    <li>
                                        <?=
                                            // アクション
                                            $this->Form->customControl('acos._ids', [
                                                'type' => 'select',
                                                'multiple' => 'checkbox',
                                                'label' => false,
                                                'options' => [$action->id => $action->alias],
                                                'default' => array_column((array) $roleDetail->acos, 'id'),
                                                'readonly' => true,
                                                'hiddenField' => false,
                                            ])
                                        ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </li>
                    <?php endforeach ?>
                </ul>
            </td>
        </tr>
    </table>
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-BACK'), [
            // 戻る
            'data-action' => '/roles',
            'class' => 'btn-outline-secondary btn-cancel'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/role-details/edit',
            'data-id' => $roleDetail->id,
            'class' => 'btn-outline-primary btn-edit'
        ]) ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/role-details/delete',
            'data-id' => $roleDetail->id,
            'data-lock' => $roleDetail->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]) ?>
    </div>
</section>