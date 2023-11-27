<?php
function db_connect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

function register_user($username, $password) {
    $conn = db_connect();

    $username = mysqli_real_escape_string($conn, $username);

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (username, password) VALUES ('$username', '$hashed_password')";
    mysqli_query($conn, $sql);

    // Get the user ID of the newly registered user
    $user_id = mysqli_insert_id($conn);

    mysqli_close($conn);

    return $user_id;
}

function authenticate_user($username, $password) {
    $conn = db_connect();

    $username = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT id, password FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row && password_verify($password, $row['password'])) {
            return $row['id'];
        }
    }

    mysqli_close($conn);
    return false;
}

function validate_file_type($filename) {
    global $allowed_file_types;
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array(strtolower($ext), $allowed_file_types);
}

function sanitize_filename($filename) {
    return preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
}

function get_user_ip() {
    // Handle proxy headers to get the correct IP address if necessary
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function upload_file($user_id, $filename, $temp_file_path) {
  $conn = db_connect();

  $filename = mysqli_real_escape_string($conn, $filename);

  // Specify a secure directory outside the web server's root
  $upload_directory = '/path/to/secure/uploads/';

  // Ensure the directory exists
  if (!is_dir($upload_directory)) {
      // Create the directory with appropriate permissions
      mkdir($upload_directory, 0755, true);
  }

  // Sanitize filename and ensure it doesn't have a script-like extension
  $sanitizedFilename = sanitize_filename($filename);

  // Move the uploaded file to the secure directory
  $destination_path = $upload_directory . $sanitizedFilename;

  if (move_uploaded_file($temp_file_path, $destination_path)) {
      // If the file move is successful, insert into the database
      $sql = "INSERT INTO uploads (user_id, filename, file_path) VALUES ('$user_id', '$sanitizedFilename', '$destination_path')";
      mysqli_query($conn, $sql);

      mysqli_close($conn);
      return true;
  } else {
     
      log_upload($user_id, $filename, 'Failed - Move Error');
      mysqli_close($conn);
      return false;
  }
}

function log_upload($user_id, $filename, $status) {
  $conn = db_connect();

  $filename = mysqli_real_escape_string($conn, $filename);
  $ip_address = mysqli_real_escape_string($conn, get_user_ip());
  $status = mysqli_real_escape_string($conn, $status);
  $timestamp = date('Y-m-d H:i:s'); // Get the current timestamp

  $sql = "INSERT INTO logs (user_id, filename, ip_address, status, upload_timestamp) VALUES ('$user_id', '$filename', '$ip_address', '$status', '$timestamp')";
  mysqli_query($conn, $sql);

  mysqli_close($conn);
}

?>
