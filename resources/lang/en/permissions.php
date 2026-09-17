<?php

return [
    'categories' => [
        'agenda' => [
            'title' => 'Service Body Agendas',
            'icon' => 'bi-journals',
        ],
        'store' => [
            'title' => 'Store & Literature',
            'icon' => 'bi-box-seam',
        ],
        'calendar' => [
            'title' => 'Calendar & Events',
            'icon' => 'bi-calendar-check',
        ],
        'forms' => [
            'title' => 'Custom Forms',
            'icon' => 'bi-ui-checks',
        ],
        'system' => [
            'title' => 'System & Legacy Roles',
            'icon' => 'bi-shield-lock',
        ],
        'general' => [
            'title' => 'General & Others',
            'icon' => 'bi-gear-fill',
        ],
    ],

    'items' => [
        // Service Body Agendas
        'create sb agenda' => [
            'label' => 'Create Service Body Agendas',
            'description' => 'Draft and submit new meeting agendas for the assigned service body.',
            'category' => 'agenda',
        ],
        'edit sb agenda' => [
            'label' => 'Edit Service Body Agendas',
            'description' => 'Modify existing draft meeting agendas before facilitator approval.',
            'category' => 'agenda',
        ],
        'approve sb agenda' => [
            'label' => 'Approve Service Body Agendas',
            'description' => 'Review, approve, and officially publish submitted meeting agendas.',
            'category' => 'agenda',
        ],
        'delete sb agenda' => [
            'label' => 'Delete Service Body Agendas',
            'description' => 'Remove draft or obsolete agendas from the service body archive.',
            'category' => 'agenda',
        ],

        // Store & Literature
        'manage store' => [
            'label' => 'Manage Literature Store',
            'description' => 'Configure store catalog, update pricing, and oversee store operations.',
            'category' => 'store',
        ],
        'view lit inventory' => [
            'label' => 'View Literature Inventory',
            'description' => 'Access real-time stock levels and warehouse inventory counts.',
            'category' => 'store',
        ],
        'view inventory slips' => [
            'label' => 'View Inventory Slips',
            'description' => 'Review incoming and outgoing stock dispatch and receiving slips.',
            'category' => 'store',
        ],
        'acknowledge inventory slips' => [
            'label' => 'Acknowledge Inventory Slips',
            'description' => 'Confirm physical receipt or handover of inventory dispatch slips.',
            'category' => 'store',
        ],
        'view lit ledger' => [
            'label' => 'View Literature Ledger',
            'description' => 'Inspect comprehensive financial and movement history of literature items.',
            'category' => 'store',
        ],
        'manage literature requests' => [
            'label' => 'Manage Literature Requests',
            'description' => 'Process, approve, fulfill, or reject literature distribution orders.',
            'category' => 'store',
        ],
        'approve literature requests' => [
            'label' => 'Approve Literature Requests',
            'description' => 'Authorize submitted literature orders prior to dispatch fulfillment.',
            'category' => 'store',
        ],
        'edit literature requests' => [
            'label' => 'Edit Literature Requests',
            'description' => 'Modify quantities and line items on pending literature requests.',
            'category' => 'store',
        ],

        // Calendar & Events
        'can_manage_calendar' => [
            'label' => 'Manage General Calendar',
            'description' => 'Full administrative control over fellowship events, dates, and recurring calendars.',
            'category' => 'calendar',
        ],
        'create calendar events' => [
            'label' => 'Create Calendar Events',
            'description' => 'Submit and schedule new regional and committee events.',
            'category' => 'calendar',
        ],
        'manage calendar events' => [
            'label' => 'Manage Calendar Events',
            'description' => 'Edit, reschedule, or cancel scheduled calendar events.',
            'category' => 'calendar',
        ],

        // Custom Forms
        'manage own forms' => [
            'label' => 'Manage Own Custom Forms',
            'description' => 'Create, edit, and review submissions for committee-specific custom forms.',
            'category' => 'forms',
        ],
        'manage helpline' => [
            'label' => 'Manage Helplines',
            'description' => 'View and export helpline call responses, manage volunteers list, and synchronize monthly reports.',
            'category' => 'forms',
        ],

        // Legacy / System Role-Named Permissions
        'super admin' => [
            'label' => 'Super Administrator Access (Legacy)',
            'description' => 'Legacy permission granting unrestricted system-wide administrative control.',
            'category' => 'system',
        ],
        'rsc' => [
            'label' => 'RSC Officer Access (Legacy)',
            'description' => 'Legacy permission representing Regional Service Committee administrative authority.',
            'category' => 'system',
        ],
        'Committees' => [
            'label' => 'Service Committee Access (Legacy)',
            'description' => 'Legacy permission identifying service committee coordinators and members.',
            'category' => 'system',
        ],
        'store' => [
            'label' => 'Store Officer Access (Legacy)',
            'description' => 'Legacy permission granting access to literature store operations.',
            'category' => 'system',
        ],
        'gsr' => [
            'label' => 'GSR Access (Legacy)',
            'description' => 'Legacy permission for General Service Representatives of home groups.',
            'category' => 'system',
        ],
        'RCM' => [
            'label' => 'RCM Access (Legacy)',
            'description' => 'Legacy permission for Regional Committee Members representing areas.',
            'category' => 'system',
        ],
        'FAC' => [
            'label' => 'Facilitator Access (Legacy)',
            'description' => 'Legacy permission for service body and committee meeting facilitators.',
            'category' => 'system',
        ],
        'TR' => [
            'label' => 'Treasurer Access (Legacy)',
            'description' => 'Legacy permission for service body and committee treasurers.',
            'category' => 'system',
        ],
        'Sec' => [
            'label' => 'Secretary Access (Legacy)',
            'description' => 'Legacy permission for service body and committee recording secretaries.',
            'category' => 'system',
        ],
        'Phoneline' => [
            'label' => 'Phoneline Access (Legacy)',
            'description' => 'Legacy permission for NA helpline and phoneline responders.',
            'category' => 'system',
        ],
        'view api analytics' => [
            'label' => 'View API & Mobile Analytics',
            'description' => 'Monitor mobile app API traffic, endpoint latency, errors, and request logs.',
            'category' => 'system',
        ],
    ],
];

