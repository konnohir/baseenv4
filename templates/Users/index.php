<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="w-100 mx-auto">
    <h2 class="mb-2"><?= __('Users') ?></h2>
    <div class="card mb-2">
        <div class="card-body py-1">
            <?= $this->Form->create() ?>
            <?= $this->Form->customControl('filter.email', ['label' => 'メールアドレス']) ?>
            <?= $this->Form->customButton(__('BTN-CLEAR'), ['data-action' => '/users', 'class' => 'btn-outline-secondary btn-clear']) ?>
            <?= $this->Form->customButton(__('BTN-SEARCH'), ['type' => 'submit', 'class' => 'btn-outline-info btn-search']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
    <div class="btn-group mb-2">
        <?= $this->Form->customButton(__('BTN-ADD'), [
            // 新規作成
            'data-action' => '/users/add',
            'class' => 'btn-outline-primary btn-add'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-EDIT'), [
            // 編集
            'data-action' => '/users/edit',
            'class' => 'btn-outline-primary btn-edit'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-PASSWORD-ISSUE'), [
            // パスワード再発行
            'data-action' => '/users/password-issue',
            'class' => 'btn-outline-success btn-jump-api'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-ACCOUNT-LOCK'), [
            // アカウントロック
            'data-action' => '/users/account-lock',
            'class' => 'btn-outline-success btn-jump-api'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-ACCOUNT-UNLOCK'), [
            // アカウントロック解除
            'data-action' => '/users/account-unlock',
            'class' => 'btn-outline-success btn-jump-api'
        ])
        ?>
        <?= $this->Form->customButton(__('BTN-DELETE'), [
            // 削除
            'data-action' => '/users/delete',
            'class' => 'btn-outline-danger btn-delete'
        ])
        ?>
    </div>
    <div class="pagination-wrap mb-2">
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->counter() ?>
            <?= $this->Paginator->first() ?>
            <?= $this->Paginator->numbers(['modulus' => 4]) ?>
            <?= $this->Paginator->last() ?>
        </ul>
    </div>
    <div class="table-wrap mb-2">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->checkboxAll() ?></th>
                    <th class="w-100">
                        <?= $this->Paginator->sort('Users.email', ['label' => __('メールアドレス')]) ?>
                    </th>
                    <th><?= $this->Paginator->sort('Roles.id', ['label' => __('権限')]) ?></th>
                    <th><?= $this->Paginator->sort('Users.login_failed_count', ['label' => __('アカウントロック')]) ?></th>
                    <th><?= $this->Paginator->sort('Users.password_issue', ['label' => __('パスワード発行')]) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($user->id, $user->_lock) ?></td>
                    <td><?= $this->Html->link($user->email, ['action' => 'view', $user->id]) ?></td>
                    <td><?= h($user->role->name ?? null) ?></td>
                    <td><?= $user->login_failed_count >= 5 ? '〇' : '' ?></td>
                    <td><?= $user->password_issue ? '〇' : '' ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
