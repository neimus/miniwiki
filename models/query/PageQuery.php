<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 11.02.2018
 */

namespace app\models\query;

use app\models\Page;
use app\models\PageRel;
use yii\db\ActiveQuery;
use yii\db\Query;

class PageQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return Page[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Page|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $parentId
     *
     * @return ActiveQuery
     */
    public function queryChildren($parentId): ActiveQuery
    {
        $subQuery = Page::find()->from(Page::tableName())
            ->select(PageRel::columnName(PageRel::COL_CHILD_ID))
            ->leftJoin(PageRel::tableName(),
                PageRel::columnName(PageRel::COL_PAGE_ID) . ' = ' . Page::columnName(Page::COL_ID)
            )
            ->where(['=', PageRel::columnName(PageRel::COL_PAGE_ID), $parentId])
            ->groupBy(PageRel::columnName(PageRel::COL_CHILD_ID));

        return $this->from(Page::tableName())
            ->where(['IN', Page::columnName(Page::COL_ID), $subQuery]);
    }

    /**
     * @param $id
     *
     * @return ActiveQuery
     */
    public function queryParent($id): ActiveQuery
    {
        return $this->from(Page::tableName())
            ->leftJoin(PageRel::tableName(),
                PageRel::columnName(PageRel::COL_PAGE_ID) . ' = ' . Page::columnName(Page::COL_ID)
            )
            ->where(['=', PageRel::columnName(PageRel::COL_PAGE_ID), $id])
            ->groupBy(PageRel::columnName(PageRel::COL_PARENT_ID));
    }

    /**
     * @return Query
     */
    public function queryPages(): Query
    {
        $selectColumns = [
            Page::columnName(Page::COL_ID),
            Page::columnName(Page::COL_NAME),
            Page::columnName(Page::COL_TITLE),
            Page::columnName(Page::COL_BODY),
            Page::columnName(Page::COL_IS_PUBLISHED),
            PageRel::columnName(PageRel::COL_PAGE_ID),
            PageRel::columnName(PageRel::COL_PARENT_ID),
            PageRel::columnName(PageRel::COL_CHILD_ID),
        ];

        return (new Query())
            ->select($selectColumns)
            ->from(Page::tableName())
            ->leftJoin(PageRel::tableName(),
                PageRel::columnName(PageRel::COL_PAGE_ID) . ' = ' . Page::columnName(Page::COL_ID)
            );
    }
}
