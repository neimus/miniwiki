<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\models\base;

use app\components\PageUrlRule;
use app\models\ModelTrait;
use app\models\PageRel;
use app\models\query\PageQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;

/**
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $body
 * @property bool $is_published
 * @property integer $created_at
 * @property integer $updated_at
 */
class PageBase extends ActiveRecord
{
    use ModelTrait;

    const COL_ID            = 'id';
    const COL_NAME          = 'name';
    const COL_TITLE         = 'title';
    const COL_BODY          = 'body';
    const COL_IS_PUBLISHED  = 'is_published';
    const COL_IS_CREATED_AT = 'created_at';
    const COL_IS_UPDATED_AT = 'updated_at';

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->is_published = true;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return
            [
                [
                    'class' => TimestampBehavior::className(),
                    'value' => function () {
                        return date('Y-m-d H:i:s', time());
                    },
                ],
            ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[self::COL_NAME, self::COL_TITLE, self::COL_BODY], 'required'],
            [[self::COL_NAME], 'unique'],
            [[self::COL_NAME], 'trim'],
            [[self::COL_ID,], 'integer',],
            [[self::COL_IS_PUBLISHED], 'boolean'],
            [[self::COL_NAME], 'string', 'max' => 45, 'min' => 3],
            [[self::COL_TITLE], 'string', 'max' => 255, 'min' => 3],
            [[self::COL_BODY], 'string', 'min' => 3],
            [
                [self::COL_BODY],
                'filter',
                'filter' => function ($value) {
                    return HtmlPurifier::process($value);
                },
            ],
            [[self::COL_NAME], 'validateName'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            self::COL_ID            => 'ID',
            self::COL_NAME          => 'Наименование',
            self::COL_TITLE         => 'Заголовок',
            self::COL_BODY          => 'Текст',
            self::COL_IS_PUBLISHED  => 'Опубликовано',
            self::COL_IS_CREATED_AT => 'Дата создания',
            self::COL_IS_UPDATED_AT => 'Дата обновления',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints(): array
    {
        return array_merge(parent::attributeHints(), [
            self::COL_ID            => 'ID',
            self::COL_NAME          => 'Наименование страницы латинскими буквами, цифрами или нижним подчеркиванием',
            self::COL_TITLE         => 'Заголовок (будет виден в меню и в статье)',
            self::COL_BODY          => "Текст статьи. Вики-разметка: <br>**строка** - жирное выделене; 
<br>\\\\строка\\\\ - курсивное выделение; <br>((name1/name2/name3 заголовок])) - url на статью",
            self::COL_IS_PUBLISHED  => 'Публикация',
            self::COL_IS_CREATED_AT => 'Дата создания',
            self::COL_IS_UPDATED_AT => 'Дата обновления',
        ]);
    }

    /**
     * @inheritdoc
     * @return PageQuery активный запрос используемый AR классом.
     */
    public static function find(): PageQuery
    {
        return new PageQuery(static::class);
    }

    /**
     * @return ActiveQuery
     */
    public function queryPageRel(): ActiveQuery
    {
        return $this->hasMany(PageRel::className(), [PageRel::COL_PAGE_ID => self::COL_ID]);
    }

    /**
     * Запрос всех подстраниц
     *
     * @return ActiveQuery
     */
    public function queryChildren(): ActiveQuery
    {
        return self::find()->queryChildren($this->id);
    }

    /**
     * Запрос родителя
     *
     * @return ActiveQuery
     */
    public function queryParent(): ActiveQuery
    {
        return self::find()->queryParent($this->id);
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateName($attribute, $params, $validator)
    {
        if (!$this->hasErrors()) {
            if (!preg_match('%^([\w]+)$%', $this->name)) {
                $this->addError($attribute, 'Некорректный формат для имени страницы [a-zA-Z0-9_]');
            } elseif (\in_array($this->name, PageUrlRule::$pageAction, true)) {
                $this->addError($attribute,
                    'Нельзя использовать зарезервированные слова: ' . implode(' | ',
                        PageUrlRule::$pageAction));
            }
        }
    }
}