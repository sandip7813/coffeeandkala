<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default brand logo
    |--------------------------------------------------------------------------
    |
    | One of the keys defined in the logos list below. Super admins can change
    | this from the admin Settings page; the choice is stored in the settings
    | table under the brand_logo key.
    |
    */

    'default_logo' => 'mark',

    /*
    |--------------------------------------------------------------------------
    | Available logos
    |--------------------------------------------------------------------------
    |
    | Paths are relative to the public directory. Dimensions are used for
    | width/height attributes in the site header and footer.
    |
    */

    'logos' => [
        'wordmark' => [
            'label' => 'Wordmark',
            'description' => 'Horizontal COFFEE कला logo',
            'path' => 'images/logo/logo-wordmark.png',
            'email_path' => 'images/logo/logo-wordmark.png',
            'width' => 404,
            'height' => 179,
        ],
        'mark' => [
            'label' => 'Monogram',
            'description' => 'Circular C + क mark',
            'path' => 'images/logo/gk-mark.png',
            'email_path' => 'images/logo/monogram-email.png',
            'width' => 256,
            'height' => 256,
        ],
    ],

];
