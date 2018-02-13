<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\components;

use app\helpers\CacheHelper;
use app\models\Page;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\web\UrlRuleInterface;

class PageUrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * Page action
     */
    const ACTION_EDIT = 'edit';
    const ACTION_ADD  = 'add';
    const ACTION_VIEW = 'view';

    /**
     * @var array
     */
    public static $pageAction = [self::ACTION_EDIT, self::ACTION_ADD, self::ACTION_VIEW];

    /**
     * Возвращает действие контроллера из url
     *
     * @param string $path
     *
     * @return string
     */
    public static function getActionByPath(string $path): string
    {
        $uriArray = self::explode(self::encode($path));

        return self::removeAction($uriArray);
    }

    /**
     * Возвращает путь без action
     *
     * @param string $path
     *
     * @return string
     */
    public static function getPathWithoutAction(string $path): string
    {
        $uriArray = self::explode(self::encode($path));
        self::removeAction($uriArray);

        return !empty($uriArray) ? '/' . implode('/', $uriArray) . '/' : '/';
    }

    /**
     * Проверяет доступна ли добавление вложенности страниц на данном URL
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isNestingAvailable($path): bool
    {
        $uriArray = self::explode(self::encode($path));
        self::removeAction($uriArray);

        return \count($uriArray) < \Yii::$app->params['nestingLevelPages'];
    }

    /**
     * @param string $pathInfo
     *
     * @return string
     */
    public static function encode(string $pathInfo): string
    {
        return Html::encode(trim($pathInfo, '/'));
    }

    /**
     * Удаляет действие контроллера из массива путей
     *
     * @param array $pathArray
     *
     * @return string возвращает действие контроллера
     */
    public static function removeAction(array &$pathArray): string
    {
        if (!empty($pathArray) && \in_array(end($pathArray), self::$pageAction, true)) {
            return 'page/' . array_pop($pathArray);
        }

        return 'page/' . self::ACTION_VIEW;
    }

    /**
     * @param string $path
     * @param bool $useLimitNesting
     *
     * @return array
     */
    public static function explode(string $path, $useLimitNesting = true): array
    {
        return explode('/', $path, $useLimitNesting ? \Yii::$app->params['nestingLevelPages'] + 1 : null);
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        try {
            $path = $request->getPathInfo();
            $cacheId = CacheHelper::getId(CacheHelper::TYPE_ROUTE, $path);

            return CacheHelper::get($cacheId) ?? CacheHelper::set($cacheId, $this->getRoute(self::encode($path)),
                    \Yii::$app->params['durationCacheRoute']);
        } catch (InvalidConfigException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        return $route;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function check(string $path): bool
    {
        return preg_match('%^/?([\w+/]+)/?$%', $path);
    }

    /**
     * @param $path
     *
     * @return array|bool
     */
    private function getRoute($path)
    {
        if ($this->check($path)) {
            $pathArray = self::explode($path);
            $action = self::removeAction($pathArray);
            $id = $this->getCurrentPageId($pathArray);
            if ($id !== null) {
                return [$action, ['id' => $id]];
            }
        }

        return false;
    }

    /**
     * @param array $pathArray
     * @param null $parentPage
     *
     * @return int|null
     */
    private function getCurrentPageId(array $pathArray, $parentPage = null)
    {
        if (!empty($pathArray)) {
            if ($parentPage === null) {
                /* Получаем родительскую страницу */
                $parentPage = Page::find()->where([Page::COL_NAME => array_shift($pathArray)])->one();
                if ($parentPage !== null && $parentPage->issetParent()) {
                    return null;
                }
            }
            if ($parentPage !== null) {
                if (empty($pathArray)) {
                    return !$parentPage->issetParent() ? (int)$parentPage->id : null;
                }
                /* Получаем подстраницу */
                $childrenPage = $parentPage->getChildrenByName(array_shift($pathArray));
                if ($childrenPage !== null) {
                    if (!empty($pathArray)) {
                        return $this->getCurrentPageId($pathArray, $childrenPage);
                    }

                    return (int)$childrenPage->id;
                }
            }
        }

        return null;
    }
}