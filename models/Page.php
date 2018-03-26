<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\models;

use app\components\PagePath;
use app\models\query\PageQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\HtmlPurifier;

/**
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $body
 * @property string $path
 * @property bool $is_published
 * @property integer $created_at
 * @property integer $updated_at
 */
class Page extends AbstractModel
{
    const COL_ID            = 'id';
    const COL_NAME          = 'name';
    const COL_TITLE         = 'title';
    const COL_BODY          = 'body';
    const COL_PATH          = 'path';
    const COL_IS_PUBLISHED  = 'is_published';
    const COL_IS_CREATED_AT = 'created_at';
    const COL_IS_UPDATED_AT = 'updated_at';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
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
            [[self::COL_PATH], 'string', 'max' => 1024],
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
            self::COL_PATH          => 'Путь',
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
            self::COL_PATH          => 'Путь',
            self::COL_IS_PUBLISHED  => 'Публикация',
            self::COL_IS_CREATED_AT => 'Дата создания',
            self::COL_IS_UPDATED_AT => 'Дата обновления',
        ]);
    }

    public function getParentId(): ?string
    {
        if ($this->path !== null) {
            $path = explode('/', $this->path);
            if (\is_array($path)) {
                $length = \count($path);

                return isset($path[$length - 3]) && $path[$length - 3] !== '' ? $path[$length - 3] : null;
            }
        }

        return null;
    }

    public function setPath(string $parentPath): void
    {
        $this->path = $parentPath . $this->name . '/';
    }

    /**
     * @inheritdoc
     * @return PageQuery активный запрос используемый AR классом.
     */
    public static function find(): PageQuery
    {
        return new PageQuery(static::class);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateName($attribute, $params, $validator): void
    {
        if (!$this->hasErrors()) {
            if (!preg_match('%^([\w]+)$%', $this->name)) {
                $this->addError($attribute, 'Некорректный формат для имени страницы [a-zA-Z0-9_]');
            } elseif (\in_array($this->name, PagePath::$pageAction, true)) {
                $this->addError($attribute,
                    'Нельзя использовать зарезервированные слова: ' . implode(' | ',
                        PagePath::$pageAction));
            }
        }
    }
}