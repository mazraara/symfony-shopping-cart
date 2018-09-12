Installation Guide

1) Download the source code from git lab

2) Go to the project dir and run below docker commands to get the environment (assuming docker is installed)

`docker-compose build`

`docker-compose up -d`

now phpMyAdmin will be available under http://localhost:8088/

3) Run below command setup the project dependencies

`composer install`

4) Update the parameters.yml in app/config with below values
 
`database_host: 127.0.0.1 
database_port: 3306 
database_name: BookStore 
database_user: book 
database_password: book@123 
mailer_transport: smtp 
mailer_host: smtp.mailtrap.io 
mailer_user: null 
mailer_password: null
secret: 8PWLg7c2U`

5) Run below command to create the tables

`php bin/console doctrine:schema:update –force`

6) Run below command to load the migration scripts 

`php bin/console doctrine:migrations:migrate`

7) Run below command to seed the database 

`php bin/console doctrine:fixtures:load`

8) Finally to load the application run the below command 

`php bin/console server:run`

Now the application should be available under http://localhost:8000/ 
Login username: admin
Login password: p@ssword

9) To run the Test cases (Unit & Functional) run the below commands

Functional > `./vendor/bin/simple-phpunit tests/AppBundle/Controller/ `

Unit > `./vendor/bin/simple-phpunit tests/AppBundle/Utils`

10) if you get any permission issue running on docker, try to clear the cache `php bin/console cache:clear --env=prod`
