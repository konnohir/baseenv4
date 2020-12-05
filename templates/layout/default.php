<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/favicon.ico" type="image/x-icon" rel="icon">
    <?=
        // 全ページ共通のスタイルシート
        $this->Html->css('vendor/bootstrap.min'),
        $this->Html->css('vendor/material-design-icons/material-design-icons'),
        $this->Html->css('common/common'),
        // ページ固有のスタイルシート
        $this->fetch('css')
    ?>

    <title><?= $this->fetch('title') ?></title>
</head>
<body>
    <header class="navbar navbar-expand-md">
        <h1><a href="/" class="navbar-brand"><?= __('BaseEnv4') ?></a></h1>
        <?= $this->element('navbar') ?>
    </header>
    <main class="<?= $this->getName(), ' ', $this->getRequest()->getParam('action') ?> mx-auto p-3">
        <?php 
            // DB接続エラー時に発生する例外を抑制するfix
            try {
                echo $this->Flash->render();
            }catch(Exception $e) {
                // pass
            }
        ?>
        <?= $this->fetch('content') ?>
    </main>
    <footer>
        <p class="text-center border-top">© 2020 konnohir</p>
    </footer>
    <?=
        // for common.js
        $this->Form->create(null, ['id' => 'postForm', 'url' => false]),
        $this->Form->end(),
        // 全ページ共通のJavaScript
        $this->Html->script('vendor/jquery-3.4.1.min'),
        $this->Html->script('vendor/bootstrap.bundle.min'),
        $this->Html->script('common/common'),
        // ページ固有のJavaScript
        $this->fetch('script')
    ?>

</body>
</html>