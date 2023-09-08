<?php

include "../functions/phpFunctions.php";

// Checking session info
session_start();

// Get request via API
if (isset($_SESSION["user_id"])){

    $user_id = $_SESSION["user_id"];
    
    $endpointGetUser = "http://localhost/PROJECT-APIGithub/api.php?get-user&user_id=$user_id";
    $resource = file_get_contents($endpointGetUser, false);
    $data = json_decode($resource, true);

    $first_name = $data[0]['first_name']; 
}


        
    $log_id = $_GET['log_id'];
    
    $endpoint = "http://localhost/PROJECT-APIGithub/api.php?full-card&log_id=$log_id";
    $resource = file_get_contents($endpoint, false);
    $data = json_decode($resource, true);

    // Extract data
    $timestamp = $data[0][0]['mood_log_timestamp'];
    $format_datetime = date("l d-m-y,  H:i", strtotime($timestamp));
    
    $mood_rating = $data[0][0]['mood_rating_id'];
    $mood_name = $data[0][0]['mood_name'];
    $diary_entry_id = $data[0][0]['diary_entry_id']; 
    $diary_entry = $data[0][0]['diary_entry_text'];

    // Replace mood rating number with image
    if($mood_rating == 1){
        $mood_rating = "../img/terrible.webp";
    }else if($mood_rating == 2){
        $mood_rating = "../img/bad.webp";
    }else if($mood_rating == 3){
        $mood_rating = "../img/okay.webp";
    }else if($mood_rating == 4){
        $mood_rating = "../img/good.webp";
    }else if($mood_rating == 5){
        $mood_rating = "../img/excellent.webp";
    }

    $triggers = array();
    // Iterate over the data[1] array and store the trigger names in an array
    foreach ($data[1] as $key => $value) {
        $triggers[$key] = $value['trigger_name'];
    }

    // Access the trigger names
    //echo $triggers[0];
    //echo $triggers[1]; 

    $encoded_triggers = json_encode($triggers);

?>

<!doctype html>
<html lang="en">
  <head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="../css/style.css">
    

    <title>Mood Mapper</title>

  </head>
  <body>
        <!-- Navbar -->
        <?php include 'nav.php'; ?>

        

        <!-- Display selected mood log -->
        <div class="container my-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="text-center">
                            <img src=<?php echo $mood_rating ?> class="img-fluid rounded-start" alt="emoji face" style="width:15%;height:15%;">
                        </div>
                        <div class="card-body">
                            <h1 class="text-center">~~~ <?php echo $mood_name ?> ~~~</h1>
                            <p class='card-text text-center'><small class='text-muted'>Post created: <?php echo $format_datetime ?></small></p>
                            <div class="m-2" id="q4div">
                                <h4 class="mb-3 text-center">Diary</h4>
                                    <div class="form-group">
                                        <textarea class="form-control" id="diary-entry" name="diary-entry" rows="4" maxlength="500" disabled><?php echo $diary_entry ?></textarea>
                                    </div>
                            </div>

                            <!-- Q3 -->
                            <div class="q3-container pb-4 m-2" id="q3div">
                                <h4 class="mb-3 text-center">Did anything trigger your mood today?</h4>
                                <p>Select multiple:</p>
                                <div class="form-group">
                                    <?php


                                        $endpointGetMoods = "http://localhost/PROJECT-APIGithub/api.php?mood-triggers";
                                        $resource = file_get_contents($endpointGetMoods, false);
                                        $triggerResults = json_decode($resource, true);

                                        if(!$triggerResults){
                                            exit($conn->error);
                                        } else{
                                            foreach($triggerResults as $row){
                                                $triggerDisplay = $row['trigger_name'];
                                                $trigID = $row['trigger_id'];

                                                if (in_array($triggerDisplay, $triggers)){
                                                    echo "<div class='col-sm-4 col-xl-2 form-check form-check-inline'>
                                                        <label class='form-check-label' for='trigger[]'>
                                                            <input class='form-check-input' type='checkbox' id='$triggerDisplay' name='trigger[]' value='$trigID' checked>$triggerDisplay
                                                        </label>
                                                        
                                                    </div>";
                                                }else{
                                                    echo "<div class='col-sm-4 col-xl-2 form-check form-check-inline'>
                                                        <label class='form-check-label' for='trigger[]'>
                                                            <input class='form-check-input' type='checkbox' id='$triggerDisplay' name='trigger[]' value='$trigID' >$triggerDisplay
                                                        </label>
                                                        
                                                    </div>";
                                                }
                                            }
                                        }

                                    ?>
                                </div>
                            </div>
                            <div class='m-2 d-flex justify-content-between align-items-center'>
                                <div>
                                    <a href='./moodlist.php' class='btn btn-primary'>< Back to list</a>
                                </div>
                                <div> 
                                    <button id="edit-post" class="btn btn-primary">Edit diary and mood triggers</button>
                                </div>
                                
                            </div>
                            
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="hiddenLogId" value="<?= $log_id ?>">
        <input type="hidden" id="hiddenDiaryId" value="<?= $diary_entry_id ?>">
        <script src="../js/ajaxEditpost.js"></script>
    </body>
</html>