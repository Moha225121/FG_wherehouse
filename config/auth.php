<?php

return [

    'defaults' =>[
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' =>[
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin' =>[
            'driver' => 'session',
            'provider' => 'admins',
        ],
        'employee' =>[
            'driver' => 'session',
            'provider' => 'employees',
        ],
    ],

    'providers' =>[
        'users' =>[
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'admins' =>[
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
        'employees' =>[
            'driver' => 'eloquent',
            'model' => App\Models\Employee::class,
        ],
    ],

    'passwords' =>[
        // Standard password reset arrays omitted for brevity, keeping default.
        'users' =>['provider' => 'users', 'table' => 'password_reset_tokens', 'expire' => 60, 'throttle' => 60],
        'admins' =>['provider' => 'admins', 'table' => 'password_reset_tokens', 'expire' => 60, 'throttle' => 60],
        'employees' =>['provider' => 'employees', 'table' => 'password_reset_tokens', 'expire' => 60, 'throttle' => 60],
    ],
    'password_timeout' => 10800,
];