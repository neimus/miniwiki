<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 25.03.18
 */

namespace app\components;

use yii\helpers\Html;

class PagePath
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
    public static function getActionFromPath(string $path): string
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
    private static function removeAction(array &$pathArray): string
    {
        if (!empty($pathArray) && \in_array(end($pathArray), self::$pageAction, true)) {
            return 'page/' . array_pop($pathArray);
        }

        return 'page/' . self::ACTION_VIEW;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    private static function explode(string $path): array
    {
        return explode('/', $path);
    }

}