<?php 

$apis = [
    // existing ones ...
    '/entries/create'  => ['controller' => 'EntryController', 'method' => 'createEntry'],
    '/entries/list'    => ['controller' => 'EntryController', 'method' => 'getEntries'],
    '/entries/update'  => ['controller' => 'EntryController', 'method' => 'updateEntry'],
    '/entries/delete'  => ['controller' => 'EntryController', 'method' => 'deleteEntry'],

    '/habits/create'   => ['controller' => 'HabitController',  'method' => 'createHabit'],
    '/habits/list'     => ['controller' => 'HabitController',  'method' => 'getHabits'],
    '/habits/update'   => ['controller' => 'HabitController',  'method' => 'updateHabit'],
    '/habits/delete'   => ['controller' => 'HabitController',  'method' => 'deleteHabit'],

    // 🔹 NEW: stats for charts
    '/entries/stats'        => ['controller' => 'EntryController', 'method' => 'stats'],

    // 🔹 NEW: AI features
    '/ai/weekly-summary'    => ['controller' => 'AiController',    'method' => 'weeklySummary'],
    '/ai/nutrition-coach'   => ['controller' => 'AiController',    'method' => 'nutritionCoach'],

    // 🔹 NEW: admin panel APIs
    '/admin/entries'        => ['controller' => 'AdminController', 'method' => 'listEntries'],
    '/admin/habits'         => ['controller' => 'AdminController', 'method' => 'listHabits'],
];

