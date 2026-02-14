<?php

return [
    'super_admin_role' => env('SITE_SETTINGS_SUPER_ADMIN_ROLE', 'admin'),

    'menus' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'group' => 'General',
            'description' => 'Main dashboard entry',
            'defaults' => ['admin' => true, 'user' => true, 'multimedia_staff' => true],
        ],
        'calendar' => [
            'label' => 'Calendar',
            'group' => 'General',
            'description' => 'Calendar and schedule',
            'defaults' => ['admin' => true, 'user' => true, 'multimedia_staff' => true],
        ],
        'events' => [
            'label' => 'Events',
            'group' => 'Events',
            'description' => 'Event list and management link',
            'defaults' => ['admin' => true, 'user' => true, 'multimedia_staff' => true],
        ],
        'program_flow' => [
            'label' => 'Program Flow',
            'group' => 'Events',
            'description' => 'Program flow module link',
            'defaults' => ['admin' => true, 'user' => true, 'multimedia_staff' => true],
        ],
        'event_check_in' => [
            'label' => 'Event Check-In',
            'group' => 'Events',
            'description' => 'Check-in module link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'participants' => [
            'label' => 'Participants',
            'group' => 'Events',
            'description' => 'Participants management',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'attendance' => [
            'label' => 'Attendance',
            'group' => 'Events',
            'description' => 'Attendance monitoring',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'multimedia' => [
            'label' => 'Multimedia',
            'group' => 'Content',
            'description' => 'Multimedia module',
            'defaults' => ['admin' => true, 'user' => true, 'multimedia_staff' => true],
        ],
        'venues' => [
            'label' => 'Venues',
            'group' => 'Admin',
            'description' => 'Venue management',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'documents' => [
            'label' => 'Documents',
            'group' => 'Admin',
            'description' => 'Documents module',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'notifications' => [
            'label' => 'Notifications',
            'group' => 'Admin',
            'description' => 'Notifications center link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'support' => [
            'label' => 'Help Support',
            'group' => 'Admin',
            'description' => 'Support tickets link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_overview' => [
            'label' => 'Reports: Overview',
            'group' => 'Admin',
            'description' => 'Reports overview link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_pipeline' => [
            'label' => 'Reports: Pipeline',
            'group' => 'Admin',
            'description' => 'Reports pipeline link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_participants' => [
            'label' => 'Reports: Participants',
            'group' => 'Admin',
            'description' => 'Reports participants link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_venues' => [
            'label' => 'Reports: Venues',
            'group' => 'Admin',
            'description' => 'Reports venues link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_finance' => [
            'label' => 'Reports: Finance',
            'group' => 'Admin',
            'description' => 'Reports finance link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_engagement' => [
            'label' => 'Reports: Engagement',
            'group' => 'Admin',
            'description' => 'Reports engagement link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'reports_support' => [
            'label' => 'Reports: Support',
            'group' => 'Admin',
            'description' => 'Reports support link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'users' => [
            'label' => 'Users',
            'group' => 'Admin',
            'description' => 'User management link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'roles' => [
            'label' => 'Roles',
            'group' => 'Admin',
            'description' => 'Role management link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
        'permissions' => [
            'label' => 'Permissions',
            'group' => 'Admin',
            'description' => 'Permission management link',
            'defaults' => ['admin' => true, 'user' => false, 'multimedia_staff' => false],
        ],
    ],
];
