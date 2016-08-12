Symfony Test App
===

## About

This Symfony application was built and tested on PHP 7, and it uses an Sqlite database.

## Local/Dev setup

Issue the following console commands from within the application's root directory.

1. Start local server: `php bin/console server:run`.
2. Create database: `php bin/console doctrine:database:create`.
3. Create schema: `php bin/console doctrine:schema:create`.
4. Load users using custom *Console* command: `php bin/console app:import-users <FILE PATH>/user-data.csv`. Wait until shell prompt returns and *updated* vs *create* user count is indicated.
5. Visit website at `http://127.0.0.1:8000`. Log in using an imported user's *email* and *first name* as *password*.

## Production setup

Note: You might have to prefix some Symfony commands with `SYMFONY_ENV=prod` if you run into errors. Or add the environment variable to your shell profile `export SYMFONY_ENV=prod`. Likewise starting the Symfony server in production also requires the prefix.

1. Upload source code to server.
2. Optionally check Symfony requirements `php bin/symfony_requirements`.
3. Install framework requirements `composer install --no-dev --optimize-autoloader`. More information [here](http://symfony.com/doc/current/deployment/tools.html).
4. Clear cache: `php bin/console cache:clear --env=prod --no-debug`.
5. Create DB if it does not yet exist: `php bin/console doctrine:database:create`.
6. Update DB schema: `php bin/console doctrine:schema:update`.
7. Load users using custom *Console* command: `php bin/console app:import-users <FILE PATH>/user-data.csv`. Wait until shell prompt returns and *updated* vs *create* user count is indicated.

### Apache on Ubuntu setup

Note: these are setup instructions for a Ubuntu Linux web-server assuming that you have already installed Apache2 and PHP 5+ and Sqlite.

#### Requirements
1. Apache2
2. PHP 7+
3. Sqlite
4. Composer


1. Upload/Clone application repository to your server and copy the project directory to `/srv/www/`, or wherever you server your Apache2 sites from.
2. Create entry in `/etc/apache2/sites-available/` directory for this application:
```
<VirtualHost *:80>
    ServerName symfony3test.specificidea.com
    ServerAlias symfony3test.specificidea.com

    DocumentRoot /srv/www/symfony-test/web
    <Directory /srv/www/symfony-test/web>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

    ErrorLog /var/log/apache2/symfony_test_error.log
    CustomLog /var/log/apache2/symfony_test_access.log combined
</VirtualHost>
```
I named mine `symfony3test`, and am using a subdomain for this application.
3. Enable this configuration: `a2ensite symfony3test`.
4. You might need to reload Apache: `service apache2 reload`.

## Misc.

Sample CSV file and Sqlite db are located in `app/data/`.
