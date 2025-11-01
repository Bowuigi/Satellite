<?php
$name = $_POST['username'];
$password = $_POST['password'];

// Server side validation is simple, client-side is nicer to the user
if ( !isset($name) || !isset($password)
  || preg_match("[a-z0-9_\\.]{1,}", $name)
  || preg_match("\\p{Graphic}{6,100}", $password)) {
    http_response_code(401); // Bad request
    exit;
}

$password_hash = password_hash($password, PASSWORD_BCRYPT);

session_start();
$_SESSION['username'] = $name;
$_SESSION['password'] = $password_hash;
?>
