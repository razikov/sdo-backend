<?php

if (file_exists(__DIR__ . '/_users.php')) {
    $users = require __DIR__ . '/_users.php';
} else {
    $users = require __DIR__ . '/users.php';
}

return [
    'adminEmail' => 'admin@example.com',
    'useTree' => 'mp', //'ns', 'mp', 'as',
    'users' => $users,
];
