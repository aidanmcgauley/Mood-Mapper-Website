<?php

include "../functions/phpFunctions.php";

session_start();

// Get request via API
if (isset($_SESSION["user_id"])){
    
    $user_id = $_SESSION["user_id"];
    
    $endpointGetUser = "http://localhost/PROJECT-APIGithub/api.php?get-user&user_id=$user_id";
    $resource = file_get_contents($endpointGetUser, false);
    $data = json_decode($resource, true);

    $first_name = $data[0]['first_name']; 
}

?>

<!doctype html>
<html lang="en">
  <head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

    <link rel="stylesheet" href="../css/style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <title>Mood List</title>

  </head>
  <body>
        <!-- Navbar -->
        <?php include 'nav.php'; ?>

        <div class="container formBorder my-5 text-center" style="max-width: 50%">
        <form method="post">
            <h3 class="my-3">Are you sure you want to delete your account?</h3>
            <div class="text-center my-5">
                <button type="submit" class="button btn btn-primary btn-lg" name="no" value="no">No</button>
                <button type="submit" class="button btn btn-primary btn-lg" name="yes" value="yes">Yes</button>
            </div>
        </form>
        </div>



<?php

if (isset($_POST['no'])) {
    header("Location: index.php");
    exit;
} else if (isset($_POST['yes'])) {
    // perform delete account action
    $endpointAccountDelete = "http://localhost/PROJECT-APIGithub/api.php?delete-account&user_id=$user_id";

    $data = array('user_id' => $user_id);
    $options = array(
        'http' => array(
            'method' => 'DELETE',
            'header' => 'Content-Type: application/json'
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($endpointAccountDelete, false, $context);
    if ($result === false) {
        echo "Error sending DELETE request to API endpoint.";
    } else {
        $response = json_decode($result, true);
        if (isset($response['status']) && $response['status'] === 'success') {
            echo "Account deleted successfully.";
            header('Location: http://localhost/PROJECTGithub/index.php');
            exit();
        } else {
            echo "Error deleting account: " . $response['message'];
        }
    }
    session_destroy();
    header("Location: index.php");
    exit;
}


?>