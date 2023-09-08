<?php

// UPDATED API CONNECTION
// Get request via API
if (isset($_GET['mood_rating_id'])) {

    $mood_rating_id = $_GET['mood_rating_id'];
    
    $endpointGetMoods = "http://localhost/PROJECT-APIGithub/api.php?get-moods&mood_rating_id=$mood_rating_id";
    $resource = file_get_contents($endpointGetMoods, false, stream_context_create($stream_opts));
    $data = json_decode($resource, true);

    if ($data) {
        echo "<option value=\"\">Select one of the following:</option>";
        foreach ($data as $row) {
            $mood_id = $row['mood_id'];
            $mood_name = $row['mood_name'];
            
            echo "<option value=\"$mood_id\">$mood_name</option>";
        }
    }
    
}


?>
