<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Social networks
    |--------------------------------------------------------------------------
    |
    | Platforms available on the Settings page and rendered in the site footer
    | when a URL is saved. Keys are stored in the settings table.
    |
    | `admin_icon` uses Bootstrap Icons (loaded in AdminLTE).
    | `icon` uses Font Awesome (loaded on the public site).
    |
    */

    'networks' => [
        'instagram' => [
            'label' => 'Instagram',
            'admin_icon' => 'bi bi-instagram',
            'icon' => 'fa-brands fa-instagram',
            'placeholder' => 'https://instagram.com/your-handle',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'admin_icon' => 'bi bi-facebook',
            'icon' => 'fa-brands fa-facebook-f',
            'placeholder' => 'https://facebook.com/your-page',
        ],
        'pinterest' => [
            'label' => 'Pinterest',
            'admin_icon' => 'bi bi-pinterest',
            'icon' => 'fa-brands fa-pinterest-p',
            'placeholder' => 'https://pinterest.com/your-handle',
        ],
        'youtube' => [
            'label' => 'YouTube',
            'admin_icon' => 'bi bi-youtube',
            'icon' => 'fa-brands fa-youtube',
            'placeholder' => 'https://youtube.com/@your-channel',
        ],
        'x' => [
            'label' => 'X',
            'admin_icon' => 'bi bi-twitter-x',
            'icon' => 'fa-brands fa-x-twitter',
            'placeholder' => 'https://x.com/your-handle',
        ],
    ],

];
