UrbanPotager
====

Projet université LPDW 

API pour UrbanPotager

Installation
====

Pré-requis
---
* Serveur Ubuntu
* Apache ou nginx
* PHP 5.5 ou mieux (5.6 pour pouvoir lancer les tests fonctionnels)
* MySQL ou MariaDB
* Git


Étapes
---
* git clone https://github.com/lpdw/UrbanPotagerApi.git
* installation de composer : https://getcomposer.org/download/
* SYMFONY_ENV=prod && composer update --no-dev
* php bin/console d:d:c
* php bin/console d:s:u --force
* Création du vhost (selon si nginx ou apache en serveur web) http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
* Importer urbanpotager-types.sql dans la base de données
	* en cli : mysql -u :db_username: -p :database_name: < urbanpotager-types.sql
	* ou via PhpMyAdmin
	* ou via MysqlWorkbench

