<?php
/**
 * ナビゲーションバー
 * 
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;
?>
<?php if ($this->Identity->isLoggedIn()): ?>
<nav class="collapse navbar-collapse" id="navbar">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <?php
                // 企業マスタ
                echo $this->Html->customLink(__('MCompanies'), ['controller' => 'MCompanies'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // ユーザーマスタ
                echo $this->Html->customLink(__('Users'), ['controller' => 'Users'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // 権限マスタ
                echo $this->Html->customLink(__('Roles'), ['controller' => 'Roles'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // 権限詳細マスタ
                echo $this->Html->customLink(__('RoleDetails'), ['controller' => 'RoleDetails'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <?php if (Configure::read('debug')): ?>
        <li class="nav-item">
            <?php
                // キャッシュクリア (デバッグ用)
                echo $this->Html->customLink('🔄', ['controller' => 'Homes', 'action' => 'refresh'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
        <?php endif ?>
        <li class="nav-item">
            <?php
                // プロファイル
                echo $this->Html->customLink($this->Identity->get('email'), ['controller' => 'Homes', 'action' => 'profile'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // ログアウト
                echo $this->Html->customLink(__('BTN-LOGOUT'), ['controller' => 'Homes', 'action' => 'logout'], [
                    'class' => 'nav-link'
                ]);
            ?>
        </li>
    </ul>
</nav>
<?php endif ?>