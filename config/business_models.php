<?php

return [
    'project_based' => [
        'label'          => 'Por proyecto',
        'primary_entity' => 'projects',
        'metrics'        => [
            'leads_total', 'leads_new', 'conversion_basic', 'leads_by_channel',
            'pipeline_value', 'activities_summary',
            'quotes_requested_value', 'quote_to_project_rate',
            'avg_project_value', 'projects_active', 'projects_closed',
        ],
    ],
    'subscription' => [
        'label'          => 'Suscripción',
        'primary_entity' => 'subscriptions',
        'metrics'        => [
            'leads_total', 'leads_new', 'conversion_basic', 'leads_by_channel',
            'pipeline_value', 'activities_summary',
            'mrr', 'churn_rate', 'ltv', 'new_subscriptions', 'cancelled_subscriptions',
        ],
    ],
    'transactional' => [
        'label'          => 'Transaccional',
        'primary_entity' => 'invoices',
        'metrics'        => [
            'leads_total', 'leads_new', 'conversion_basic', 'leads_by_channel',
            'activities_summary',
            'total_revenue', 'avg_ticket', 'orders_count', 'repeat_customer_rate',
        ],
    ],
    'appointment' => [
        'label'          => 'Citas',
        'primary_entity' => 'appointments',
        'metrics'        => [
            'leads_total', 'leads_new', 'conversion_basic', 'leads_by_channel',
            'activities_summary',
            'appointments_booked', 'no_show_rate', 'agenda_occupancy', 'recurring_clients',
        ],
    ],
];
