<?php

// Checking session info
session_start();

if (isset($_SESSION["user_id"])){
    
    $user_id = $_SESSION["user_id"];
    
    $endpointGetUser = "http://localhost/PROJECT-APIGithub/api.php?get-user&user_id=$user_id";
    $resource = file_get_contents($endpointGetUser, false);
    $data = json_decode($resource, true);

    $first_name = $data[0]['first_name']; 
}else {
    header("Location: index.php");
    exit();
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

    <title>Log your mood</title>

  </head>
  <body>

    <!-- Navbar -->
    <?php include 'nav.php'; ?>

        <!-- Mood logging form -->

        <div class="container-lg custom-container">
            
        <div class="row justify-content-center custom-container custom-margin">
            <div class="formBorder">
                <div class="text-center">
                    <h1 class="py-4">Mood logging session</h1>
                </div>

                <form action="moodlist.php" method="post">

                    <!-- Q1 -->
                    <div class="q1-container m-2 " id="q1div">
                        <h4>How are you feeling today? *</h4>
                        <div class="radio-buttons">

                            <label class="custom-radio">
                                <input type="radio" name="moodRating" id="terrible" value="1" required>
                                <span class="radio-btn"><i class="fa-solid fa-check"></i>
                                    <div class="face-img">
                                        <img src="../img/terrible.webp">
                                        <h5>Terrible</h5>
                                    </div>
                                </span>
                            </label>

                            <label class="custom-radio">
                                <input type="radio" name="moodRating" id="bad" value="2" required>
                                <span class="radio-btn"><i class="fa-solid fa-check"></i>
                                    <div class="face-img">
                                        <img src="../img/bad.webp">
                                        <h5>Bad</h5>
                                    </div>
                                </span>
                            </label>

                            <label class="custom-radio">
                                <input type="radio" name="moodRating" id="okay" value="3" required>
                                <span class="radio-btn"><i class="fa-solid fa-check"></i>
                                    <div class="face-img">
                                        <img src="../img/okay.webp">
                                        <h5>Okay</h5>
                                    </div>
                                </span>
                            </label>

                            <label class="custom-radio">
                                <input type="radio" name="moodRating" id="good" value="4" required>
                                <span class="radio-btn"><i class="fa-solid fa-check"></i>
                                    <div class="face-img">
                                        <img src="../img/good.webp">
                                        <h5>Good</h5>
                                    </div>
                                </span>
                            </label>

                            <label class="custom-radio">
                                <input type="radio" name="moodRating" id="excellent" value="5" required>
                                <span class="radio-btn"><i class="fa-solid fa-check"></i>
                                    <div class="face-img">
                                        <img src="../img/excellent.webp">
                                        <h5>Excellent</h5>
                                    </div>
                                </span>
                            </label>

                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="m-2 " id="q2div">
                        <h4 class="mb-3 text-center">What word best describes your mood? *</h4>
                        <div class="form-group">
                            <select name ="moodName"id="moodName" class="mb-3 form-select" required>
                                <option value="0" disabled selected>Select one of the following:</option>
                                <script src="../js/ajaxQ2.js"></script>
                                <?php include 'get_moods.php'; ?>
                            </select>
                        </div>
                    </div>
                    
                    
                    <!-- Q3 -->
                    <div class="q3-container pb-4 m-2 " id="q3div">
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
                                        $trigger = $row['trigger_name'];
                                        $trigID = $row['trigger_id'];
                                        echo "<div class='form-check form-check-inline'>
                                                <label class='form-check-label' for='trigger[]'>
                                                    <input class='form-check-input' type='checkbox' id='$trigger' name='trigger[]' value='$trigID'>$trigger
                                                </label>
                                                
                                            </div>";
                                    }
                                }

                            ?>
                        </div>
                    </div>

                    <!-- Q4 Diary entry -->
                    <div class="m-2 " id="q4div">
                        <h4 class="mb-3 text-center">Diary</h4>
                            <div class="form-group">
                                <textarea class="form-control" id="diaryEntry" name="diaryEntry" rows="4" placeholder="Is there anything else you'd like to add? (500 characters)" maxlength="500"></textarea>
                            </div>
                    </div>
                    
                    <!-- Submit button -->
                    <div class="m-4 text-center ">
                        <input type="submit" class="button btn btn-primary btn-lg" name="submitted" value="Submit">
                    </div>

                </form>
            </div>
        </div>
            
        </div>

        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>