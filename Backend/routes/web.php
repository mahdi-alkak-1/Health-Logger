<?php

$apis = [
    // ENTRIES CRUD
    '/entries/create' => [
        'controller' => 'EntryController',
        'method'     => 'createEntry',
    ],
    '/entries/list' => [
        'controller' => 'EntryController',
        'method'     => 'getEntries',
    ],
    '/entries/update' => [
        'controller' => 'EntryController',
        'method'     => 'updateEntry',
    ],
    '/entries/delete' => [
        'controller' => 'EntryController',
        'method'     => 'deleteEntry',
    ],
];


?>