<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\Core\Exception\Exception $error
 * @var string $code
 * @var string $message
 * @var string $url
 */

use Cake\Core\Configure;
use Cake\Error\Debugger;

if (Configure::read('debug')) : ?>
    <?php
        $this->layout = 'dev_error';
        $this->assign('title', $message);
        $this->start('file');
    ?>
    <?php if (!empty($error->queryString)) : ?>
        <p class="notice">
            <strong>SQL Query: </strong>
            <?= h($error->queryString) ?>
        </p>
    <?php endif; ?>
    <?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
    <?php endif; ?>
    <?php if ($error instanceof Error || $error instanceof Exception) : ?>
        <strong>Error in: </strong>
        <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
    <?php endif; ?>
    <?php $this->end() ?>
<?php endif ?>
<section>
    <h2><?= __('Error') ?></h2>
    <p class="error">
        <?= __('LABEL-SYSTEM-ERROR') ?>
    </p>
    <small>
        <?= $code ?> <?= __d('cake', $message) ?>
    </small>
</section>