<?php

use app\migrations\MigrationMySQL;
use app\models\Page;

/**
 * Class m180210_112012_init
 */
class m180210_112012_init extends MigrationMySQL
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->dbType === 'mysql') {
            $this->beginUp();

            /**
             * **********************************
             *          ТАБЛИЦА PAGE
             * **********************************
             */
            $this->dropTableIsExists('page');
            $this->beginCreateTable();
            $this->createTable('page', [
                'id'           => $this->primaryKey(11)->unsigned()->comment('Id страницы'),
                'name'         => $this->string(50)->notNull()->unique()->comment('Имя страницы'),
                'title'        => $this->string(255)->notNull()->defaultValue('')->comment('Название страницы'),
                'body'         => $this->text()->notNull()->defaultValue('')->comment('Текст страницы'),
                'path'         => $this->string(1024)->notNull()->defaultValue('')->comment('Путь до ветки'),
                'is_published' => $this->boolean()->notNull()->defaultValue(true)->comment('Флаг публикации страницы'),
                'created_at'   => $this->dateTime()->notNull()->comment('Дата создания страницы'),
                'updated_at'   => $this->dateTime()->comment('Дата обновления страницы'),
            ], $this->getOptions());
            $this->addCommentOnTable('page', 'Страницы');
            $this->endCreateTable();

            /**
             * **********************************
             *     Индексы и внешние ключи
             * **********************************
             */
            $this->setIndex('page', 'path', true);

            $this->endUp();
        } else {
            echo 'The database is not MySQL format, the migration is not possible';
        }

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->dbType === 'mysql') {
            $this->beginDown();
            $this->dropTableIsExists('page');
            $this->endDown();

            return true;
        }

        echo 'The database is not MySQL format, the migration is not possible';

        return false;
    }
}
