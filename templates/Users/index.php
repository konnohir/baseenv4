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
            <?= $this->Form->customControl('filter.email', ['label' => __('Users.email')]) ?>
            <?= $this->Form->customButton(__('BTN-CLEAR'), ['data-action' => ['controller' => 'Users', 'action' => 'index'], 'class' => 'btn-outline-secondary btn-clear']) ?>
            <?= $this->Form->customButton(__('BTN-SEARCH'), ['type' => 'submit', 'class' => 'btn-outline-info btn-search']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
    <div class="btn-group mb-2">
        <?php
            // 新規作成
            echo $this->Form->customButton(__('BTN-ADD'), [
                'data-action' => ['controller' => 'Users', 'action' => 'add'],
                'class' => 'btn-outline-primary btn-add'
            ]);
            // 編集
            echo $this->Form->customButton(__('BTN-EDIT'), [
                'data-action' => ['controller' => 'Users', 'action' => 'edit'],
                'class' => 'btn-outline-primary btn-edit'
            ]);
            // パスワード再発行
            echo $this->Form->customButton(__('BTN-PASSWORD-ISSUE'), [
                'data-action' => ['controller' => 'Users', 'action' => 'passwordIssue'],
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // アカウントロック
            echo $this->Form->customButton(__('BTN-ACCOUNT-LOCK'), [
                'data-action' => ['controller' => 'Users', 'action' => 'lockAccount'],
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // アカウントロック解除
            echo $this->Form->customButton(__('BTN-ACCOUNT-UNLOCK'), [
                'data-action' => ['controller' => 'Users', 'action' => 'unlockAccount'],
                'class' => 'btn-outline-success btn-jump-api'
            ]);
            // 削除
            echo $this->Form->customButton(__('BTN-DELETE'), [
                'data-action' => ['controller' => 'Users', 'action' => 'delete'],
                'class' => 'btn-outline-danger btn-delete'
            ]);
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
                        <?= $this->Paginator->sort('Users.email', ['label' => __('Users.email')]) ?>
                    </th>
                    <th><?= $this->Paginator->sort('Roles.id', ['label' => __('Users.role_id')]) ?></th>
                    <th><?= $this->Paginator->sort('Users.login_failed_count', ['label' => __('Users.account_lock')]) ?></th>
                    <th><?= $this->Paginator->sort('Users.password_issue', ['label' => __('Users.password_issue')]) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $this->Paginator->checkbox($user->id, $user->_lock) ?></td>
                    <td><?= $this->Html->link($user->email, ['action' => 'view', $user->id]) ?></td>
                    <td><?= h($user->role->name ?? null) ?></td>
                    <td><?= h($user->login_failed_count >= 5 ? '〇' : '') ?></td>
                    <td><?= h($user->password_issue ? '〇' : '') ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
