<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\models;

use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * @property string $modelErrors
 * @property Model self
 */
abstract class AbstractModel extends ActiveRecord
{
    public static function columnName(string $name): string
    {
        return self::tableName() . '.' . $name;
    }

    /**
     * Возвращает placeholder модели
     *
     * @param $attribute - атрибут модели
     *
     * @return string
     */
    public function getPlaceholder(string $attribute): string
    {
        return $this->attributeLabels()[$attribute];
    }

    /**
     * Возвращает placeholder модели
     *
     * @param $attribute - атрибут модели
     *
     * @return string
     */
    public function getHint(string $attribute): string
    {
        return $this->attributeHints()[$attribute];
    }

    /**
     * Присвоение атрибутов, которые отсутствуют в rules
     *
     * @param string $name
     * @param mixed $value
     */
    public function onUnsafeAttribute($name, $value): void
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }

    /**
     * Возвращает все ошибки в модели
     *
     * @return string
     */
    public function getModelErrors(): string
    {
        $message = '';
        $attributeErrors = $this->getErrors();
        /** @var array $attributeErrors */
        foreach ($attributeErrors as $attribute => $errors) {
            $message .= $attribute . ': ' . implode(',', $errors);
        }

        return $message;
    }
}