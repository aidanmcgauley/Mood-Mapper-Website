<?php

$log_id = $_GET['log_id'];

$endpointCardDelete = "http://localhost/PROJECT-APIGithub/api.php?delete-card&log_id=$log_id";

$context = stream_context_create([
    'http' => [
        'method' => 'DELETE',
    ],
]);

$response = file_get_contents($endpointCardDelete, false, $context);

if ($response === false) {
    echo "Error sending DELETE request to API endpoint.";
} else {
    echo "DELETE request sent successfully. Response: " . $response;
}

header('Location: http://localhost/PROJECTGithub/src/moodlist.php');

?>