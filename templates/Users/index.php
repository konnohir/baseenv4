<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<section>
    <h2><?= __('Users') ?></h2>
    <div class="card mb-2">
        <div class="card-body py-1">
            <?= $this->Form->create() ?>
            <?php
                // メールアドレス
                echo $this->Form->customControl('filter.email', [
                    'label' => __('Users.email')
                ]);
                // 条件クリア
                echo $this->Form->customButton(__('BTN-CLEAR'), [
                    'data-action' => ['action' => 'index'],
                    'class' => 'btn-outline-secondary btn-clear'
                ]);
                // 検索
                echo $this->Form->customButton(__('BTN-SEARCH'), [
                    'type' => 'submit',
                    'class' => 'btn-outline-info btn-search',
                ]);
            ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
    <div class="btn-group">
        <?php
            // 新規作成
            echo $this->Form->customButton(__('BTN-ADD'), [
                'data-action' => ['action' => 'add'],
                'class' => 'btn-outline-primary btn-add'
            ]);
            // 編集
            echo $this->Form->customButton(__('BTN-EDIT'), [
                'data-action' => ['action' => 'edit'],
                'class' => 'btn-outline-primary btn-edit'
            ]);
            // パスワード再発行
            echo $this->Form->customButton(__('BTN-PASSWORD-ISSUE'), [
                'data-action' => ['action' => 'passwordIssue'],
                'class' => 'btn-outline-success btn-password-issue'
            ]);
            // アカウントロック
            echo $this->Form->customButton(__('BTN-ACCOUNT-LOCK'), [
                'data-action' => ['action' => 'lockAccount'],
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // アカウントロック解除
            echo $this->Form->customButton(__('BTN-ACCOUNT-UNLOCK'), [
                'data-action' => ['action' => 'unlockAccount'],
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // 削除
            echo $this->Form->customButton(__('BTN-DELETE'), [
                'data-action' => ['action' => 'delete'],
                'class' => 'btn-outline-danger btn-delete'
            ]);
        ?>
    </div>
    <div class="pagination-wrap">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->counter() ?>
            <?= $this->Paginator->first() ?>
            <?= $this->Paginator->numbers(['modulus' => 4]) ?>
            <?= $this->Paginator->last() ?>
        </ul>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->checkboxAll() ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('Users.email', ['label' => __('Users.email')]) ?>
                    </th>
                    <th><?= $this->Paginator->sort('Roles.id', ['label' => __('Users.role_id')]) ?></th>
                    <th><?= $this->Paginator->sort('VUserRemarks.is_account_locked', ['label' => __('Users.account_lock')]) ?></th>
                    <th><?= $this->Paginator->sort('VUserRemarks.is_password_issued', ['label' => __('Users.password_issue')]) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($user->id, $user->_lock) ?></td>
                    <td><?= $this->Html->link($user->email, ['action' => 'view', $user->id]) ?></td>
                    <td><?= h($user->role->name ?? null) ?></td>
                    <td><?= h($user->v_user_remark->is_account_locked ? '〇' : '') ?></td>
                    <td><?= h($user->v_user_remark->is_password_issued ? '〇' : '') ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
