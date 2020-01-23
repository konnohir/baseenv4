<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleDetail $roleDetail
 */
$acoParentIds = array_column((array) $roleDetail->acos, 'parent_id', 'parent_id');
$acoIds = array_column((array) $roleDetail->acos, 'id', 'id');
?>
<section>
    <h2 class="mb-2"><?= __('RoleDetails') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <?php // 名称 
            ?>
            <dt class="col-md"><?= __('RoleDetails.name') ?></dt>
            <dd class="col-md"><?= h($roleDetail->name) ?></dd>
        </dl>
        <dl class="row">
            <?php // 説明 ?>
            <dt class="col-md"><?= __('RoleDetails.description') ?></dt>
            <dd class="col-md"><?= h($roleDetail->description) ?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md"><?= __('RoleDetails.acos') ?></dt>
            <dd class="col-md">
                <ul style="list-style-type: none;padding-left:0">
                    <?php foreach ($acos as $controller) : ?>
                        <?php
                        if (
                            !isset($acoParentIds[$controller->id]) &&
                            !isset($acoIds[$controller->id])) {
                                // 選択されていないコントローラーは表示しない
                                continue;
                            }
                        ?>
                        <li>
                            <?=
                                // コントローラー
                                $this->Form->customControl('acos._ids', [
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'label' => false,
                                    'options' => [$controller->id => $controller->alias],
                                    'default' => $acoIds,
                                    'readonly' => true,
                                    'hiddenField' => false,
                                    'data-type' => 'controller',
                                ]);
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
                                                'default' => $acoIds,
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
            </dd>
        </dl>
    </div>
    <div class="btn-group my-2">
        <?php
        // 戻る
        echo $this->Form->customButton(__('BTN-BACK'), [
            'data-action' => ['controller' => 'RoleDetails', 'action' => 'index'],
            'class' => 'btn-outline-secondary btn-cancel'
        ]);
        // 編集
        echo $this->Form->customButton(__('BTN-EDIT'), [
            'data-action' => ['controller' => 'RoleDetails', 'action' => 'edit'],
            'data-id' => $roleDetail->id,
            'data-lock' => $roleDetail->_lock,
            'class' => 'btn-outline-primary btn-edit'
        ]);
        // 削除
        echo $this->Form->customButton(__('BTN-DELETE'), [
            'data-action' => ['controller' => 'RoleDetails', 'action' => 'delete'],
            'data-id' => $roleDetail->id,
            'data-lock' => $roleDetail->_lock,
            'class' => 'btn-outline-danger btn-delete'
        ]);
        ?>
    </div>
</section>