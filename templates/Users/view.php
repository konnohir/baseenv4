<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<section>
    <h2 class="mb-2"><?= __('Users') ?></h2>
    <div class="dl-wrap mb-4">
        <dl class="row">
            <?php // メールアドレス ?>
            <dt class="col-md"><?= __('Users.email') ?></dt>
            <dd class="col-md"><?= h($user->email) ?></dd>
        </dl>
        <dl class="row">
            <?php // 権限 ?>
            <dt class="col-md"><?= __('Users.role_id') ?></dt>
            <dd class="col-md"><?= h($user->role->name ?? null) ?></dd>
        </dl>
        <dl class="row">
            <?php // アカウントロック ?>
            <dt class="col-md"><?= __('Users.account_lock') ?></dt>
            <dd class="col-md"><?= h($user->login_failed_count >= 5 ? '〇' : '') ?></dd>
        </dl>
        <dl class="row">
            <?php // パスワード発行 ?>
            <dt class="col-md"><?= __('Users.password_issue') ?></dt>
            <dd class="col-md"><?= h($user->password_issue ? '〇' : '') ?></dd>
        </dl>
    </div>
    <div class="btn-group my-2">
        <?php
            // 戻る
            echo $this->Form->customButton(__('BTN-BACK'), [
                'data-action' => ['action' => 'index'],
                'class' => 'btn-outline-secondary btn-cancel'
            ]);
            // 編集
            echo $this->Form->customButton(__('BTN-EDIT'), [
                'data-action' => ['action' => 'edit'],
                'data-id' => $user->id,
                'data-lock' => $user->_lock,
                'class' => 'btn-outline-primary btn-edit'
            ]);
            // パスワード再発行
            echo $this->Form->customButton(__('BTN-PASSWORD-ISSUE'), [
                'data-action' => ['action' => 'passwordIssue'],
                'data-id' => $user->id,
                'data-lock' => $user->_lock,
                'class' => 'btn-outline-success btn-password-issue'
            ]);
            // アカウントロック
            echo $this->Form->customButton(__('BTN-ACCOUNT-LOCK'), [
                'data-action' => ['action' => 'lockAccount'],
                'data-id' => $user->id,
                'data-lock' => $user->_lock,
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // アカウントロック解除
            echo $this->Form->customButton(__('BTN-ACCOUNT-UNLOCK'), [
                'data-action' => ['action' => 'unlockAccount'],
                'data-id' => $user->id,
                'data-lock' => $user->_lock,
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // 削除
            echo $this->Form->customButton(__('BTN-DELETE'), [
                'data-action' => ['action' => 'delete'],
                'data-id' => $user->id,
                'data-lock' => $user->_lock,
                'class' => 'btn-outline-danger btn-delete'
            ]);
        ?>
    </div>
</section>