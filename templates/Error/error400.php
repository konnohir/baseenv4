<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\Core\Exception\Exception $error
 * @var string $code
 * @var string $message
 * @var string $url
 */
?>
<section>
    <h2><?= __('Error') ?></h2>
    <p class="error">
        <?= __('LABEL-SYSTEM-ERROR') ?>
    </p>
    <small>
        <?= $code ?> <?= __d('cake', $message) ?>
    </small>
</section>