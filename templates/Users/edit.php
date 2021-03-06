<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array $roleList
 */
?>
<section>
    <h2><?= __('Users') ?></h2>
    <?= $this->Form->create($user, ['type' => 'post']) ?>
    <?php
        // 排他制御用フィールド
        echo $this->Form->hidden('_lock');
    ?>
    <div class="dl-wrap dl-wrap-form">
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('email', __('Users.email')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // メールアドレス
                    echo $this->Form->customControl('email', [
                        'type' => 'text',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?= $this->Form->label('role_id', __('Users.role_id')) ?>
            </dt>
            <dd class="col-md">
                <?php
                    // 権限
                    echo $this->Form->customControl('role_id', [
                        'type' => 'select',
                        'options' => $roleList,
                        'empty' => true,
                        'label' => false,
                    ]);
                ?>
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