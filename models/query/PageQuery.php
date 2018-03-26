<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 11.02.2018
 */

namespace app\models\query;

use app\models\Page;
use yii\db\ActiveQuery;

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

    public function getPageByPath($path)
    {
        return $this
            ->where(['=', Page::columnName(Page::COL_PATH), $path])
            ->one();
    }

    public function getPagesForMenu()
    {
        return $this->orderBy(Page::columnName(Page::COL_PATH))
            ->indexBy(Page::COL_NAME)
            ->all();
    }
}
