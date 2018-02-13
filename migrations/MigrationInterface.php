<?php
/**
 * Created by PhpStorm.
 * User: Saburov Denis
 * Date: 10.02.18
 */

namespace app\migrations;

interface MigrationInterface
{
    /**
     * @return string|null
     */
    public function getOptions();

    public function beginCreateTable(): void;

    public function endCreateTable(): void;

    public function beginUp(): void;

    public function endUp(): void;

    public function beginDown(): void;

    public function endDown(): void;
}