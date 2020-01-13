<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/favicon.ico" type="image/x-icon" rel="icon" />
    <?=
        $this->Html->css('bootstrap.min'),
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
    <main class="container-fluid <?= $this->getName() ?> <?= $this->getRequest()->getParam('action') ?>">
        <div class="row flex-md-nowrap">
            <!-- <div class="col-md side-menu">
                Side Menu
                <ul>
                    <li>Side Menu Name 1</li>
                </ul>
            </div> -->
            <div class="col-md py-4">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>
    </main>
    <footer>
        <p class="text-center border-top">© 2019 footer</p>
    </footer>
    <?=
        $this->Html->script('jquery-3.4.1.slim.min'),
        $this->Html->script('bootstrap.bundle.min'),
        $this->Html->script('common'),
        $this->fetch('script'),

        // for common.js
        $this->Form->create(null, ['id' => 'postForm', 'url' => false]),
        $this->Form->end()
    ?>

</body>

</html>