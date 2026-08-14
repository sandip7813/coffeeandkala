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

];
