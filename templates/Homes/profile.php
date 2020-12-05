<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <!-- タイトル -->
    <h2><?= __('Profile') ?></h2>
    <!-- データ出力領域-->
    <div class="dl-wrap">
        <?php
            // メールアドレス
            echo $this->element('dl', [
                'label' => __('Users.email'),
                'value' => $user->email,
            ]);

            // 権限
            echo $this->element('dl', [
                'label' => __('Users.role_id'),
                'value' => $user->role->name ?? null,
            ]);
        ?>
    </div>
    <!-- ボタン表示領域 -->
    <div class="btn-group">
        <?php
            // パスワード変更
            echo $this->Form->customButton(__('BTN-PASSWORD-CHANGE'), [
                'data-action' => ['controller' => 'Homes', 'action' => 'password'],
                'class' => 'btn-outline-primary btn-jump'
            ])
        ?>
    </div>
</section>

