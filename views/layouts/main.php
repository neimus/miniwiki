<?php

use app\assets\AppAsset;
use app\components\PageUrlRule;
use app\models\Page;
use app\widgets\Alert;
use app\widgets\PageBuildMenu;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$menuItem = [];
$id = \Yii::$app->params['page']['id'] ?? null;
$path = \Yii::$app->params['page']['path'];
$action = \Yii::$app->params['page']['action'];
$isNestingAvailable = \Yii::$app->params['page']['isNestingAvailable'];

if ($action === 'page/' . PageUrlRule::ACTION_VIEW) {
    if ($isNestingAvailable) {
        $menuItem[] = ['label' => 'Добавить', 'url' => [$path . PageUrlRule::ACTION_ADD]];
    }
    if ($id !== null) {
        $menuItem[] = ['label' => 'Редактировать', 'url' => [$path . PageUrlRule::ACTION_EDIT]];
    }
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items'   => $menuItem,
    ]);
    NavBar::end();
    ?>

	<div class="container-fluid" style="padding-top: 60px">
		<div class="col-lg-3">
			<h2>Рубрики</h2>
			<div class="wiki-menu">
                <?= PageBuildMenu::widget([
                    'nestedAttr'      => Page::COL_NAME,
                    'contentCallback' => function ($page, array $nested) use ($id) {
                        $title = StringHelper::truncateWords(Html::encode($page[Page::COL_TITLE]), 3);

                        return $id !== (int)$page[Page::COL_ID]
                            ? Html::a($title, Url::home() . implode('/', $nested))
                            : Html::tag('strong', $title);

                    },
                ]) ?>
			</div>

		</div>

		<div class="col-lg-9">
            <?php //echo Breadcrumbs::widget([
            //     'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            // ]); ?>
            <?= Alert::widget() ?>
            <?= $content ?>
		</div>
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>

		<p class="pull-right"><?= Yii::powered() ?></p>
	</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
