<?php

include "../functions/phpFunctions.php";

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


// Writing mood log form to db via API
// First get values
if (isset($_POST['submitted'])) {
    $moodRating = $_POST['moodRating'];
    $moodID = $_POST['moodName'];
    $diaryEnt = $_POST['diaryEntry'];
    $user_id = $_SESSION["user_id"];

    if(!empty($_POST['trigger'])){
        $triggerArray = $_POST['trigger'];
        $postData = http_build_query(
            array(
                'moodRating' => $moodRating,
                'moodName' => $moodID,
                'diaryEntry' => $diaryEnt,
                'trigger' => implode(',', $triggerArray),
                'user_id' => $user_id
            )
        );
    }else{
        $postData = http_build_query(
            array(
                'moodRating' => $moodRating,
                'moodName' => $moodID,
                'diaryEntry' => $diaryEnt,
                'user_id' => $user_id
            )
        );
    }

    $endpointPostMoodLog = "http://localhost/PROJECT-APIGithub/api.php?submit-mood-log";

    $opts = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData
        )
    );

    $context = stream_context_create($opts);
    $resource = file_get_contents($endpointPostMoodLog, false, $context);

    if($resource === FALSE){
        exit("Unable to add form details to the database!");
    }
    
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
            
        <div class="container titleBorder my-3">
            <h2 class="my-4">List of your previously recorded moods:</h2>
        </div>
        
        <div class="container my-5">
            <div class="row g-5 justify-content-start">

                <?php

                    // Reading DB info for displaying cards

                    if (isset($_SESSION["user_id"])){
                        
                        $user_id = $_SESSION["user_id"];

                        // Determine which page user is currently on
                        if (!isset($_GET['page'])) {
                            $page = 1;
                        } else {
                            $page = $_GET['page'];
                        }

                        $limit = 8;
                        
                        $endpoint = "http://localhost/PROJECT-APIGithub/api.php?user_id=$user_id&page=$page&limit=$limit";
                        $resource = file_get_contents($endpoint, false);
                        $data = json_decode($resource, true);

                        // Number of results
                        $number_of_results = $data['count'];

                        if($number_of_results < 1){
                            echo    "<div class='card mb-3 p-3 my-3'>
                                        <div class='row g-0'>
                                            <div class='col'>
                                                <div class='d-flex justify-content-between align-items-center'>
                                                    <div class='card-body align-items-center' style='width: 100%;'>
                                                        <h5 class='card-title'>No moods logged yet!</h5>
                                                        <p>Once you start logging moods, they'll be summarised here. You'll also be able to edit/delete posts.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                        }else {
                    
                            foreach($data as $key => $row){

                                if ($key === 'count') {
                                    // skip the count property
                                    continue;
                                }

                                $log_id = $row['mood_log_id'];
                                $datetime = $row['mood_log_timestamp'];
                                $mood_rating = $row['mood_rating_id'];
                                $mood = $row['mood_name'];
                                
                                $diary = $row['diary_entry_text'];

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

                                $format_datetime = date("d-m-y,  H:i", strtotime($datetime));

                                echo    "<div class='col-lg-6 '>
                                            <div class='card '>
                                                <div class='row g-0'>
                                                    <div class='col-3 '>
                                                        <img src=$mood_rating class='card-img' alt='emoji face'>
                                                    </div>
                                                    <div class='col-5'>
                                                        <div class='card-body '>
                                                            <div class='h-100'>
                                                                <h5 class='card-title'>$mood</h5>
                                                                <p class='card-text '>$diary</p>
                                                                <p class='card-text'><small class='text-muted'>$format_datetime</small></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class='col-4 d-flex align-items-center'>
                                                        <div class='card-footer' style='border: none;'>
                                                            <a href='displaycard.php?log_id=$log_id' class='btn btn-primary'>View</a>
                                                        </div>
                                                        <div class='card-footer' style='border: none;'>
                                                            <a href='deleteCard.php?log_id=$log_id' class='btn btn-danger' id='deleteBtn' value='Delete'>Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                            }

                            
                                    
                        }

                    }
                    
                ?>
            </div>
        </div> <!-- End of cards div -->

        <?php
        
        // Determine total no. of pages needed
        $number_of_pages = ceil($number_of_results / $limit);

        // Determine the sql LIMIT starting number for the results on displaying page
        
        $this_page_first_result = ($page-1) * $limit;

        ?>

        <!-- Page navigation -->
        <div class="container">
            <nav aria-label="Select page:">
                <ul class="pagination justify-content-center">

                    <!-- Previous Page Link -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="moodlist.php?page=<?php echo $page - 1; ?>" tabindex="-1">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                    <?php endif; ?>

                    <!-- Numbered Page Links -->
                    <?php for ($i = 1; $i <= $number_of_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <li class="page-item active" aria-current="page">
                                <span class="page-link"><?php echo $page; ?><span class="sr-only"></span></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="moodlist.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Next Page Link -->
                    <?php if ($page < $number_of_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="moodlist.php?page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Next</span>
                        </li>
                    <?php endif; ?>

                </ul>
            </nav>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    </body>
</html>