<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <h2 class="mb-2"><?= __('Password') ?></h2>
    <?= $this->Form->create($user, ['type' => 'post']) ?>
    <?= $this->Form->hidden('_lock') ?>
    <div class="dl-wrap dl-wrap-form mb-4">
        <dl class="row">
            <dt class="col-md required">
                <?php
                    // 現在のパスワード
                    echo $this->Form->label('password', __('Users.current_password'));
                ?>
            </dt>
            <dd class="col-md">
                <?php
                    echo $this->Form->customControl('current_password', [
                        'type' => 'password',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?php
                    // 新しいパスワード
                    echo $this->Form->label('new_password', __('Users.new_password'));
                ?>
            </dt>
            <dd class="col-md">
                <?php
                    echo $this->Form->customControl('new_password', [
                        'type' => 'password',
                        'label' => false,
                    ]);
                ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="col-md required">
                <?php
                    // 新しいパスワード（再入力）
                    echo $this->Form->label('password', __('Users.password.2'));
                ?>
            </dt>
            <dd class="col-md">
                <?php
                    echo $this->Form->customControl('password', [
                        'type' => 'password',
                        'label' => false,
                        'value' => '',
                    ]);
                ?>
            </dd>
        </dl>
    </div>
    <div class="button-wrap py">
        <?php
            // キャンセル
            echo $this->Form->customButton(__('BTN-CANCEL'), [
                'data-action' => ['controller' => 'Homes', 'action' => 'profile'],
                'class' => 'btn-secondary btn-jump'
            ]);
            // 保存
            echo $this->Form->customButton(__('BTN-SAVE'), [
                'class' => 'btn-primary btn-submit'
            ]);
        ?>
    </div>
    <?= $this->Form->end() ?>
</section>