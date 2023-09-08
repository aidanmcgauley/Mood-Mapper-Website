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

        <script src="./js/statusLoggedIn.js"></script>

        <title>Mood Summary</title>
    </head>
    <body>
    
    <!-- Navbar -->
    <?php include 'nav.php'; ?>

        <!-- Canvas to draw chart -->
        <div class="container-lg custom-container formBorder p-2 custom-margin">

            <div class="card">
                <div class="card-body d-flex justify-content-between">
                    <h3>Your mood trend over time</h3>
                    <div class="text-end">
                        <select onchange="handleOnChangeDates(this.value)">
                            <option value="last7Days" selected>Last 7 days</option>
                            <option value="last30Days">Last 30 days</option>
                            <option value="lastYear">Last 12 months</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
                <p class="p-3 m-0 text-secondary"><em>This graph helps identify patterns in your moods over time by showing the highs and lows of your mood logs from oldest to most recent in the selected timeframe</em></p>
                <p class="p-3 m-0">1 = Terrible  :   5 = Excellent</p>
                <hr>
                <canvas id="moodsOverTime" class="p-3"></canvas>
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
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>


        <script>

            let chart = null;

            // Get user ID
            const userId = <?php echo isset($data) ? $data[0]['user_id'] : null; ?>;

            function renderChart(timestampAndRating, timeRange) {
                const moodsByTime = document.getElementById('moodsOverTime');

                if (chart !== null) {
                    chart.destroy();
                }

                chart = new Chart(moodsByTime, {
                        type: 'line',
                        data: {
                            datasets: [{
                                label: 'Mood rating',
                                data: timestampAndRating.map(obj => ({ x: obj.timestamp, y: obj.rating })),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: timeRange,
                                        tooltipFormat: 'dd/MM/yy HH:mm',
                                        displayFormats: {
                                            hour: 'dd/MM/yy HH:mm',
                                            day: 'dd/MM/yy',
                                            week: 'dd/MM/yy',
                                            month: 'MMM yyyy',
                                            quarter: '[Q]Q yyyy',
                                            year: 'yyyy'
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: false,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
            }


            function updateChart(startTime, currentTime, userId) {
                // Code to fetch data from API
                fetch('http://localhost/PROJECT-APIGithub/api.php?line-chart&user_id=' + userId)
                .then(function (response) {
                    return response.json();
                }).then(function (obj) {
                    apiData = obj;

                    // Filter mood log data based on start timestamp
                    const timeFilteredData = apiData.filter(card => {
                    const cardTimestamp = new Date(card.mood_log_timestamp).getTime();
                    return cardTimestamp >= startTime && cardTimestamp <= currentTime;
                    });
                    //console.log(timeFilteredData);

                    // In separate arrays
                    const timestamps = timeFilteredData.map(card => new Date(card.mood_log_timestamp).getTime());
                    const ratings = timeFilteredData.map(card => card.mood_rating_id);

                    // Data in same array
                    const timestampAndRating = timeFilteredData.map(card => {
                    const timestamp = new Date(card.mood_log_timestamp).getTime();
                    const rating = card.mood_rating_id;
                    return { timestamp, rating };
                    });

                    //console.log("User: "+ userId);
                    //console.log(timestampAndRating);

                    const timeRange = determineTimeUnit(timestamps);
                    renderChart(timestampAndRating, timeRange);

                }).catch(function (error) {
                console.error("Something went wrong retrieving mood log data from db.");
                console.error(error);
                });

                
            }

            function determineTimeUnit(timestamps) {
            const maxTimestamp = Math.max(...timestamps);
            const minTimestamp = Math.min(...timestamps);
            const timeDiff = maxTimestamp - minTimestamp;

                if (timeDiff > 365 * 24 * 60 * 60 * 1000) {
                    // If the time difference is greater than 1 year, use 'year' as the unit
                    return 'year';
                } else if (timeDiff > 30 * 24 * 60 * 60 * 1000) {
                    // If the time difference is greater than 1 month, use 'month' as the unit
                    return 'month';
                } else if (timeDiff > 7 * 24 * 60 * 60 * 1000) {
                    // If the time difference is greater than 1 week, use 'day' as the unit
                    return 'day';
                } else {
                    // Otherwise, use 'hour' as the unit
                    return 'hour';
                }
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


            // Run switch once to display 7 days on intial loading of page
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


            updateChart(startTime, currentTime, userId);

            


        </script>

</body>

</html>