<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Execution timeout (seconds)
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('ARTISAN_RUNNER_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Page unlock lifetime (seconds)
    |--------------------------------------------------------------------------
    |
    | How long a successful password unlock keeps the Artisan Runner accessible.
    |
    */

    'unlock_timeout' => (int) env('ARTISAN_RUNNER_UNLOCK_TIMEOUT', 3600),

    /*
    |--------------------------------------------------------------------------
    | Allowed Artisan commands
    |--------------------------------------------------------------------------
    |
    | Only these command names may be executed from the admin runner.
    | Destructive commands (db:wipe, migrate:fresh, migrate:rollback, tinker,
    | etc.) are omitted on purpose. Add entries here deliberately.
    |
    */

    'allowed' => [
        'about',
        'cache:clear',
        'config:cache',
        'config:clear',
        'config:show',
        'db:seed',
        'db:show',
        'event:cache',
        'event:clear',
        'event:list',
        'migrate',
        'migrate:install',
        'migrate:status',
        'optimize',
        'optimize:clear',
        'queue:failed',
        'queue:flush',
        'queue:restart',
        'queue:retry',
        'route:cache',
        'route:clear',
        'route:list',
        'schedule:clear-cache',
        'schedule:list',
        'schedule:run',
        'storage:link',
        'view:cache',
        'view:clear',
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands that receive --force automatically
    |--------------------------------------------------------------------------
    */

    'force' => [
        'migrate',
        'db:seed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer binary
    |--------------------------------------------------------------------------
    |
    | Executable used to run allowlisted Composer commands. Override via env
    | if Composer isn't available as a bare `composer` on the PATH.
    |
    */

    'composer_binary' => env('ARTISAN_RUNNER_COMPOSER_BINARY', 'composer'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Composer commands
    |--------------------------------------------------------------------------
    |
    | Only these Composer subcommands may be executed from the admin runner.
    | Commands that add/remove packages (require, remove) or touch the
    | global Composer installation (self-update) are omitted on purpose.
    |
    */

    'composer_allowed' => [
        'audit',
        'check-platform-reqs',
        'clear-cache',
        'diagnose',
        'dump-autoload',
        'install',
        'outdated',
        'show',
        'update',
        'validate',
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer commands that receive --no-interaction automatically
    |--------------------------------------------------------------------------
    */

    'composer_force' => [
        'install',
        'update',
    ],

    /*
    |--------------------------------------------------------------------------
    | Accordion group labels
    |--------------------------------------------------------------------------
    */

    'groups' => [
        'migrations' => 'Migrations',
        'database' => 'Database',
        'scheduler' => 'Scheduler',
        'cache' => 'Cache & optimize',
        'queue' => 'Queue & storage',
        'composer' => 'Composer',
        'info' => 'Application info',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preset quick actions shown in the admin UI
    |--------------------------------------------------------------------------
    */

    'presets' => [
        [
            'key' => 'migrate',
            'group' => 'migrations',
            'label' => 'Run migrations',
            'description' => 'php artisan migrate',
            'command' => 'migrate',
        ],
        [
            'key' => 'migrate-status',
            'group' => 'migrations',
            'label' => 'Migration status',
            'description' => 'php artisan migrate:status',
            'command' => 'migrate:status',
        ],
        [
            'key' => 'migrate-install',
            'group' => 'migrations',
            'label' => 'Install migration repository',
            'description' => 'php artisan migrate:install',
            'command' => 'migrate:install',
        ],
        [
            'key' => 'db-seed',
            'group' => 'database',
            'label' => 'Run database seeder',
            'description' => 'php artisan db:seed',
            'command' => 'db:seed',
            'danger' => true,
        ],
        [
            'key' => 'db-show',
            'group' => 'database',
            'label' => 'Show database',
            'description' => 'php artisan db:show',
            'command' => 'db:show',
        ],
        [
            'key' => 'schedule-run',
            'group' => 'scheduler',
            'label' => 'Run scheduler',
            'description' => 'php artisan schedule:run',
            'command' => 'schedule:run',
        ],
        [
            'key' => 'schedule-list',
            'group' => 'scheduler',
            'label' => 'List scheduled tasks',
            'description' => 'php artisan schedule:list',
            'command' => 'schedule:list',
        ],
        [
            'key' => 'schedule-clear-cache',
            'group' => 'scheduler',
            'label' => 'Clear scheduler cache',
            'description' => 'php artisan schedule:clear-cache',
            'command' => 'schedule:clear-cache',
        ],
        [
            'key' => 'optimize-clear',
            'group' => 'cache',
            'label' => 'Clear all caches',
            'description' => 'php artisan optimize:clear',
            'command' => 'optimize:clear',
        ],
        [
            'key' => 'optimize',
            'group' => 'cache',
            'label' => 'Optimize application',
            'description' => 'php artisan optimize',
            'command' => 'optimize',
        ],
        [
            'key' => 'cache-clear',
            'group' => 'cache',
            'label' => 'Clear application cache',
            'description' => 'php artisan cache:clear',
            'command' => 'cache:clear',
        ],
        [
            'key' => 'config-clear',
            'group' => 'cache',
            'label' => 'Clear config cache',
            'description' => 'php artisan config:clear',
            'command' => 'config:clear',
        ],
        [
            'key' => 'config-cache',
            'group' => 'cache',
            'label' => 'Cache config',
            'description' => 'php artisan config:cache',
            'command' => 'config:cache',
        ],
        [
            'key' => 'route-clear',
            'group' => 'cache',
            'label' => 'Clear route cache',
            'description' => 'php artisan route:clear',
            'command' => 'route:clear',
        ],
        [
            'key' => 'route-cache',
            'group' => 'cache',
            'label' => 'Cache routes',
            'description' => 'php artisan route:cache',
            'command' => 'route:cache',
        ],
        [
            'key' => 'view-clear',
            'group' => 'cache',
            'label' => 'Clear compiled views',
            'description' => 'php artisan view:clear',
            'command' => 'view:clear',
        ],
        [
            'key' => 'view-cache',
            'group' => 'cache',
            'label' => 'Cache views',
            'description' => 'php artisan view:cache',
            'command' => 'view:cache',
        ],
        [
            'key' => 'event-clear',
            'group' => 'cache',
            'label' => 'Clear event cache',
            'description' => 'php artisan event:clear',
            'command' => 'event:clear',
        ],
        [
            'key' => 'event-cache',
            'group' => 'cache',
            'label' => 'Cache events',
            'description' => 'php artisan event:cache',
            'command' => 'event:cache',
        ],
        [
            'key' => 'queue-restart',
            'group' => 'queue',
            'label' => 'Restart queue workers',
            'description' => 'php artisan queue:restart',
            'command' => 'queue:restart',
        ],
        [
            'key' => 'queue-failed',
            'group' => 'queue',
            'label' => 'List failed jobs',
            'description' => 'php artisan queue:failed',
            'command' => 'queue:failed',
        ],
        [
            'key' => 'storage-link',
            'group' => 'queue',
            'label' => 'Link storage',
            'description' => 'php artisan storage:link',
            'command' => 'storage:link',
        ],
        [
            'key' => 'about',
            'group' => 'info',
            'label' => 'Application about',
            'description' => 'php artisan about',
            'command' => 'about',
        ],
        [
            'key' => 'route-list',
            'group' => 'info',
            'label' => 'List routes',
            'description' => 'php artisan route:list',
            'command' => 'route:list',
        ],
        [
            'key' => 'event-list',
            'group' => 'info',
            'label' => 'List events',
            'description' => 'php artisan event:list',
            'command' => 'event:list',
        ],
        [
            'key' => 'composer-install',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Install dependencies',
            'description' => 'composer install --no-interaction --prefer-dist',
            'command' => 'install',
            'parameters' => ['--prefer-dist'],
        ],
        [
            'key' => 'composer-update',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Update dependencies',
            'description' => 'composer update --no-interaction',
            'command' => 'update',
            'danger' => true,
        ],
        [
            'key' => 'composer-dump-autoload',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Dump autoload',
            'description' => 'composer dump-autoload',
            'command' => 'dump-autoload',
        ],
        [
            'key' => 'composer-dump-autoload-optimized',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Dump autoload (optimized)',
            'description' => 'composer dump-autoload --optimize',
            'command' => 'dump-autoload',
            'parameters' => ['--optimize'],
        ],
        [
            'key' => 'composer-outdated',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'List outdated packages',
            'description' => 'composer outdated --direct',
            'command' => 'outdated',
            'parameters' => ['--direct'],
        ],
        [
            'key' => 'composer-show',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'List installed packages',
            'description' => 'composer show',
            'command' => 'show',
        ],
        [
            'key' => 'composer-validate',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Validate composer.json',
            'description' => 'composer validate',
            'command' => 'validate',
        ],
        [
            'key' => 'composer-diagnose',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Diagnose environment',
            'description' => 'composer diagnose',
            'command' => 'diagnose',
        ],
        [
            'key' => 'composer-audit',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Audit for vulnerabilities',
            'description' => 'composer audit',
            'command' => 'audit',
        ],
        [
            'key' => 'composer-check-platform-reqs',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Check platform requirements',
            'description' => 'composer check-platform-reqs',
            'command' => 'check-platform-reqs',
        ],
        [
            'key' => 'composer-clear-cache',
            'group' => 'composer',
            'type' => 'composer',
            'label' => 'Clear Composer cache',
            'description' => 'composer clear-cache',
            'command' => 'clear-cache',
        ],
    ],

];
