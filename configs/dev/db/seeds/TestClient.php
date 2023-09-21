<?php
return [
    'gems__oauth_clients' => [
        [
            'id' => 1,
            'client_id' => 'test',
            'name' => 'Test Client',
            'secret' => password_hash('test123', PASSWORD_DEFAULT),
            'active' => 1,
            'confidential' => 1,
        ],
    ],
];