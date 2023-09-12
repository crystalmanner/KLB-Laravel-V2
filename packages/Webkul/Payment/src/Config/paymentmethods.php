<?php
return [
    'mpay'  => [
        'code'        => 'mpay',
        'title'       => 'Pay By Mpay',
        'description' => 'Pay By Mpay',
        'class'       => 'Webkul\Payment\Payment\Mpay',
        'active'      => true,
        'sort'        => 1,
    ],
    'sekure'  => [
        'code'        => 'sekure',
        'title'       => 'Pay By Sekure',
        'description' => 'Pay By Sekure',
        'class'       => 'Webkul\Payment\Payment\Sekure',
        'active'      => true,
        'sort'        => 1,
    ],
    'cashondelivery'  => [
        'code'        => 'cashondelivery',
        'title'       => 'Cash On Delivery',
        'description' => 'Cash On Delivery',
        'class'       => 'Webkul\Payment\Payment\CashOnDelivery',
        'active'      => true,
        'sort'        => 1,
    ],

    'moneytransfer'   => [
        'code'        => 'moneytransfer',
        'title'       => 'Money Transfer',
        'description' => 'Money Transfer',
        'class'       => 'Webkul\Payment\Payment\MoneyTransfer',
        'active'      => true,
        'sort'        => 2,
    ],

    'paypal_standard' => [
        'code'             => 'paypal_standard',
        'title'            => 'Paypal Standard',
        'description'      => 'Paypal Standard',
        'class'            => 'Webkul\Paypal\Payment\Standard',
        'sandbox'          => true,
        'active'           => true,
        'business_account' => 'test@webkul.com',
        'sort'             => 3,
    ]
];