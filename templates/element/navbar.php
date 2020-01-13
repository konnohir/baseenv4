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
            <a class="nav-link" href="/m-companies"><?= __('MCompanies') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/users"><?= __('Users') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/roles"><?= __('Roles') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/role-details"><?= __('RoleDetails') ?></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <?php if (Configure::read('debug')): ?>
        <li class="nav-item">
            <a class="nav-link" href="/homes/refresh" title="Cache clear (debug)">🔄</a>
        </li>
        <?php endif ?>
        <li class="nav-item">
            <a class="nav-link" href="/profile"><?= $this->Identity->get('email') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/logout"><?= __('Logout') ?></a>
        </li>
    </ul>
</nav>
<?php endif ?>