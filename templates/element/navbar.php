<?php
/**
 * ナビゲーションバー
 * ユーザーが認証済みの場合のみ表示する.
 * 
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;
?>
<?php if ($this->Identity->isLoggedIn()): ?>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
    <i class="material-icons">menu</i>
</button>
<nav class="collapse navbar-collapse" id="navbar">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <?php
                // 企業マスタ
                echo $this->Html->customLink('<i class="material-icons">location_city</i>' . __('MCompanies'), ['controller' => 'MCompanies', 'action' => 'index'], [
                    'class' => 'nav-link',
                    'escape' => false,
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // ユーザーマスタ
                echo $this->Html->customLink('<i class="material-icons">person</i>' . __('Users'), ['controller' => 'Users', 'action' => 'index'], [
                    'class' => 'nav-link',
                    'escape' => false,
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // 権限マスタ
                echo $this->Html->customLink('<i class="material-icons">group</i>' . __('Roles'), ['controller' => 'Roles', 'action' => 'index'], [
                    'class' => 'nav-link',
                    'escape' => false,
                ]);
            ?>
        </li>
        <li class="nav-item">
            <?php
                // 権限詳細マスタ
                echo $this->Html->customLink('<i class="material-icons">settings</i>' . __('RoleDetails'), ['controller' => 'RoleDetails', 'action' => 'index'], [
                    'class' => 'nav-link',
                    'escape' => false,
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
                echo $this->Html->customLink('<i class="material-icons">power_settings_new</i>', ['controller' => 'Homes', 'action' => 'logout'], [
                    'class' => 'nav-link',
                    'escape' => false,
                ]);
            ?>
        </li>
    </ul>
</nav>
<?php endif ?>