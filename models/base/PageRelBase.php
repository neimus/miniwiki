<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\models\base;

use app\models\ModelTrait;
use app\models\Page;
use app\models\query\PageRelQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property Page $page_id
 * @property Page $parent_id
 * @property Page[] $child_id
 */
class PageRelBase extends ActiveRecord
{
    use ModelTrait;

    const COL_ID        = 'id';
    const COL_PAGE_ID   = 'page_id';
    const COL_PARENT_ID = 'parent_id';
    const COL_CHILD_ID  = 'child_id';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[self::COL_PAGE_ID, self::COL_PARENT_ID, self::COL_CHILD_ID], 'integer',],
            [[self::COL_PAGE_ID], 'required'],
        ];
    }

    /**
     * @inheritdoc
     * @return PageRelQuery активный запрос используемый AR классом.
     */
    public static function find(): PageRelQuery
    {
        return new PageRelQuery(static::class);
    }

    /**
     * @inheritdoc
     * @return PageRelQuery активный запрос используемый AR классом.
     */
    public static function command(): PageRelQuery
    {
        return self::find();
    }

    public function queryPage(): ActiveQuery
    {
        return $this->hasOne(Page::className(), [Page::COL_ID => self::COL_PAGE_ID]);
    }

    public function queryChildren(): ActiveQuery
    {
        return $this->hasMany(Page::className(), [Page::COL_ID => self::COL_CHILD_ID]);
    }

    public function queryParent(): ActiveQuery
    {
        return $this->hasOne(Page::className(), [Page::COL_ID => self::COL_PARENT_ID]);
    }
}