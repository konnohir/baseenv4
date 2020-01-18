<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\Core\Exception\Exception $error
 * @var string $code
 * @var string $message
 * @var string $url
 */

use Authorization\Exception\ForbiddenException;

// fix
if ($error instanceof ForbiddenException) {
    $message = 'Forbidden';
}
?>
<section>
    <h2><?= __('システムエラー') ?></h2>
    <p class="error">
        <?= __('お手数ですが最初からやり直してください。') ?>
    </p>
    <small>
        <?= $code ?> <?= __d('cake', $message) ?>
    </small>
</section>