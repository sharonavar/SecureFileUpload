<?php
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'");

require_once 'config.php';
require_once 'functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = authenticate_user($_SESSION['username'], $_SESSION['password']);

    if (!$user_id) {
        die('Authentication failed');
    }

    if (!validate_file_type($_FILES['file']['name'])) {
        log_upload($user_id, $_FILES['file']['name'], $_SERVER['REMOTE_ADDR'], 'rejected');
        die('Invalid file type');
    }

    if ($_FILES['file']['size'] > MAX_FILE_SIZE) {
        log_upload($user_id, $_FILES['file']['name'], $_SERVER['REMOTE_ADDR'], 'rejected');
        die('File size exceeds limit');
    }

    $filename = sanitize_filename($_FILES['file']['name']);
    $upload_path = UPLOAD_DIR . $filename;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
        upload_file($user_id, $filename);
        log_upload($user_id, $filename, $_SERVER['REMOTE_ADDR'], 'success');
        echo 'File uploaded successfully';
    } else {
        log_upload($user_id, $filename, $_SERVER['REMOTE_ADDR'], 'suspicious');
        echo 'Failed to upload file';
    }
} else {
    echo 'Invalid request method';
}
?>
