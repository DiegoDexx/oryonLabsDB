<?php

return [
    'starter' => [
        'label'          => 'Starter',
        'modules'        => ['leads', 'clients_basic'],
        'channels'       => ['chatbot', 'form'],
        'metrics'        => ['leads_total', 'leads_new', 'conversion_basic'],
        'max_users'      => 2,
        'monthly_convos' => 200,
    ],
    'pro' => [
        'label'          => 'Pro',
        'modules'        => ['leads', 'clients', 'projects', 'activities'],
        'channels'       => ['chatbot', 'form', 'whatsapp', 'instagram'],
        'metrics'        => [
            'leads_total', 'leads_new', 'conversion_basic',
            'leads_by_channel', 'pipeline_value', 'activities_summary',
        ],
        'max_users'      => 5,
        'monthly_convos' => 800,
    ],
    'professional' => [
        'label'          => 'Professional',
        'modules'        => [
            'leads', 'clients', 'projects', 'activities',
            'subscriptions', 'invoices', 'lifecycle',
        ],
        'channels'       => ['chatbot', 'form', 'whatsapp', 'instagram', 'voice'],
        'metrics'        => [
            'leads_total', 'leads_new', 'conversion_basic',
            'leads_by_channel', 'pipeline_value', 'activities_summary',
            // project_based
            'quotes_requested_value', 'quote_to_project_rate',
            'avg_project_value', 'projects_active', 'projects_closed',
            // subscription
            'mrr', 'churn_rate', 'ltv', 'new_subscriptions', 'cancelled_subscriptions',
            // transactional
            'total_revenue', 'avg_ticket', 'orders_count', 'repeat_customer_rate',
            // appointment
            'appointments_booked', 'no_show_rate', 'agenda_occupancy', 'recurring_clients',
        ],
        'max_users'      => null,
        'monthly_convos' => 2000,
    ],
];
