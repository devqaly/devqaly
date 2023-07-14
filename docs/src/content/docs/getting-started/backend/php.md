---
title: Connect Your Backend - PHP
description: Check out how you can connect your backend to fully utilize the power of Devqaly
---

# Installing
You can connect your PHP backend to your frontend by simply installing the `PHP-SDK` with composer:

```bash
composer require devqaly/devqaly-php
```

# How to use

Before anything, you will need to start the client:

```php
$devqaly = new Devqaly\DevqalyClient();
```

# When a session is being recorded

Whenever a session have started being recorded in the frontend, for each request leaving the application Devqaly's 
browser SDK will attach three important headers to the HTTP request:

- `x-devqaly-session-id`:
- `x-devqaly-session-secret-token`:
- `x-devqaly-request-id`: