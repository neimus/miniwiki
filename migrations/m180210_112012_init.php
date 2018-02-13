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
            $this->createTable(Page::tableName(), [
                'id'           => $this->primaryKey(11)->unsigned()->comment('Id страницы'),
                'name'         => $this->string(50)->notNull()->unique()->comment('Имя страницы'),
                'title'        => $this->string(255)->notNull()->defaultValue('')->comment('Название страницы'),
                'body'         => $this->text()->notNull()->defaultValue('')->comment('Текст страницы'),
                'is_published' => $this->boolean()->notNull()->defaultValue(true)->comment('Флаг публикации страницы'),
                'created_at'   => $this->dateTime()->notNull()->comment('Дата создания страницы'),
                'updated_at'   => $this->dateTime()->comment('Дата обновления страницы'),
            ], $this->getOptions());
            $this->addCommentOnTable('page', 'Страницы');
            $this->endCreateTable();

            /**
             * **********************************
             *     ТАБЛИЦА PAGE_REL
             * **********************************
             */
            $this->dropTableIsExists('page_rel');
            $this->beginCreateTable();
            $this->createTable('page_rel', [
                'id'        => $this->primaryKey(11)->unsigned()->comment('Id'),
                'page_id'   => $this->integer(11)->notNull()->unsigned()->comment('ID страницы'),
                'parent_id' => $this->integer(11)->unsigned()->comment('ID родителя страницы'),
                'child_id'  => $this->integer(11)->unsigned()->unique()->comment('ID подстраницы'),

            ], $this->getOptions());
            $this->addCommentOnTable('page', 'Связи страниц родителя и подстраницы');
            $this->endCreateTable();

            /**
             * **********************************
             *     Индексы и внешние ключи
             * **********************************
             */
            $this->setIndex('page_rel', 'page_id');
            $this->setIndex('page_rel', 'parent_id');
            $this->setIndex('page_rel', ['page_id', 'parent_id']);
            $this->setIndex('page_rel', ['page_id', 'child_id'], true);
            $this->setIndex('page_rel', ['page_id', 'parent_id', 'child_id'], true);

            $this->setForeignKey('page_rel', 'parent_id', 'page', 'id', 'RESTRICT');
            $this->setForeignKey('page_rel', 'child_id', 'page', 'id', 'RESTRICT');
            $this->setForeignKey('page_rel', 'page_id', 'page', 'id', 'CASCADE');

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
            $this->dropTableIsExists('page_id');
            $this->dropTableIsExists('page');
            $this->endDown();

            return true;
        }

        echo 'The database is not MySQL format, the migration is not possible';

        return false;
    }
}
