<?php

session_start();

// Get request via API
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

        <link rel="stylesheet" href="../css/style.css">

        <title>Mood Summary</title>
    </head>
    <body>
    
    <!-- Navbar -->
    <?php include 'nav.php'; ?>

    <!-- Canvas to draw chart -->
    <div class="container-lg custom-container formBorder p-2 custom-margin">

        <div class="card">
            <div class="card-body d-flex justify-content-between">
                <h3>Total Moods Logged</h3>
                <div class="text-end">
                    <select onchange="handleOnChangeDates(this.value)">
                        <option value="last7Days" selected>Last 7 days</option>
                        <option value="last30Days">Last 30 days</option>
                        <option value="lastYear">Last 12 months</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
            <p class="p-3 m-0 text-secondary"><em>By visualizing your mood history, you can gain a clearer understanding of your mood fluctuations, which can be helpful for setting goals and adjusting behaviors</em></p>
            <hr>
            <canvas id="moodCount" class="p-3"></canvas>
        </div>
        

        <div class="card-body d-flex justify-content-between my-3">
                <h4>Choose another chart:</h4>
                <div class="text-end">
                    <a href='chart1.php' class='btn btn-primary small-btn'>Total Moods</a>
                    <a href='chart2.php' class='btn btn-primary small-btn'>Mood Trend</a>
                    <a href='chart3.php' class='btn btn-primary small-btn'>Top Mood Words</a>
                    <a href='chart4.php' class='btn btn-primary small-btn'>Mood Triggers</a>
                </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1"></script>

    <script>
        // Get user ID
        const userId = <?php echo isset($data) ? $data[0]['user_id'] : null; ?>;
        
        
        // Need to run switch once outside function so that last7days displays on initial load of page
        const currentTime = new Date().getTime();
        let selectedTimeframe = 'last7Days';
        let startTime;
            switch (selectedTimeframe) {
                case 'last7Days':
                    startTime = currentTime - (7 * 24 * 60 * 60 * 1000);
                    break;
                case 'last30Days':
                    startTime = currentTime - (30 * 24 * 60 * 60 * 1000);
                    break;
                case 'lastYear':
                    startTime = currentTime - (365 * 24 * 60 * 60 * 1000);
                    break;
                case 'all':
                    startTime = 0;
                    break;
                default:
                    startTime = currentTime;
            }

        // Switch will be run again every time the onChange event handler is triggered
        function handleOnChangeDates(range){
            //const currentTime = new Date().getTime();
            selectedTimeframe = range;

            //let startTime;
            switch (selectedTimeframe) {
                case 'last7Days':
                    startTime = currentTime - (7 * 24 * 60 * 60 * 1000);
                    break;
                case 'last30Days':
                    startTime = currentTime - (30 * 24 * 60 * 60 * 1000);
                    break;
                case 'lastYear':
                    startTime = currentTime - (365 * 24 * 60 * 60 * 1000);
                    break;
                case 'all':
                    startTime = 0;
                    break;
                default:
                    startTime = currentTime;
            }
            updateChart(startTime, currentTime, userId);
        }

        function updateChart(startTime, currentTime, userId) {
            // Use api to get info from users mood cards
            let userMoodCards;
            fetch('http://localhost/PROJECT-APIGithub/api.php?cards&user_id=' + userId)
            .then(function (response) {
                return response.json();
            }).then(function (obj) {
                userMoodCards = obj;
                
                // Filter mood log data based on start timestamp
                const filteredMoodCards = userMoodCards.filter(card => {
                    const cardTimestamp = new Date(card.mood_log_timestamp).getTime();
                    return cardTimestamp >= startTime && cardTimestamp <= currentTime;
                });

                // Count the occurrences of each mood rating value
                const counts = [0, 0, 0, 0, 0];
                filteredMoodCards.forEach(card => {
                    const rating = card.mood_rating_id;
                    if (rating >= 1 && rating <= 5) {
                        counts[rating-1]++;
                    }
                });

                // Update the chart data with the counts
                chart.data.datasets[0].data = counts;
                chart.update();
                

            }).catch(function (error) {
                console.error("Something went wrong retrieving mood log data from db.");
                console.error(error);
            });
        }
        
        const moodCountChart = document.getElementById('moodCount');

        const chart = new Chart(moodCountChart, {
            type: 'bar',
            data: {
                labels: ['Terrible', 'Bad', 'Okay', 'Good', 'Excellent'],
                datasets: [{
                    label: 'Number of times mood was selected',
                    data: [0, 0, 0, 0, 0],
                    borderWidth: 1,
                    backgroundColor: ['rgb(208,79,63)', 'rgb(250,127,86)', 'rgb(245,188,112)', 'rgb(136,207,197)', 'rgb(172,197,99)'],
                    borderWidth: 0
                }]
            },
            options: {
                scales: {
                    y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            usePointStyle: false,
                            boxHeight: 0,
                            boxWidth: 0
                        }
                    }
                }
            }
        });

        updateChart(startTime, currentTime, userId);
        
        
    </script>
    </body>
</html>