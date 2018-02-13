<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 11.02.18
 */

namespace app\widgets;

use app\models\Page;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class PageBuildMenu extends Widget
{
    /**
     * @var string
     */
    public $parentAttr = 'parent_id';

    /**
     * @var
     */
    public $valueAttr;

    /**
     * @var
     */
    public $nestedAttr = 'id';

    /**
     * @var \Closure Функция для отображения содержимого
     */
    public $contentCallback;

    /**
     * @var array Элементы дерева
     */
    public $data = [];

    public function init()
    {
        $this->data = Page::find()
            ->queryPages()
            ->orderBy(Page::columnName(Page::COL_NAME))
            ->all();
    }

    public function run()
    {
        try {
            $builder = TreeBuilder::instance($this->data, array(
                'treeTag'            => 'ul',
                'itemTag'            => 'li',
                'parentAttr'         => $this->parentAttr,
                'valueAttr'          => $this->valueAttr,
                'nestedAttr'         => $this->nestedAttr,
                'contentCallback'    => $this->contentCallback,
                'treeHtmlOptions'    => function () {
                    return $this->getTreeOptions();
                },
                'itemHtmlOptions'    => function ($id) {
                    return $this->getItemOptions($id);
                },
                'contentHtmlOptions' => function () {
                    return $this->getContentOptions();
                },
            ));

            return $this->renderTree($builder->buildHtml());

        } catch (\Exception $e) {
            // TODO: логировать
            return '';
        }
    }

    /**
     * Извлекает опции для дерева
     * @return array
     */
    protected function getTreeOptions(): array
    {
        return ['class' => 'tree'];
    }

    /**
     * Извлекает опции для элемента
     *
     * @param int $id id элемента
     *
     * @return array
     */
    protected function getItemOptions($id): array
    {
        return [
            'class'   => 'item',
            'data-id' => $id,
        ];
    }

    /**
     * Извлекает опции для содержимого
     * @return array
     */
    protected function getContentOptions(): array
    {
        return ['class' => ''];
    }

    /**
     * Отображает дерево
     *
     * @param string $tree
     *
     * @return string
     */
    protected function renderTree($tree): string
    {
        $html = Html::beginTag('div', ['id' => $this->id, 'class' => 'dd']);

        $html .= $tree;

        $html .= Html::endTag('div');

        return $html;
    }
}