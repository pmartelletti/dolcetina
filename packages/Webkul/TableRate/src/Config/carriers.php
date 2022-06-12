<?php

return [
    'tablerate' => [
        'code'          => 'tablerate',
        'title'         => 'Table Rate Shipping',
        'description'   => 'Table Rate Shipping',
        'active'        => true,
        'default_rate'  => 20,
        'type'          => 'per_order',
        'class'         => 'Webkul\TableRate\Carriers\TableRate',
    ]
];