<?php

return [
    'name' => 'FileManager',
    'thumbs' => [
        'icon' => [
            'w' => 50,
            'h' => 50,
            'q' => 80,
            'slug' => 'icon',
        ],
        'small' => [
            'w' => 320,
            'h' => 240,
            'q' => 70,
            'slug' => 'small',
        ],
        'low' => [
            'w' => 640,
            'h' => 480,
            'q' => 70,
            'slug' => 'low',
        ],
        'normal' => [
            'w' => 1024,
            'h' => 728,
            'q' => 70,
            'slug' => 'normal',
        ],
    ],
    'images_ext' => [
        'jpg',
        'png',
        'gif',
        'bmp',
        'webp',
    ],
    'static_url' => env('STATIC_URL'),
    'admin_allow_ext' => 'mp4,mpeg,avi,3gp,mov,jpeg,jpg,svg,png,docx,xls,xlsx,csv,pdf,zip,rar,mp3,ogg',
    'front_allow_ext' => 'jpeg,jpg,png,docx,xlsx,zip,rar,mp3,ogg,mp4,mpeg,avi,3gp,mov,svg,xls,csv,pdf',
];
