<?php

use app\components\PagePath;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $pages array */

$this->title = 'MiniWiki';
$pathAdd = '/' . PagePath::ACTION_ADD;

?>
<div class="site-index">

	<div class="jumbotron">
		<h1>Добро пожаловать!</h1>

		<p class="lead">Бесплатная и анонимная. &copy; <?= Yii::$app->name ?></p>

		<p>
            <?= Html::a('Добавить статью', $pathAdd, ['class' => 'btn btn-lg btn-success']) ?>
		</p>
	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-4">
				<h2>Бесплатно</h2>

				<p>Вы можете абсолютно бесплатно и самостоятельно изменять содержимое МиниВики с помощью инструментов,
				   предоставляемых самим сайтом. Форматирование текста и вставка различных объектов в текст производится
				   с использованием HTML и вики-разметки. На базе этих принципов построена Википедия и другие проекты
				   Фонда Викимедиа</p>

				<p>
                    <?= Html::a('Добавить бесплатную статью', $pathAdd, ['class' => 'btn btn-lg btn-default']) ?>
				</p>
			</div>
			<div class="col-lg-4">
				<h2>Анонимно</h2>

				<p>Добавить или изменить статью может любой желающий, а если Вам статья не понравилась, Вы всегда
				   сможете снять ее с публикации</p>

				<p>
                    <?= Html::a('Добавить анонимную статью', $pathAdd, ['class' => 'btn btn-lg btn-default']) ?>
				</p>
			</div>
			<div class="col-lg-4">
				<h2>Доступно</h2>

				<p>Наш сервис работает двадцать четыре часа и семь дней в неделю. В любую погоду и при любых
				   обстоятельствах (кроме не зависящих от нас)</p>

				<p>
                    <?= Html::a('Добавить доступную статью', $pathAdd, ['class' => 'btn btn-lg btn-default']) ?>
				</p>
			</div>
		</div>

	</div>
</div>
