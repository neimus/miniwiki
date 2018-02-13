<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\models;

use app\models\base\PageBase;
use yii\db\ActiveRecord;

/**
 *
 * @property ActiveRecord|null|Page $parent
 * @property Page[]|array|ActiveRecord[] $children
 */
class Page extends PageBase
{
    /**
     * @return Page[]|array|ActiveRecord[]
     */
    public function getChildren(): array
    {
        return $this->queryChildren()->all();
    }

    /**
     * @param $childId
     *
     * @return bool
     */
    public function addChild($childId): bool
    {
        try {
            return PageRel::command()->queryAddChild($this->id, $childId);
        } catch (\LogicException $exception) {
            return false;
        }
    }

    /**
     * @return null|Page|ActiveRecord
     */
    public function getParent()
    {
        return $this->queryParent()->one();
    }

    public function setParent($parentId = null): bool
    {
        try {
            return PageRel::command()->querySetParent($this->id, $parentId);
        } catch (\LogicException $exception) {
            return false;
        }
    }

    public function issetParent(): bool
    {
        return (int)$this->queryPageRel()
                ->where(['NOT', [PageRel::columnName(PageRel::COL_PARENT_ID) => null]])
                ->count() > 0;
    }

    /**
     * @param $name
     *
     * @return Page|null|ActiveRecord
     */
    public function getChildrenByName($name)
    {
        return $this->queryChildren()
            ->andWhere(['=', self::columnName(self::COL_NAME), $name])
            ->one();
    }
}