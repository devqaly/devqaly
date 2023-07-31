# Getting started

We will assume that you are currently into the folder `devqaly/apps/api` already.

## Copy `.env.example` into `.env`

We have setup a `.env.example` that will be enough for most people. You can copy it with:

```bash
cp .env.example .env
```

## Install dependencies

Install the dependencies using a `laravelsail/php82-composer:latest` docker image:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

## Start the project
Now you can start the project with:

```bash
./vendor/bin/sail up --build -d
```

This command will build your images and create the necessary infrastructure to run the backend.

## Run migrations

Now that you have your database setup, you should run the database migrations

```bash
./vendor/bin/sail artisan migrate
```

### Test everything is okay

You can make a `GET` request to `/api/test`. You should receive an `{status: "okay"}` as a response.

## Connecting to database

Your local port `5432` will be forwarded to the database's container. You can connect to the database with any
database GUI such as [DBeaver](https://dbeaver.io/) with the following credentials:

```dotenv
HOST: localhost
PORT: 5432
DATABASE: laravel
USERNAME: sail
PASSWORD: password
```

## Entering the container

You can enter the web container with:

```bash
./vendor/bin/sail bash
```

## Running tests

You can run the tests with:

```bash
./vendor/bin/sail test
```
