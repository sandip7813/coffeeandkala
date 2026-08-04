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
    | Accordion group labels
    |--------------------------------------------------------------------------
    */

    'groups' => [
        'migrations' => 'Migrations',
        'database' => 'Database',
        'scheduler' => 'Scheduler',
        'cache' => 'Cache & optimize',
        'queue' => 'Queue & storage',
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
    ],

];
