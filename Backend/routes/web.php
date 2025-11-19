<?php

$apis = [
    //ENTRIES
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

    //HABITS
    '/habits/create' => [
        'controller' => 'HabitController',
        'method'     => 'createHabit',
    ],
    '/habits/list' => [
        'controller' => 'HabitController',
        'method'     => 'getHabits',
    ],
    '/habits/update' => [
        'controller' => 'HabitController',
        'method'     => 'updateHabit',
    ],
    '/habits/delete' => [
        'controller' => 'HabitController',
        'method'     => 'deleteHabit',
    ],
    
  
    
];
