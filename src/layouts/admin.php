<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use reinvently\ondemand\core\assets\AdminAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script type="text/javascript">
        var authToken = '<?= \Yii::$app->controller->authToken ?>';
    </script>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'On-Demand',
        'brandUrl' => Yii::$app->homeUrl . 'admin',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    echo Nav::widget([
        'items' => \Yii::$app->controller->navItems,
        'options' => ['class' => 'navbar-nav navbar-left'],
    ]);

    echo Nav::widget([
        'items' => [
            [
                'label' => 'Logout',
                'url' => ['/admin/auth/logout'],
            ]
        ],
        'options' => ['class' => 'navbar-nav navbar-right'],
    ]);

    NavBar::end();
    ?>
    <div class="container mainContent">
        <?= Breadcrumbs::widget([
            'homeLink' => [
                'label' => 'Admin panel',
                'url' => Yii::$app->homeUrl . 'admin'
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; On-Demand <?= date('2015-Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
