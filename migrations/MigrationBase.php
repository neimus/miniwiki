<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\migrations;

use yii\db\Migration;

/**
 *
 * @property string $index
 * @property string $foreignKey
 */
abstract class MigrationBase extends Migration implements MigrationInterface
{
    /**
     * @var string Тип текущей БД
     */
    public $dbType = '';

    /**
     * @var array Массив уже существующих таблиц в БД
     */
    public $tables = array();

    /**
     * @var string Перфикс для индексов и внешних ключей
     */
    public $indexPrefix = 'fk_';

    public function init()
    {
        parent::init();
        $this->tables = \Yii::$app->db->schema->getTableNames();
        $this->dbType = $this->db->driverName;
    }

    /**
     * Создание индекса и генерация его имени
     *
     * @param string $table Наименование таблицы
     * @param array|string $columns Наименование колонок
     * @param bool $unique Уникальность индекса
     */
    protected function setIndex(string $table, $columns, bool $unique = false): void
    {
        $columnName = $this->getColumnName($columns);
        $name = $unique ? $columnName . '_UNIQUE' : $this->indexPrefix . $table . '__' . $columnName . '_idx';

        $this->createIndex($name, $table, $columns, $unique);
    }

    /**
     * Добавление внешнего ключа
     *
     * @param string $table Наименование таблицы
     * @param string|array $columns Наименование колонок
     * @param string $refTable Наименование таблицы для связи
     * @param string|array $refColumns Наименование колонок для связи
     * @param string|null $onDelete Действие при удалении связанных данных
     * @param string|null $onUpdate Действие при обновлении связанных данных
     */
    protected function setForeignKey(
        string $table,
        $columns,
        string $refTable,
        $refColumns,
        string $onDelete = null,
        string $onUpdate = null
    ): void {
        $name = $this->indexPrefix . $table . '__' . $this->getColumnName($columns);
        $this->addForeignKey($name, $table, $columns, $refTable, $refColumns, $onDelete, $onUpdate);
    }

    /**
     * Удаляет таблицу, если она существует в схеме БД
     *
     * @param string $tableName Наименование таблицы
     */
    protected function dropTableIsExists(string $tableName): void
    {
        if (\in_array($tableName, $this->tables, true)) {
            $this->dropTable($tableName);
        }
    }

    /**
     * @param array|string $columns
     *
     * @return string
     */
    private function getColumnName($columns): string
    {
        return \is_array($columns) ? implode('_', $columns) : $columns;
    }
}