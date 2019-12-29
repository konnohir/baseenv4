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
use Cake\Datasource\Exception\RecordNotFoundException;
if (Configure::read('debug')) :
    if ($error instanceof RecordNotFoundException) {
        $this->extend('error400');
        return;
    }
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
<?php
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>
<h2><?= __('サーバーエラー') ?></h2>
<p class="error">
    <?= h($message) ?>
</p>
