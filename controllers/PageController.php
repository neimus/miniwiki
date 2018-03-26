<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\controllers;

use app\components\PagePath;
use app\models\Page;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::className(),
            ],
        ];
    }

    /**
     * @return mixed
     *
     * @throws \yii\base\InvalidParamException
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param $id
     *
     * @return mixed
     *
     * @throws \yii\base\InvalidParamException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', ['page' => $this->getPage($id)]);
    }

    /**
     * @param $id
     *
     * @return mixed
     *
     * @throws \yii\base\InvalidParamException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $page = $this->getPage($id);

        if ($page->load(\Yii::$app->request->post()) && $page->validate() && $page->save()) {

            return $this->redirect($page->getPath());
        }

        return $this->render('edit', ['page' => $page,]);
    }

    /**
     * @param int|null $id
     *
     * @return mixed
     *
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdd($id = null)
    {
        $page = new Page();

        if ($page->load(\Yii::$app->request->post())) {

            $page->setPath(PagePath::getPathWithoutAction(\Yii::$app->getRequest()->getPathInfo()));
            if ($page->validate() && $page->save()) {

                return $this->redirect($page->getPath());
            }
            $message = 'Возникла ошибка при добавлении статьи: ' . $page->getModelErrors();
            \Yii::$app->getSession()->setFlash('error', $message);
        }

        return $this->render('add',
            [
                'page'       => $page,
                'parentPage' => Page::find()->where(['id' => $id])->one(),
            ]);

    }

    /**
     * @param $id
     *
     * @return Page|array|null
     *
     * @throws \yii\web\NotFoundHttpException
     */
    private function getPage($id): Page
    {
        $page = Page::find()->where(['id' => $id])->one();
        if ($page === null) {
            throw new NotFoundHttpException('Данная статья не найдена');
        }

        return $page;
    }
}