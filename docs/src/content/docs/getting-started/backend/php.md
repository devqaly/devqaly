---
title: Connect Your Backend - PHP
description: Check out how you can connect your backend to fully utilize the power of Devqaly
---

# Installing
You can connect your PHP backend to your frontend by simply installing the `devqaly-php` with composer:

```bash
composer require devqaly/devqaly-php
```

# How to use

Before anything, you will need to start the client:

```php
$devqaly = new Devqaly\DevqalyClient();
```

In case you are self-hosting Devqaly, you can pass your self-hosted Devqaly's backend URL when initializing the client:

```php
$devqaly = new Devqaly\DevqalyClient('https://api-devqaly.my-domain.com');
```

You can also pass the `source` where this event comes from:

```php
$devqaly = new Devqaly\DevqalyClient('https://api-devqaly.my-domain.com', 'authentication-service');
```

## When a session is being recorded

Whenever a session have started being recorded in the frontend, for each request leaving your frontend application, Devqaly's 
browser SDK will attach three important headers to the HTTP request:

- `x-devqaly-session-id`: the session ID that is currently being recorded
- `x-devqaly-session-secret-token`: the session token that you will use to create events
- `x-devqaly-request-id`: the unique identifier that will identify a specific request in a session

You will need to send those headers to create events in the session being recorded:

```php
$sessionId = $_SERVER['HTTP_X_DEVQALY_SESSION_ID'];
$sessionSecretToken = $_SERVER['HTTP_X_DEVQALY_SESSION_SECRET_TOKEN'];
$requestId = $_SERVER['HTTP_X_DEVQALY_REQUEST_ID'];
```

## Creating events in the session

Now that we have all the necessary information to create an event in a session, we can create events

### Creating Database Transactions
You can listen to all database transactions that are happening in your PHP application and create them in a session 
as an event with `createDatabaseEventTransaction` method in `DevqalyClient`:

```php
$devqaly = new Devqaly\DevqalyClient();

$sessionId = $_SERVER['HTTP_X_DEVQALY_SESSION_ID'];
$sessionSecretToken = $_SERVER['HTTP_X_DEVQALY_SESSION_SECRET_TOKEN'];
$requestId = $_SERVER['HTTP_X_DEVQALY_REQUEST_ID'];

$devqaly->createDatabaseEventTransaction($sessionId, $sessionSecretToken, [
    'sql' => 'select * from users where users.id = 1',
    'executionTimeInMilliseconds' => 10,
    'requestId' => $requestId
]);
```

:::tip[Did you know?]

If you are making a request from your backend application to a third-party (or even another microservice) you should
forward the headers to be able to fully trace your request

:::

### Creating Logs
While a session is being recorded, you can create logs that will help out you debug issues faster. For that, you just
need to call `createLogEvent`:

```php
$devqaly = new Devqaly\DevqalyClient();

$sessionId = $_SERVER['HTTP_X_DEVQALY_SESSION_ID'];
$sessionSecretToken = $_SERVER['HTTP_X_DEVQALY_SESSION_SECRET_TOKEN'];
$requestId = $_SERVER['HTTP_X_DEVQALY_REQUEST_ID'];

if ($userCantSeeContent) {
    $devqaly->createLogEvent($sessionId, $sessionSecretToken, [
        'level' => 'INFORMATIONAL',
        'log' => 'User with ID xx is not allowed to see this content',
        'requestId' => $requestId,
    ]);
}
```

In the `level` key you can pass the following levels:

- emergency
- alert
- critical
- error
- warning
- notice
- informational
- debug


