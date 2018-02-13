<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $page \app\models\Page */

$this->title = $page->title;
\Yii::$app->params['page']['id'] = $page->id;

?>
<div class="site-index">

	<div class="jumbotron">
		<div class="pull-right">
			Дата: <?= \Yii::$app->getFormatter()->asDatetime($page->updated_at) ?>
		</div>
		<h2><?= Html::encode($page->title) ?></h2>
		<p class="lead">Бесплатная и анонимная. &copy; <?= Yii::$app->name ?></p>

	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-12">
                <?php if ($page->is_published): ?>
                    <?= \Yii::$app->getFormatter()->asWikiStyle($page->body) ?>
                <?php else: ?>
					<div class="col-lg-12 text-center">
						<h4 style="color: #32609e">
							Данная статья была снята с публикации.
						</h4>
						<p>Для того чтобы заново опубликовать ее, необходимо в редактировании разрешить публикацию этой
						   статьи.</p>
					</div>
                <?php endif; ?>
			</div>
		</div>

	</div>
</div>
