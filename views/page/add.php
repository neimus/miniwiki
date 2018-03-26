<?php

use app\models\Page;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $page Page */
/* @var $parentPage Page|null */

$this->title = Html::encode($parentPage ? $parentPage->title : 'МиниВики');
\Yii::$app->params['page']['id'] = $parentPage !== null ? $parentPage->id : null;

?>
<div class="site-index">

	<div class="jumbotron">
		<h2>Добавление статьи для "<?= $this->title ?>"</h2>
	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-12">
                <?php $form = ActiveForm::begin([
                    'id'          => 'login-form',
                    'layout'      => 'horizontal',
                    'fieldConfig' => [
                        'template'     => '{label}
							<div class="col-lg-11">
								<div class="col-lg-8">{input}</div>
								<div class="col-lg-8 help-block">{error}{hint}</div>
							</div>
							',
                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                        'hintOptions'  => ['class' => 'help-block-hint col-sm-12'],
                        'errorOptions' => ['class' => 'help-block-error col-sm-12'],
                    ],
                ]); ?>

                <?= $form->field($page, Page::COL_NAME)->textInput([
                    'autofocus'   => true,
                    'placeholder' => $page->getPlaceholder(Page::COL_NAME),
                ]) ?>
                <?= $form->field($page, Page::COL_TITLE)
                    ->textInput(['placeholder' => $page->getPlaceholder(Page::COL_TITLE)]) ?>
                <?= $form->field($page, Page::COL_BODY)->textarea([
                    'rows'        => '10',
                    'placeholder' => $page->getPlaceholder(Page::COL_BODY),
                ]) ?>

				<div class="form-group">
					<div class="col-lg-offset-1 col-lg-7">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'name' => 'save-button']) ?>
                        <?= Html::a('Отменить', $parentPage !== null ? $parentPage->path : \Yii::$app->getHomeUrl(),
                            ['class' => 'btn btn-primary pull-right', 'name' => 'cancel-button']) ?>
					</div>
				</div>

                <?php ActiveForm::end(); ?>
			</div>
		</div>

	</div>
</div>
