<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\components;

use app\models\Page;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class PageUrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function parseRequest($manager, $request)
    {
        $path = PagePath::encode($request->getPathInfo());

        if ($this->check($path)) {
            $page = Page::find()->getPageByPath(PagePath::getPathWithoutAction($path));
            if ($page !== null) {
                return [PagePath::getActionFromPath($path), ['id' => $page->id]];
            }
        }

        return false;
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
}