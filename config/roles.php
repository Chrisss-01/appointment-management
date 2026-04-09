<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role Landing Pages
    |--------------------------------------------------------------------------
    |
    | This map defines the default dashboard or landing page for each user role.
    | It is used by RoleMiddleware to perform guided redirects when a user
    | attempts to access a restricted area.
    |
    */
    'dashboards' => [
        'admin'   => 'admin.dashboard',
        'staff'   => 'staff.dashboard',
        'student' => 'student.dashboard',
    ],
];
