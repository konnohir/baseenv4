<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/favicon.ico" type="image/x-icon" rel="icon" />
    <?=
        $this->Html->css('/vendor/bootstrap.min'),
        $this->Html->css('common'),
        $this->fetch('css')
    ?>

    <title><?= $this->fetch('title') ?></title>
</head>
<body>
    <header class="navbar navbar-expand-md navbar-dark bg-dark p-0">
        <h1><a href="/" class="navbar-brand"><?= __('CakePHP4dev') ?></a></h1>
        <?= $this->element('navbar') ?>
    </header>
    <main class="container-fluid">
        <div class="<?= $this->getName() ?> <?= $this->getRequest()->getParam('action') ?> py-3">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer>
        <p class="text-center border-top">© 2019 footer</p>
    </footer>
    <?=
        $this->Html->script('/vendor/jquery-3.4.1.slim.min'),
        $this->Html->script('/vendor/bootstrap.bundle.min'),
        $this->Html->script('common'),
        $this->fetch('script'),

        // for common.js
        $this->Form->create(null, ['id' => 'postForm', 'url' => false]),
        $this->Form->end()
    ?>

</body>
</html>