<?php
require_once 'config.php';
require_once 'functions.php';

$testUsername = 'test_user';
$testPassword = 'test_password';
$userId = authenticate_user($testUsername, $testPassword);

if (!$userId) {
    die('Test user authentication failed');
}

// Test scenarios

// Scenario 1: Successful file upload
echo "Scenario 1: Successful file upload\n";
$successFile = [
    'name' => 'success_file.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => '/tmp/phpabc',
    'error' => 0,
    'size' => 1024 // Adjust the size as needed
];

simulateFileUpload($userId, $successFile);

// Scenario 2: Rejected file upload (invalid file type)
echo "\nScenario 2: Rejected file upload (invalid file type)\n";
$invalidFileType = [
    'name' => 'invalid_file.exe',
    'type' => 'application/octet-stream',
    'tmp_name' => '/tmp/phpabc',
    'error' => 0,
    'size' => 1024
];

simulateFileUpload($userId, $invalidFileType);

// Scenario 3: Rejected
