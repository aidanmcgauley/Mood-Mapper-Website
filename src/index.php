<?php

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

    <title>Mood Mapper</title>
  </head>
<body>
  
  <!-- Navbar -->
  <?php include 'nav.php'; ?>
  
  <!-- Index Container -->
  <div class="container-lg custom-container index-container custom-margin text-center">
    <h4 class="py-2">Welcome to Mood Mapper!</h4>
      <!-- Opening Paragraph -->
      <div class="opening-paragraph p-2">
        Welcome to Mood Mapper, your personalized companion on the journey to emotional well-being. Our mission is to help you navigate the complexities of your emotions by providing a safe and intuitive platform to track and visualize your moods. With the guiding principle of 'map your moods, find your balance', Mood Mapper empowers you to take control of your emotional health by identifying patterns, triggers, and trends in your feelings. By fostering self-awareness and promoting a deeper understanding of your emotions, we aim to create a harmonious and balanced life for our users. Join us today and embark on a transformative journey of self-discovery, emotional growth, and holistic well-being.
      </div>
      <hr>
      <!-- Step by Step Guide -->
      <div class="step-by-step-guide">
        <h4>How it works</h4>

          <p>Mood Mapper simplifies the process of tracking your emotions through an intuitive and user-friendly interface. To get started, users answer a few straightforward questions that help them log their current mood, along with an optional diary entry for additional context.<p>

          <div class="image-border mb-4">
            <img src="../img/carousel1.png">
          </div>

          <div class="arrow-img">
            <img src="../img/arrow.png">
          </div>
          
          <p>Once a mood has been logged, the platform stores and lists the user's past mood logs, allowing them to easily edit or delete previous entries as needed.</p>

          <div class="image-border mb-4">
            <img src="../img/carousel2.png">
          </div>
          <div class="arrow-img">
            <img src="../img/arrow.png">
          </div>

          <p>Finally, Mood Mapper transforms the collected data into a series of visually engaging and easily interpreted charts, providing users with valuable insights into their emotional patterns and trends over time. Discover the benefits of mood tracking and find your balance with Mood Mapper today!</p>

          <div class="image-border mb-4">
            <img src="../img/carousel3.png">
          </div>
      </div>

      <hr>
      <!-- Fake testimonials -->
      <div class="funny-testimonials pb-3">
        <br>
        <h5 class="pb-2">Testimonials</h5>
        <p><em>"Mood Mapper turned me into an emotional cartographer! Now I can navigate my feelings like a pro, with a compass that always points to 'feeling awesome'!" - John D.</em></p>

        <p><em>"Who needs a weather forecast when you have Mood Mapper? I can predict my emotional highs and lows better than the local meteorologist predicts the rain!" - Timmy T.</em></p>
        
        <p><em>"Before Mood Mapper, I was lost in an emotional jungle. But now, I can confidently swing from vine to vine, mastering my moods with ease!" - Jane J.</em></p>
      </div>
  </div>
  


    
    
    
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/1ZAA6CxhNmO60w8nUw41apMpq6j55O5D21n8kF" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
  </body>
</html>