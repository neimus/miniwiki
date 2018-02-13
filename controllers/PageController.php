<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\controllers;

use app\components\PageUrlRule;
use app\models\Page;
use app\models\PageRel;
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
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $path = \Yii::$app->request->getUrl() . '/';
        \Yii::$app->params['page']['action'] = PageUrlRule::getActionByPath($path);
        \Yii::$app->params['page']['path'] = $this->getPath();
        \Yii::$app->params['page']['isNestingAvailable'] = $this->isNestingAvailable();
    }

    /**
     * @return string
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
     * @return string
     *
     * @throws \yii\base\InvalidParamException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view',
            [
                'page' => $this->getPage($id),
            ]);
    }

    /**
     * @param $id
     *
     * @return string
     *
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $page = $this->getPage($id);

        if ($page->load(\Yii::$app->request->post())
            && $page->validate() && $page->save()) {

            return $this->redirect($this->getPath());
        }

        return $this->render('edit',
            [
                'page' => $page,
            ]);
    }

    /**
     * @param int|null $id
     *
     * @return string
     *
     * @throws \yii\db\Exception
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdd($id = null)
    {
        if (!$this->isNestingAvailable()) {
            \Yii::$app->getSession()
                ->setFlash('warning', 'Мы приносим извинения, но сюда больше нельзя добавить статью');

            return $this->redirect($this->getPath());
        }

        $page = new Page();
        $pageRel = new PageRel();

        if (\Yii::$app->request->isPost && $this->addPage($page, $pageRel)) {

            return $this->redirect($this->getPath() . $page->name);
        }
        $pageRel->parent_id = $id;

        return $this->render('add',
            [
                'page'       => $page,
                'pageRel'    => $pageRel,
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

    /**
     * @param Page $page
     * @param PageRel $pageRel
     *
     * @return bool
     *
     * @throws \yii\db\Exception
     */
    private function addPage(Page $page, PageRel $pageRel): bool
    {
        if ($page->load(\Yii::$app->request->post()) && $page->validate()
            && $pageRel->load(\Yii::$app->request->post())) {
            $transaction = Page::getDb()->beginTransaction();
            try {
                if ($page->save()) {
                    if (!empty($pageRel->parent_id)) {
                        $parentPage = Page::find()->where(['id' => $pageRel->parent_id])->one();
                        if ($parentPage !== null) {
                            $parentPage->addChild($page->id);
                        } else {
                            throw new \LogicException('родительская страница не была найдена в БД');
                        }
                    }
                    $page->setParent($pageRel->parent_id);
                    $transaction->commit();

                    return true;
                }

                throw new \LogicException($page->getModelErrors());

            } catch (\Exception $exception) {
                $transaction->rollBack();
                \Yii::$app->getSession()
                    ->setFlash('error', 'Возникла ошибка при добавлении статьи: ' . $exception->getMessage());
            }
        }

        return false;
    }

    /**
     * @return string
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function getPath(): string
    {
        return PageUrlRule::getPathWithoutAction(\Yii::$app->getRequest()->getPathInfo());
    }

    /**
     * @return bool
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function isNestingAvailable(): bool
    {
        return PageUrlRule::isNestingAvailable(\Yii::$app->getRequest()->getPathInfo());
    }
}