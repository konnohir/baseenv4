<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section>
    <h2>マスタ管理</h2>
    <div class="row mb-4">
        <div class="col-sm-6 mb-2">
            <?php
                // 企業マスタ
                echo $this->Form->customButton('<i class="material-icons">location_city</i>' . __('MCompanies'), [
                    'data-action' => ['controller' => 'MCompanies', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
        <div class="col-sm-6 mb-2">
            <?php
                // 組織マスタ
                echo $this->Form->customButton('<i class="material-icons">format_list_bulleted</i>' . __('Organizations'), [
                    'data-action' => ['controller' => 'Organizations', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
    </div>
    <h2>システム管理</h2>
    <div class="row mb-4">
        <div class="col-sm-6 mb-2">
            <?php
                // ユーザーマスタ
                echo $this->Form->customButton('<i class="material-icons">person</i>' . __('Users'), [
                    'data-action' => ['controller' => 'Users', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
        <div class="col-sm-6 mb-2">
            <?php
                // 権限マスタ
                echo $this->Form->customButton('<i class="material-icons">group</i>' . __('Roles'), [
                    'data-action' => ['controller' => 'Roles', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
        <div class="col-sm-6 mb-2">
            <?php
                // 権限詳細マスタ
                echo $this->Form->customButton('<i class="material-icons">settings</i>' . __('RoleDetails'), [
                    'data-action' => ['controller' => 'RoleDetails', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
    </div>
</section>