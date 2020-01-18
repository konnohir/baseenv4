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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?= $this->element('navbar') ?>
    </header>
    <main class="container-fluid">
        <div class="<?= $this->getName() ?> <?= $this->getRequest()->getParam('action') ?> py-3">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <?php /*
    <main class="container-fluid">
        <div class="row flex-md-nowrap">
            <div class="col-md side-menu bg-dark text-white border-top">
                <ul class="p-0 my-4" style="font-size:80%;list-style-type: none">
                    <li>新規作成</li>
                    <li>編集</li>
                    <li>パスワード再発行</li>
                    <li>アカウントロック</li>
                    <li>アカウントロック解除</li>
                    <li>削除</li>
                </ul>
            </div>
            <div class="col-md py-4 <?= $this->getName() ?> <?= $this->getRequest()->getParam('action') ?>">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </main>
    */ ?>
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