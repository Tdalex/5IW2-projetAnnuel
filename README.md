To init the project you have to:

run ```composer install```

edit parameter.yml if necessary and  run
```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```
if you are in a dev environment use ```php bin/console doctrine:fixtures:load```
