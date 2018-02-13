<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 12.02.18
 */

namespace app\helpers;

use yii\widgets\FragmentCache;

class CacheHelper
{
    /**
     * Тип кеша, для генерации id кеша
     */
    const TYPE_ROUTE = 'route';

    /**
     * @param string $type
     * @param null|string $context
     *
     * @return string
     */
    public static function getId(string $type, string $context = null): string
    {
        return '_cache_' . $type . '_' . ($context ?? '');
    }

    /**
     * Возвращает id для установленный FragmentCache
     *
     * @param string $type
     * @param null|string $context
     *
     * @return array
     */
    public static function getFragmentId(string $type, string $context = null): array
    {
        return [FragmentCache::className(), self::getId($type, $context)];
    }

    /**
     * @param mixed $id
     * @param mixed $content
     * @param int $duration в секундах
     *
     * @see \yii\caching\Cache::set()
     *
     * @return mixed
     */
    public static function set($id, $content, int $duration = 60)
    {
        \Yii::$app->getCache()->set($id, $content, $duration);

        return $content;
    }

    /**
     * @param mixed $id
     *
     * @see \yii\caching\Cache::get()
     *
     * @return mixed|null
     */
    public static function get($id)
    {
        if (\Yii::$app->getCache()->exists($id)) {
            return \Yii::$app->getCache()->get($id);
        }

        return null;
    }

    /**
     * @param mixed $id
     *
     * @see \yii\caching\Cache::delete()
     */
    public static function delete($id): void
    {
        if (\Yii::$app->getCache()->exists($id)) {
            \Yii::$app->getCache()->delete($id);
        }
    }
}