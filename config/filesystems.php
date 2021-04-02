<?php

return [
    'default' => 'local',
    'disks' => [
        'root' => [
            'driver' => 'local',
            'root' => getenv('HOME'),
        ],

        'local' => [
            'driver' => 'local',
            'root' => getcwd(),
        ],
    ],
];
