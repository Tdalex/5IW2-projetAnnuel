To init the project you have to use several commands:

    - `composer install`

    - edit parameter.yml if necessaty

    - `php bin/console doctrine:database:create`

    - `php bin/console doctrine:schema:update --force`

    - if you are in a dev environment use `php bin/console doctrine:fixtures:load`