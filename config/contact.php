<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contact fields
    |--------------------------------------------------------------------------
    |
    | Fields available on the Settings page and rendered in the site footer
    | when a value is saved. Keys are stored in the settings table.
    |
    | `admin_icon` uses Bootstrap Icons (loaded in AdminLTE).
    | `icon` uses Font Awesome (loaded on the public site).
    |
    */

    'fields' => [
        'email' => [
            'label' => 'Email',
            'admin_icon' => 'bi bi-envelope',
            'icon' => 'fa-solid fa-envelope',
            'placeholder' => 'hello@coffeeandkala.com',
            'type' => 'email',
        ],
        'phone' => [
            'label' => 'Phone',
            'admin_icon' => 'bi bi-telephone',
            'icon' => 'fa-solid fa-phone',
            'placeholder' => '+91 98765 43210',
            'type' => 'tel',
        ],
        'address' => [
            'label' => 'Address',
            'admin_icon' => 'bi bi-geo-alt',
            'icon' => 'fa-solid fa-location-dot',
            'placeholder' => '123 Coffee Lane, Udaipur, India',
            'type' => 'text',
        ],
    ],

];
