<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\migrations;

/**
 * @property string $options
 */
class MigrationMySQL extends MigrationBase
{
    public function getOptions()
    {
        return "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB\n";
    }

    /**
     *  При создании таблицы, сохраняет текущую кодировку и изменяет ее на UTF8
     */
    public function beginCreateTable(): void
    {
        $this->execute('SET @saved_cs_client = @@character_set_client');
        $this->execute('SET character_set_client = utf8');
    }

    /**
     * Восстанавливает сохраненную кодировку после создания таблицы
     */
    public function endCreateTable(): void
    {
        $this->execute('SET character_set_client = @saved_cs_client');
    }

    /**
     *  Устанавливает переменные
     */
    public function beginUp(): void
    {
        $this->execute('SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT');
        $this->execute('SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS');
        $this->execute('SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION');
        $this->execute('SET NAMES utf8');
        $this->execute('SET @OLD_TIME_ZONE=@@TIME_ZONE');
        $this->execute("SET TIME_ZONE='+00:00'");
        $this->execute('SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0');
        $this->execute('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0');
        $this->execute("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
        $this->execute('SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0');
    }

    /**
     *  Восстанавливает переменные по умолчанию
     */
    public function endUp(): void
    {
        $this->execute('SET TIME_ZONE=@OLD_TIME_ZONE');
        $this->execute('SET SQL_MODE=@OLD_SQL_MODE');
        $this->execute('SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS');
        $this->execute('SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS');
        $this->execute('SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT');
        $this->execute('SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS');
        $this->execute('SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION');
        $this->execute('SET SQL_NOTES=@OLD_SQL_NOTES');
    }

    /**
     *  Устанавливает переменные
     */
    public function beginDown(): void
    {
        $this->execute('SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0');
        $this->execute('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0');
        $this->execute("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';");
    }

    /**
     *  Восстанавливает переменные по умолчанию
     */
    public function endDown(): void
    {
        $this->execute('SET SQL_MODE=@OLD_SQL_MODE;');
        $this->execute('SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS');
        $this->execute('SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS');
    }
}