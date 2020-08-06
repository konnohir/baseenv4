<?php
/**
 * (フォールバック用)エラーレイアウト
 * システムエラー画面レンダリング時に例外が発生した場合、このレイアウトが使用される.
 * 
 * @var \App\View\AppView $this
 */
?>
<?= $this->fetch('content') ?>
