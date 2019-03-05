# sdo-backend

Приложение для взаимодействия с СДО ilias. Позволяет создать файл импорта пользователей для этой СДО.

# Установка

Считаю корневой папкой будущего сайта ".../sdo-backend/"
Для установки потребуются git, composer и yarn
Команды исправить под своё окружение! При необходимости воспользоваться sudo

* ...sdo-backend]$ git clone https://github.com/razikov/sdo-backend.git
* настроить веб-сервер, учитывая https://www.yiiframework.com/doc/guide/2.0/ru/start-installation#configuring-web-servers
* ...sdo-backend]$ composer install
* ...sdo-backend]$ yarn
* ...sdo-backend/config]$ cp db.php _db.php
* ...sdo-backend/config]$ cp db_ilias.php _db_ilias.php
* ...sdo-backend/config]$ cp users.php _users.php
* в скопированные версии внести правки согласно своему окружению

# Обновление

* ...sdo-backend]$ git pull