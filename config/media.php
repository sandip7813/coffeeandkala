<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Profile Photo
    |--------------------------------------------------------------------------
    |
    | Settings for user profile picture uploads: the disk and directory used
    | for storage, the accepted file extensions, the maximum upload size
    | (in kilobytes), and the dimensions used when generating a thumbnail.
    |
    */

    'profile_photo' => [
        'disk' => 'public',
        'directory' => 'profile-photos',
        'thumbnail_directory' => 'profile-photos/thumbnails',
        'formats' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_size_kb' => 2048,
        'thumbnail' => [
            'width' => 150,
            'height' => 150,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery & Studio Media
    |--------------------------------------------------------------------------
    |
    | Settings for admin-uploaded Gallery and Studio images: the disk and
    | directories used for storage (original, large, and thumbnail), the
    | accepted file extensions, the maximum upload size (in kilobytes), and
    | the dimensions used when generating the large and thumbnail copies.
    |
    */

    'gallery' => [
        'disk' => 'public',
        'directory' => 'gallery',
        'thumbnail_directory' => 'gallery/thumbnails',
        'large_directory' => 'gallery/large',
        'formats' => ['jpg', 'jpeg', 'png', 'webp'],
        // Capped to fit the server's current upload_max_filesize (2M in
        // /etc/php/8.5/cli/php.ini) — raise both together if that's ever increased.
        'max_size_kb' => 2048,
        'thumbnail' => [
            'width' => 400,
            'height' => 400,
        ],
        'large' => [
            'width' => 1600,
            'height' => 1600,
        ],
    ],

    'studio' => [
        'disk' => 'public',
        'directory' => 'studio',
        'thumbnail_directory' => 'studio/thumbnails',
        'large_directory' => 'studio/large',
        'formats' => ['jpg', 'jpeg', 'png', 'webp'],
        // Capped to fit the server's current upload_max_filesize (2M in
        // /etc/php/8.5/cli/php.ini) — raise both together if that's ever increased.
        'max_size_kb' => 2048,
        'thumbnail' => [
            'width' => 400,
            'height' => 400,
        ],
        'large' => [
            'width' => 1600,
            'height' => 1600,
        ],
    ],

];
