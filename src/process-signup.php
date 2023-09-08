<?php

// In unlikely event that client-side validation is bypassed:

if (empty($_POST["username"])) {
    die("Username is required");
}
if (empty($_POST["firstname"])) {
    die("First name is required");
}
if (empty($_POST["surname"])) {
    die("Last name is required");
}

if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email address required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (! preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["confirmpassword"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);


// making call to api
$postData = http_build_query(
    array(
        'username' => $_POST["username"],
        'firstname' => $_POST["firstname"],
        'surname' => $_POST["surname"],
        'email' => $_POST["email"],
        'passwordHash' =>  $password_hash
    )
);

$endpoint = "http://localhost/PROJECT-APIGithub/api.php?signup";

$opts = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $postData
    )
);

$context = stream_context_create($opts);
$response = file_get_contents($endpoint, false, $context);

$json = json_decode($response);

if($response === FALSE){
    exit("Unable to add form details to the database!");
}

if ($json->success) {
    session_start();
    $_SESSION['logged_in'] = true;
    session_regenerate_id();
    $_SESSION['user_id'] = $json->user_id;
    header('Location: http://localhost/PROJECTGithub/src/index.php');
    exit;
} else {
    echo 'Signup failed: ' . $json->message;
}


?>
