# Bagisto

<p style="text-align: center">
<a href="http://www.bagisto.com"><img src="https://bagisto.com/wp-content/themes/bagisto/images/logo.png" alt="Total Downloads"></a>
</p>

<p style="text-align: center">
<a href="https://packagist.org/packages/bagisto/bagisto"><img src="https://poser.pugx.org/bagisto/bagisto/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/bagisto/bagisto"><img src="https://poser.pugx.org/bagisto/bagisto/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/bagisto/bagisto"><img src="https://poser.pugx.org/bagisto/bagisto/license.svg" alt="License"></a>
<a href="https://github.com/bagisto/bagisto/actions"><img src="https://github.com/bagisto/bagisto/workflows/CI/badge.svg" alt="Backers on Open Collective"></a>
<a href="#backers"><img src="https://opencollective.com/bagisto/backers/badge.svg" alt="Backers on Open Collective"></a>
<a href="#sponsors"><img src="https://opencollective.com/bagisto/sponsors/badge.svg" alt="Sponsors on Open Collective"></a>
</p>

## Topics

1. [Introduction](#introduction)
2. [Documentation](#documentation)
3. [Requirements](#requirements)
4. [Installation & Configuration](#installation-and-configuration)
5. [License](#license)
6. [Security Vulnerabilities](#security-vulnerabilities)
7. [Miscellaneous](#miscellaneous)

### Introduction

[Bagisto](https://www.bagisto.com) is a hand tailored E-Commerce framework built on some of the hottest opensource technologies
such as [Laravel](https://laravel.com) (a [PHP](https://secure.php.net/) framework) and [Vue.js](https://vuejs.org)
a progressive Javascript framework.

**Bagisto can help you to cut down your time, cost, and workforce for building online stores or migrating from physical stores
to the ever demanding online world. Your business -- whether small or huge -- can benefit. And it's very simple to set it up.**

**Read our documentation: [Bagisto Docs](https://devdocs.bagisto.com/)**

**We also have a forum for any type of concerns, feature requests, or discussions. Please visit: [Bagisto Forums](https://forums.bagisto.com/)**

## Visit our live [Demo](https://demo.bagisto.com)

It packs in lots of features that will allow your E-Commerce business to scale in no time:

* Multiple Channels, Locale, Currencies.
* Built-in Access Control Layer.
* Beautiful and Responsive Storefront.
* Descriptive and Simple Admin Panel.
* Admin Dashboard.
* Custom Attributes.
* Built on Modular Approach.
* Support for Multiple Store Themes.
* Multistore Inventory System.
* Orders Management System.
* Customer Cart, Wishlist, Product Reviews.
* Simple, Configurable, Group, Bundle, Downloadable and Virtual Products.
* Price rules (Discount) inbuilt.
* Theme (Velocity).
* CMS Pages.
* Check out [these features and more](https://bagisto.com/features/).

**For Developers**:
Take advantage of two of the hottest frameworks used in this project -- Laravel and Vue.js -- both of which have been used in Bagisto.

### Documentation

#### Bagisto Documentation [https://devdocs.bagisto.com](https://devdocs.bagisto.com)

### Requirements

* **OS**: Ubuntu 16.04 LTS or higher / Windows 7 or Higher (WampServer / XAMPP).
* **SERVER**: Apache 2 or NGINX.
* **RAM**: 3 GB or higher.
* **PHP**: 7.2.0 or higher.
* **Processor**: Clock Cycle 1 Ghz or higher.
* **For MySQL users**: 5.7.23 or higher.
* **For MariaDB users**: 10.2.7 or Higher.
* **Node**: 8.11.3 LTS or higher.
* **Composer**: 1.6.5 or higher.

### Installation and Configuration

**1. You can install Bagisto by using the GUI installer.**

#### a. Download zip from the link below

[Download the latest release](https://github.com/bagisto/bagisto/releases/latest)

#### b. Extract the contents of zip and execute the project in your browser

```text
http(s)://localhost/bagisto/public
```

or

```text
http(s)://example.com/public
```

**2. Or you can install Bagisto from your console.**

##### Execute these commands below, in order

```bash
# Command #1
composer create-project bagisto/bagisto-standard

# Command #2
php artisan bagisto:install
```

**To execute Bagisto**:

##### On server

Warning: Before going into production mode we recommend you uninstall developer dependencies.
In order to do that, run the command below:

```bash
composer install --no-dev
```

> Open the specified entry point in your hosts file in your browser or make an entry in hosts file if not done.

##### On local

```bash
php artisan serve
```

**How to log in as admin:**

> *http(s)://example.com/admin/login*

```text
email:admin@example.com
password:admin123
```

**How to log in as customer:**

*You can directly register as customer and then login.*

> *http(s)://example.com/customer/register*

### License

Bagisto is a truly opensource E-Commerce framework which will always be free under the [MIT License](https://github.com/bagisto/bagisto/blob/master/LICENSE).

### Security Vulnerabilities

Please don't disclose security vulnerabilities publicly. If you find any
security vulnerability in Bagisto then please email us: [support@bagisto.com](mailto:support@bagisto.com).

### Miscellaneous

#### Contributors

This project is on [Open Collective](https://opencollective.com/bagisto) and it exists thanks to the people who contribute.

[![contributors](https://opencollective.com/bagisto/contributors.svg?width=890&button=false "https://opencollective.com/bagisto")](https://github.com/bagisto/bagisto/graphs/contributors)

#### Backers

Thank you to all our backers! 🙏

<a href="https://opencollective.com/bagisto#contributors" target="_blank">
<img src="https://opencollective.com/bagisto/backers.svg?width=890">
</a>

#### Sponsors

Support this project by becoming a sponsor. Your logo will show up here with a link to your website.

<a href="https://opencollective.com/bagisto/contribute/sponsor-7372/checkout" target="_blank"><img src="https://images.opencollective.com/static/images/become_sponsor.svg"></a>

## Docker

Create a local environment with PHP 7.4, Nginx, MySQL, for Kalista Beauty's
front-end and back-end web applications. Microsoft SQL Server drivers have been
added for connectivity purposes.

The main Dockerfile is based [on this repo](https://github.com/dimadeush/docker-nginx-php-laravel).

There is a script included, `setup.sh`, that will do the following:

* Prompt the user to input any missing required environment variables to `.env`
* Build the __nginx, mysql, and kalistabeauty__ containers and link them together
* Start all containers as a non-root user
  * `mysql` for the `mysql` container
  * `www-data` for the `webserver` and `kalistabeauty` containers
* Install composer packages via `composer install`
* Install npm packages via `npm install`
* Publish [Bagisto packages](https://bagisto.com/en/) via `php artisan vendor:publish --force --all`

This script uses `docker-compose` to build and link all three containers together;
details of how this work are in `docker-compose.yml` and the associated
`Dockerfile`s:

* `Dockerfile`
* `mysql8.Dockerfile`

To connect to MySQL within Docker, make sure that:

* `DB_HOST` is set to the name of the mysql container (mysql)
* `DB_DATABASE` is set to the name of your app container (kalistabeauty)
* `DB_USERNAME` set to `root`; `root` seems to work best
* `DB_PASSWORD` is set to any password

Reference links for MySQL configuration:

* <https://stackoverflow.com/a/51598108/1620794>

If everything works properly, you should be able to visit this link:

<http://localhost:8990>

If you would like to change the port number, you can do so by:

* setting `APP_PORT` in `.env` or
* updating the `webserver` port in `docker-compose.yml`

and using the following commands to restart the containers:

```bash
# Build all images
docker-compose build
docker-compose down
docker-compose up -d --build

```

The script should have installed all Composer packages for you. Just in case,
do so now and migrate all tables and site data to the local MySQL database:

```bash
docker exec kalistabeauty composer install
docker exec kalistabeauty php artisan key:generate
# This should only be run one time
# docker exec kalistabeauty php artisan bagisto:install
docker exec kalistabeauty npm install
docker exec kalistabeauty npm run dev
docker exec kalistabeauty bash -c "cd packages/KLB/Themes; npm install; npm run dev"
```

If you encounter errors when going to <http://localhost:8990>, make sure that the
`APP_KEY` environment variable in your `.env` file is set properly. Check to
make sure value is not blank and does not have multiple base-64-encoded values.

Once you clear the `APP_KEY` environment variable in the `.env` file, use
Docker to add the key properly and re-create the containers:

```bash
# Generates a new base-64-encoded value for APP_KEY in .env
docker exec kalistabeauty php artisan key:generate

# Destroys then re-creates the containers, building the images if necessary.
docker-compose down
docker-compose up -d --build
```

This should refresh localhost.

> The command `php artisan bagisto:install` is actually the combination of
> multiple commands that are listed on their website:
> <https://webkul.com/blog/laravel-ecommerce-website/>. This command should
> only be run once. If things are not working properly, try seeding the database.

Running `php artisan bagisto:install` does not fully seed the database. You can
fully seed the database by:

```bash
# After going to /admin and adding a new attribute family where the "code" is
# `shopify` and the name is `Shopify`, run our KLB-specific seeder:
docker exec kalistabeauty php artisan db:seed --class='KLB\Themes\Database\Seeders\KLBDatabaseSeeder'
```

To open a shell into our main Docker container with your code:

```bash
docker exec -it kalistabeauty bash
```

### Usage

Once you have Bagisto running, you need to use the __KLB Theme__ instead of the
default Velocity theme. To do so, go to `/admin` and log in with the default
admin credentials (also listed on their site):

* __Username__: `admin@example.com`
* __Password__: `admin123`

```bash
# Open a shell to the mysql container
docker exec -it mysql bash

# Open a shell to the Laravel/Bagisto container
docker exec -it kalistabeauty bash

# Open a shell to the Nginx container
docker exec -it nginx container
```

## Troubleshooting

If for some reason your code is not mounted to `/var/www`, make sure that you
have enabled file sharing in Docker for your code folder. If volume mounting
still does not work, restart Docker and try again. That seems to work for
Docker Desktop 3.0.3 (macOS).

Another potential workaround is to disable __Experimental Features -->__
__Use gRPC FUSE for file sharing__ in the Docker Desktop preferences, according
[to this issue](https://github.com/docker/for-mac/issues/5115#issuecomment-742688465)
on GitHub.

If you need information concerning extensions installed or enabled for PHP, you
can use the following commands in a shell:

```bash
# https://community.centminmod.com/threads/cannot-load-zend-opcache-it-was-already-loaded.19224/
php --ini

php -m

ls -lah $(php-config --extension-dir)
```

### MySQL

If you get an error about port 3306 not being available, if you have MySQL
installed on your host system, stop the service using the System Preferences
and prevent it from automatically starting on boot.

```bash
# login with mysqladmin or mysql (mysql container)
# hostname is localhost or 127.0.0.1
docker exec -it mysql bash
mysqladmin -u user_name -p database_name -h hostname
mysql -u user_name -p database -h hostname

# While in mysql:
show databases;
use database_name;
show tables;
```

```sql
-- Log in (mysql -u user_name) as root and list all users
-- https://www.percona.com/blog/2019/07/05/fixing-a-mysql-1045-error/
select user from mysql.user;
```

### Error Messages

* `Trying to get property 'default_locale' of non-object

This error occurs when the `channels` table in your local MySQL database is
empty. Try seeding your database to fill the table try loading the page again.

```bash
# How to create your database tables and seed them
docker exec -it kalistabeauty php artisan migrate
docker exec -it kalistabeauty php artisan db:seed

# How to confirm that the channels table is empty:
docker exec -it mysql bash
mysql -u root -p kalistabeauty

# Inside the mysql shell...
show databases;
use kalistabeauty;
# Make sure you have already run `php artisan migrate` on the `kalistabeauty`
# container to migrate all tables
show tables;
select * from channels;
```

### Making Changes

When making changes to `/packages/KLB`, you may need to run the script
`refresh.sh` in order to see your changes when you refresh your browser. This
script follows the same steps following to
see your changes:

```bash
php artisan vendor:publish --force --all
php artisan route:cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```
