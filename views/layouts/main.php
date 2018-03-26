<?php

use app\assets\AppAsset;
use app\components\PagePath;
use app\models\Page;
use app\widgets\PageBuildMenu;
use yii\bootstrap\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$menuItem = [];
$pageId = \Yii::$app->params['page']['id'] ?? null;
$action = PagePath::getActionFromPath(\Yii::$app->request->getUrl() . '/');

if ($action === ('page/' . PagePath::ACTION_VIEW)) {
    $path = PagePath::getPathWithoutAction(\Yii::$app->getRequest()->getPathInfo());
    $menuItem[] = ['label' => 'Добавить', 'url' => [$path . PagePath::ACTION_ADD]];
    if ($pageId !== null) {
        $menuItem[] = ['label' => 'Редактировать', 'url' => [$path . PagePath::ACTION_EDIT]];
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
                    //TODO: Временное решение для вывода меню (Кешировать или выводить порционно)
                    'data'            => Page::find()->getPagesForMenu(),
                    'contentCallback' => function (Page $page) use ($pageId) {
                        $title = StringHelper::truncateWords(Html::encode($page[Page::COL_TITLE]), 3);

                        return $pageId !== (int)$page[Page::COL_ID]
							? Html::a($title, $page->path)
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
