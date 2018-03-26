<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 11.02.18
 */

namespace app\widgets;

use Closure;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class TreeBuilder
{
    public $idAttr = 'id';

    /**
     * @var string
     */
    public $parentAttr = 'parent_id';

    /**
     * @var Closure|null
     */
    public $parentCallback;

    /**
     * @var string
     */
    public $valueAttr;

    /**
     * @var string тег для дерева
     */
    public $treeTag = 'ul';

    /**
     * @var string тег для элемента в дереве
     */
    public $itemTag = 'li';

    /**
     * @var string Содержимое тега
     */
    public $contentTag = '';

    /**
     * @var Closure Функция генерирующая параметры для всего дерева
     */
    public $treeHtmlOptions;

    /**
     * @var Closure Функция генерирующая параметры для элементов дерева
     */
    public $itemHtmlOptions;

    /**
     * @var Closure Функция генерирующая параметры для содержимого
     */
    public $contentHtmlOptions;

    /**
     * @var Closure Функция генерирующая содержимое дерева
     */
    public $contentCallback;

    /**
     * @var array элементы дерева
     */
    private $_items = [];


    /**
     *
     * @param array $items элементы дерева
     *
     * @param array $params дополнительные параметры
     *
     * @throws \Exception
     */
    private function __construct($items, array $params = array())
    {
        $this->_items = $items;

        $this->setParams($params);
    }

    /**
     * Возвращает новый объект TreeBuilder
     *
     * @param array $items элементы дерева
     * @param array $params
     *
     * @return \app\widgets\TreeBuilder
     *
     * @throws \Exception
     */
    public static function instance($items, array $params = array()): TreeBuilder
    {
        return new self($items, $params);
    }

    /**
     * Строит дерево элементов
     *
     * @throws \yii\base\InvalidParamException
     */
    public function buildHtml(): string
    {
        return $this->treeToHtml($this->getTree());
    }

    /**
     * @return array
     *
     * @throws \yii\base\InvalidParamException
     */
    public function buildArray(): array
    {
        return $this->getTree();
    }

    /**
     * Задает параметры для класса
     *
     * @param array $params параметры
     *
     * @throws \Exception Если параметры не будут найдены
     */
    public function setParams($params)
    {
        foreach ($params as $param => $value) {
            if (!property_exists($this, $param)) {
                throw new \RuntimeException('У класса ' . \get_class($this) . ' нет параметров: ' . $param);
            }

            $this->{$param} = $value;
        }
    }

    /**
     * Формирует дерево вида:
     * ```
     *      [
     *          'id' => 1,
     *          'children' => [
     *                  'id' => 2,
     *                  'children' => [],
     *                  'value' => 'Содержимое 2'
     *              ],
     *          'value' => 'Содержимое 1',
     *      ]
     * где значение value, данные
     *```
     *
     * @return array
     *
     * @throws \yii\base\InvalidParamException
     */
    public function getTree(): array
    {
        $data = ArrayHelper::map($this->_items,
            function ($array, $default) {
                if ($array instanceof ActiveRecord && $array->hasAttribute($this->idAttr)) {
                    return $array->{$this->idAttr};
                }

                if (\is_array($array) && isset($array[$this->idAttr])) {
                    return $array[$this->idAttr];
                }

                $this->throwInvalidParams();
            },
            function ($array, $default) {
                $id = null;
                $parent_id = null;
                if ($array instanceof ActiveRecord && $array->hasAttribute($this->idAttr)) {
                    $id = $array->{$this->idAttr};
                    if ($this->parentCallback !== null) {
                        $parent_id = $array->{$this->parentCallback}();
                    } else {
                        $parent_id = $array->hasAttribute($this->parentAttr) ? $array->{$this->parentAttr} : null;
                    }
                } elseif (\is_array($array) && isset($array[$this->idAttr])) {
                    $id = $array[$this->idAttr];
                    $parent_id = $array[$this->parentAttr] ?? null;
                } else {
                    $this->throwInvalidParams();
                }

                return ['value' => $array, $this->parentAttr => $parent_id, 'id' => $id];
            }
        );

        // Формирование дерева
        $tree = [];
        $references = [];

        foreach ($data as $id => &$node) {
            $node['children'] = $references[$node['id']]['children'] ?? [];
            $references[$node['id']] = &$node;

            if (null !== $node[$this->parentAttr] && isset($data[$node[$this->parentAttr]])) {
                $references[$node[$this->parentAttr]]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }

        return $tree;
    }

    /**
     * Преобразование массива дерева в html код
     *
     * @param array $tree массив дерева, например:
     * ```
     *      [
     *          'id' => 1,
     *          'children' => [
     *                  'id' => 2,
     *                  'children' => [],
     *                  'value' => 'Содержимое 2'
     *              ],
     *          'value' => 'Содержимое 1',
     *      ]
     * где значение value, может быть строкой или функцией пользователя
     * ```
     *
     * @return string
     */
    private function treeToHtml($tree): string
    {
        $html = '';
        foreach ($tree as $item) {
            $value = $item['value'];

            $html .= $this->renderTreeOpen();
            $html .= $this->renderItemOpen($item['id']);

            if (\is_callable($this->contentCallback)) {
                $html .= $this->renderContent(\call_user_func($this->contentCallback, $item['value']));
            } else {

                if ($this->valueAttr !== null && $value instanceof ActiveRecord) {
                    if ($value->hasAttribute($this->valueAttr)) {
                        $value = $value->{$this->valueAttr};
                    }
                }
                $html .= $this->renderContent($value);
            }
            if (isset($item['children']) && !empty($item['children'])) {
                $html .= $this->treeToHtml($item['children']);
            }

            $html .= $this->renderItemClose();
            $html .= $this->renderTreeClose();
        }

        return $html;
    }

    /**
     * Отображает тег для открытия дерева
     *
     * @return string html
     */
    protected function renderTreeOpen(): string
    {
        $options = $this->getHtmlOptions($this->treeHtmlOptions);

        return Html::beginTag($this->treeTag, $options);
    }

    /**
     * Отображает тег для открытия элемента
     *
     * @param $id
     *
     * @return string html
     */
    protected function renderItemOpen($id): string
    {
        $options = $this->getHtmlOptions($this->itemHtmlOptions, $id);

        return Html::beginTag($this->itemTag, $options);
    }

    /**
     * Отображает содержимое
     *
     * @param $content
     *
     * @return string
     */
    protected function renderContent($content): string
    {
        if ($this->contentTag) {
            $options = $this->getHtmlOptions($this->contentHtmlOptions);

            return Html::tag($this->contentTag, $options, $content);
        }

        return $content;
    }

    /**
     * Отображает тег для закрытия элемента
     *
     * @return string html
     */
    protected function renderItemClose(): string
    {
        return Html::endTag($this->itemTag);
    }

    /**
     * Отображает тег для закрытия дерева
     *
     * @return string html
     */
    protected function renderTreeClose(): string
    {
        return Html::endTag($this->treeTag);
    }

    /**
     * Извлекает параметры для заданного свойства
     *
     * @param $property
     * @param null|string $attr
     *
     * @return array html options
     */
    protected function getHtmlOptions($property, $attr = null): array
    {
        if (\is_callable($property)) {
            return (array)$property($attr);
        }

        return (array)$property;
    }

    /**
     * @throws \yii\base\InvalidParamException
     */
    private function throwInvalidParams()
    {
        throw new InvalidParamException('Свойство items, должно быть массивом содержащим уникальный ключ ["id"]
                 или экземпляром класса ActiveRecord со свойством [id]');
    }

}