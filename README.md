Symfony Test App
===

## About

This Symfony application was built and tested on PHP 7, and it uses an Sqlite database.

## Local/Dev setup

Issue the following console commands from within the application's root directory.

1. Start local server: `php bin/console server:run`.
2. Create database: `php bin/console doctrine:database:create`.
3. Create schema: `php bin/console doctrine:schema:create`.
4. Load users using custom *Console* command: `php bin/console app:import-users user-data.csv`. Wait until shell prompt returns and *updated* vs *create* user count is indicated.
5. Visit website at `http://127.0.0.1:8000`. Log in using an imported user's *email* and *first name* as *password*.

## Production setup

Note: You might have to prefix some Symfony commands with `SYMFONY_ENV=prod` if you run into errors. Likewise starting the Symfony server in production also requires the prefix.

1. Upload source code to server.
2. Install framework requirements `SYMFONY_ENV=prod composer install --no-dev --optimize-autoloader`. Optionally check Symfony requirements `php bin/symfony_requirements`. More information [here](http://symfony.com/doc/current/deployment/tools.html).
3. Clear cache: `php bin/console cache:clear --env=prod --no-debug`.
4. Create DB if it does not yet exist: `php bin/console doctrine:database:create`.
5. Create or update DB schema: `php bin/console doctrine:schema:create` or `php bin/console doctrine:schema:update`.
6. Load users using custom *Console* command: `php bin/console app:import-users user-data.csv`. Wait until shell prompt returns and *updated* vs *create* user count is indicated.
