<?php
// Backend/seed.php

require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/models/Habit.php';

// 1) choose a user_id that already exists in your users table
//    you can check in phpMyAdmin: SELECT * FROM users;
$userId = 3; // <-- change this if your user has a different id

// 2) SEED HABITS (only if you want some default habits)
$habits = [
    [
        'name'         => 'sleep',
        'entry_field'  => 'sleep_hours',
        'unit'         => 'hour/s',
        'target_value' => 8,
    ],
    [
        'name'         => 'steps',
        'entry_field'  => 'steps_count',
        'unit'         => 'step/s',
        'target_value' => 8000,
    ],
    [
        'name'         => 'exercise',
        'entry_field'  => 'exercise_minutes',
        'unit'         => 'min/s',
        'target_value' => 30,
    ],
    [
        'name'         => 'coffee',
        'entry_field'  => 'caffeine_cups',
        'unit'         => 'cup/s',
        'target_value' => 2,
    ],
    [
        'name'         => 'water',
        'entry_field'  => 'water_liters',
        'unit'         => 'liter/s',
        'target_value' => 2,
    ],
    [
        'name'         => 'mood',
        'entry_field'  => 'mood_score',
        'unit'         => 'm',
        'target_value' => 7,
    ],
];

foreach ($habits as $h) {
    $data = [
        'user_id'      => $userId,
        'name'         => $h['name'],
        'entry_field'  => $h['entry_field'],
        'unit'         => $h['unit'],
        'target_value' => $h['target_value'],
        'is_active'    => 1,
    ];

    Habit::create($connection, $data);
}

echo "Seeded default habits.\n";

// 3) SEED ENTRIES (random data over last 30 days)
$start = new DateTime('-29 days'); // 30 days including today
$end   = new DateTime('today');

$stmt = $connection->prepare(
    "INSERT INTO entries 
        (user_id, raw_text, sleep_hours, steps_count, exercise_minutes, caffeine_cups, water_liters, mood_score, created_at, updated_at)
     VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die("Prepare failed: " . $connection->error);
}

for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
    $dateStr   = $d->format('Y-m-d');
    $createdAt = $dateStr . ' ' . sprintf('%02d:%02d:%02d', rand(6, 22), rand(0, 59), rand(0, 59));

    // random but realistic values
    $sleepHours      = rand(5, 9);            // 5–9 hours
    $stepsCount      = rand(3000, 12000);     // 3k–12k steps
    $exerciseMinutes = rand(0, 60);          // 0–60 min
    $coffeeCups      = rand(0, 4);           // 0–4 cups
    $waterLiters     = rand(10, 30) / 10.0;  // 1.0–3.0 L
    $moodScore       = rand(4, 9);           // 4–9

    $rawText = sprintf(
        "Walked %d min, %d coffees, slept %d hours, drank %.1fL water, mood %d/10, %d steps",
        $exerciseMinutes,
        $coffeeCups,
        $sleepHours,
        $waterLiters,
        $moodScore,
        $stepsCount
    );

    $updatedAt = $createdAt;

    $stmt->bind_param(
        'isdiiidiss',
        $userId,
        $rawText,
        $sleepHours,
        $stepsCount,
        $exerciseMinutes,
        $coffeeCups,
        $waterLiters,
        $moodScore,
        $createdAt,
        $updatedAt
    );

    if (!$stmt->execute()) {
        echo "Failed insert for {$dateStr}: " . $stmt->error . "\n";
    }
}

$stmt->close();

echo "Seeded entries for last 30 days.\n";
