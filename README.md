## Installation

Clone the repo locally:

```sh
git clone https://github.com/jonquintero/api-job-test.git
cd api-job-test
```

Install PHP dependencies:

```sh
composer install
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Setup you own database.


Run database migrations:

```sh
php artisan migrate
php artisan queue:table
php artisan migrate
php artisan notifications:table
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```
Setup servermail in env file.

Run the dev server (the output will give the address):

```sh
php artisan serve
```

