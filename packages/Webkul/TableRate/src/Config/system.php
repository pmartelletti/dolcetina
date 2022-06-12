<?php

return [
    [
        'key' => 'sales.carriers.tablerate',
        'name' => 'tablerate::app.admin.system.tablerate-shipping',
        'sort' => 3,
        'fields' => [ 
            [
                'name' => 'title',
                'title' => 'tablerate::app.admin.system.title',
                'type'          => 'depends',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'tablerate::app.admin.system.description',
                'type' => 'textarea',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name'       => 'type',
                'title'      => 'admin::app.admin.system.type',
                'type'          => 'depends',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'options'    => [
                    [
                        'title' => 'Per Unit',
                        'value' => 'per_unit',
                    ], [
                        'title' => 'Per Order',
                        'value' => 'per_order',
                    ]
                ],
            ], [
                'name' => 'active',
                'title' => 'tablerate::app.admin.system.status',
                'type' => 'boolean',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'is_calculate_tax',
                'title'         => 'admin::app.admin.system.calculate-tax',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ]
        ]
    ],
];