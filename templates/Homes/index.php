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
                // 本部マスタ
                echo $this->Form->customButton('<i class="material-icons">location_city</i>' . __('MDepartment1s'), [
                    'data-action' => ['controller' => 'MDepartment1s', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
        <div class="col-sm-6 mb-2">
            <?php
                // 部店マスタ
                echo $this->Form->customButton('<i class="material-icons">location_city</i>' . __('MDepartment2s'), [
                    'data-action' => ['controller' => 'MDepartment2s', 'action' => 'index'],
                    'class' => 'btn-block btn-outline-primary btn-jump',
                    'escapeTitle' => false,
                ]);
            ?>
        </div>
        <div class="col-sm-6 mb-2">
            <?php
                // 課マスタ
                echo $this->Form->customButton('<i class="material-icons">location_city</i>' . __('MDepartment3s'), [
                    'data-action' => ['controller' => 'MDepartment3s', 'action' => 'index'],
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