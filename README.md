URL prod : http://roadtripeurs.devprod.fr/

To init the project you have to:

if are in developpment environement start with :

installing vagrant

```
cd {base_dir}/puphpet
vagrant up
vagrant ssh
cd /var/www
```
on all environnement do:

run ```composer install```

edit parameter.yml if necessary and run

if elasticsearch is not install do

``` sh {base_dir}/puphpet/files/exec-once/install-elasticsearch.sh ```

then

```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console f:e:p
php bin/console roadtrip:waypoint:update
```

if you are in a developpment environment use ```php bin/console doctrine:fixtures:load```


