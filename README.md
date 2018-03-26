<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">MiniWiki</h1>
    <br>
</p>

МиниВики


DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

PHP 7.1.
Nginx 
MySql


INSTALLATION
------------
 
 1) Создайте конфигурационные файлы из копий *.loc
 <br>
 config/db.php (настройте соединение с БД)
 <br>
 config/params.php

2) composer install

3) php yii migrate/up

TODO
-------------

1) Верстка

2) Логирование

3) Тесты
