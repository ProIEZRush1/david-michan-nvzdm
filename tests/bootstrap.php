<?php

require __DIR__.'/../vendor/autoload.php';

/*
 * This sandbox pre-populates real deploy-style env vars (APP_ENV=production, a live
 * DB_DATABASE path, SESSION_DRIVER=database, etc.) directly into $_SERVER at process
 * start. PHP's putenv() — which is all PHPUnit's <env force="true"> uses — never touches
 * an already-populated $_SERVER superglobal, and Laravel's env() resolution can read
 * $_SERVER, so phpunit.xml's <php><env> block alone is not enough here: without this,
 * tests would silently run against production settings (real DB file, CSRF enforced,
 * real mailer, etc.). Force the testing values into putenv/$_ENV/$_SERVER directly.
 */
foreach ([
    'APP_ENV' => 'testing',
    'APP_DEBUG' => 'true',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:',
    'SESSION_DRIVER' => 'array',
    'CACHE_STORE' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'MAIL_MAILER' => 'array',
    'BROADCAST_CONNECTION' => 'null',
] as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}
