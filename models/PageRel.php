<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\models;

use app\models\base\PageRelBase;

class PageRel extends PageRelBase
{
    /**
     * @return \app\models\Page|null|\yii\db\ActiveRecord
     */
    public function getPage()
    {
        return $this->queryPage()->one();
    }

    /**
     * @return \app\models\Page|null|\yii\db\ActiveRecord
     */
    public function getParent()
    {
        return $this->queryParent()->one();
    }

    /**
     * @return \app\models\Page[]|null|\yii\db\ActiveRecord
     */
    public function getChildren()
    {
        return $this->queryChildren()->all();
    }
}