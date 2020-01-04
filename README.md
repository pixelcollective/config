# Tiny Pixel WordPress Configuration

![Packagist Version](https://img.shields.io/packagist/v/tiny-pixel/config)
[![Maintainability](https://api.codeclimate.com/v1/badges/97d1ec006ef738b2838a/maintainability)](https://codeclimate.com/github/pixelcollective/config/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/97d1ec006ef738b2838a/test_coverage)](https://codeclimate.com/github/pixelcollective/config/test_coverage)
[![CircleCI](https://circleci.com/gh/pixelcollective/config.svg?style=shield)](https://circleci.com/gh/pixelcollective/config)

An opinionated wrapper for [roots/wp-config](https://github.com/roots/wp-config) enabling a more object-oriented config definition style for Bedrock projects. Used as the base of Tiny Pixel managed WordPress applications.

Not safer or better, just different. Currently `roots/wp-config` has way better coverage, in fact.

## Example `config/application.php`

```php
<?php

/**
 * WordPress App config
 *
 * @author    Kelly Mears <hello@tinypixel.dev>
 * @copyright Tiny Pixel https://tinypixel.dev
 * @license   MIT https://git.io/JepVj
 */

use TinyPixel\Config\Bootloader;

/**
 * Initialize global env.
 */
Env::init();

/**
 * Initialize bootloader.
 */
$bootloader = new Bootloader();
$bootloader->init(dirname(__DIR__));

/**
 * Specify required environmental variables.
 */
$bootloader->loadEnv([
    'DB_HOST',
    'DB_NAME',
    'DB_PASSWORD',
    'DB_USER',
    'REDIS_HOST',
    'REDIS_PASSWORD',
    'S3_UPLOADS_BUCKET',
    'S3_UPLOADS_KEY',
    'S3_UPLOADS_SECRET',
    'WP_ENV',
    'WP_HOME',
    'WP_SITEURL',
]);

/**
 * Configure Sentry.
 */
if (env('SENTRY_DSN') && env('WP_ENV') !== 'development') {
    Sentry\init([
        'dsn'         => env('SENTRY_DSN'),
        'release'     => env('SENTRY_RELEASE'),
        'environment' => env('SENTRY_ENVIRONMENT'),
        'error_types' => E_ALL & ~E_NOTICE & ~E_DEPRECATED,
    ]);

    if (env('SENTRY_TRELLIS_VERSION')) {
        Sentry\configureScope(function (Sentry\State\Scope $scope): void {
            $scope->setTag('property', env('WP_SITEURL'));
            $scope->setTag('version', env('SENTRY_TRELLIS_VERSION'));
        });
    }
}

/**
 * Define environments
 */
$bootloader->defineEnvironments([
    'development' => 'https://ada.vagrant',
    'staging'     => 'https://staging.ada.com',
    'production'  => 'https://ada.com',
]);

/**
 * Production defaults.
 */
$bootloader->configureWordPressApp([
    'DISABLE_WP_CRON'            => true,
    'AUTOMATIC_UPDATER_DISABLED' => true,
    'DISALLOW_FILE_EDIT'         => true,
    'DISALLOW_FILE_MODS'         => true,
    'WP_DEBUG_DISPLAY'           => false,
    'SCRIPT_DEBUG'               => false,
    'DISPLAY_ERRORS'             => false,
]);

/**
 * Configure application paths.
 */
$bootloader->defineFS([
    'CONTENT_DIR' => 'app',
    'WP_ENV'      => env('WP_ENV'),
    'WP_HOME'     => env('WP_HOME'),
    'WP_SITEURL'  => env('WP_SITEURL'),
]);

/**
 * Define DB.
 */
$bootloader->defineDB([
    'DB_NAME'      => env('DB_NAME'),
    'DB_USER'      => env('DB_USER'),
    'DB_PASSWORD'  => env('DB_PASSWORD'),
    'DB_HOST'      => env('DB_HOST'),
    'DB_CHARSET'   => env('DB_CHARSET') ?: 'utf8',
    'DB_COLLATION' => env('DB_COLLATION') ?: 'utf8_unicode_ci',
    'DB_PREFIX'    => env('DB_PREFIX') ?: 'wp_',
]);

$table_prefix = $bootloader::get('DB_PREFIX');

/**
 * Define S3.
 */
$bootloader->defineS3([
    'S3_UPLOADS_BUCKET'     => env('S3_UPLOADS_BUCKET'),
    'S3_UPLOADS_KEY'        => env('S3_UPLOADS_KEY'),
    'S3_UPLOADS_SECRET'     => env('S3_UPLOADS_SECRET'),
    'S3_UPLOADS_ENDPOINT'   => env('S3_UPLOADS_ENDPOINT'),
    'S3_UPLOADS_REGION'     => env('S3_UPLOADS_REGION'),
]);

/**
 * Define & configure Redis.
 */
$bootloader->defineRedis([
    'REDIS_HOST'          => env('REDIS_HOST'),
    'REDIS_AUTH'          => env('REDIS_AUTH'),
    'REDIS_PORT'          => env('REDIS_PORT'),
    'PREDIS_CERT'         => "{$bootloader->bedrockDir}/redis.crt",
    'PREDIS_VERIFY_PEERS' => true,
]);

$bootloader->configureRedis([
    'REDIS_OBJECT_CACHE'        => env('REDIS_OBJECT_CACHE'),
    'WP_REDIS_USE_CACHE_GROUPS' => env('REDIS_USE_CACHE_GROUPS'),
    'WP_CACHE_KEY_SALT'         => env('REDIS_CACHE_KEY_SALT'),
]);

/**
 * Configure auth keys and salts.
 */
$bootloader->defineSalts([
    'AUTH_KEY'         => env('AUTH_KEY'),
    'AUTH_SALT'        => env('AUTH_SALT'),
    'LOGGED_IN_KEY'    => env('LOGGED_IN_KEY'),
    'LOGGED_IN_SALT'   => env('LOGGED_IN_SALT'),
    'NONCE_KEY'        => env('NONCE_KEY'),
    'NONCE_SALT'       => env('NONCE_SALT'),
    'SECURE_AUTH_KEY'  => env('SECURE_AUTH_KEY'),
    'SECURE_AUTH_SALT' => env('SECURE_AUTH_SALT'),
]);

/**
 * Allow SSL behind a reverse proxy.
 */
$bootloader->exposeSSL();

/**
 * Override environmental variables
 */
$bootloader->overrideEnv($bootloader::get('WP_ENV'));

/**
 * Boot application.
 */
$bootloader->boot();
```

## License

Copyright 2019 Tiny Pixel Collective.

Licensed MIT.
