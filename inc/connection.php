<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '\database.db');
} catch (Exception $e) {
    echo 'Unable to connect to database';
    exit;
}
echo 'Connected to database';