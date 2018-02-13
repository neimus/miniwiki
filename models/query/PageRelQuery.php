<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 11.02.2018
 */

namespace app\models\query;

use app\models\PageRel;
use LogicException;
use yii\db\ActiveQuery;

class PageRelQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return PageRel[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PageRel|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $pageId
     * @param null $parentId
     *
     * @return bool
     */
    public function querySetParent($pageId, $parentId = null): bool
    {
        if (!empty($parentId)) {
            $pageRel = $this->from(PageRel::tableName())
                ->where(['=', PageRel::COL_PAGE_ID, $pageId])
                ->count();
            if ($pageRel > 0) {
                return (bool)PageRel::updateAll([PageRel::COL_PARENT_ID => $parentId],
                    ['=', PageRel::COL_PAGE_ID, $pageId]);
            }
        }

        $pageRel = new PageRel();
        $pageRel->page_id = $pageId;
        $pageRel->parent_id = $parentId;

        return $pageRel->save();
    }

    /**
     * @param $pageId
     * @param $childId
     *
     * @return bool
     * @throws \LogicException если модель $pageId не была найдена
     */
    public function queryAddChild($pageId, $childId): bool
    {
        $childPageRel = $this->from(PageRel::tableName())
            ->where(['=', PageRel::COL_PAGE_ID, $pageId])
            ->andWhere(['IS', PageRel::COL_CHILD_ID, null])
            ->one();
        if ($childPageRel === null) {
            $pageRel = $this->from(PageRel::tableName())
                ->where(['=', PageRel::COL_PAGE_ID, $pageId])
                ->one();
            if ($pageRel === null) {
                throw new LogicException("Model [PageRel] with id [$pageId] not found");
            }
            $childPageRel = new PageRel();
            $childPageRel->page_id = $pageId;
            $childPageRel->parent_id = $pageRel->parent_id;
        }

        $childPageRel->child_id = $childId;

        return $childPageRel->save();
    }
}
