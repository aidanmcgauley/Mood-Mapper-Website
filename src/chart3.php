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
                    <h3>Top Mood Words</h3>
                    <div class="text-end">
                        <select onchange="handleOnChangeDates(this.value)">
                            <option value="last7Days" selected>Last 7 days</option>
                            <option value="last30Days">Last 30 days</option>
                            <option value="lastYear">Last 12 months</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
                <p class="p-3 m-0 text-secondary"><em>"Tracking which adjectives you choose most often can help you understand the moods you have a natural inclination towards, and which ones you may struggle with"</em></p>
                <hr>
                <canvas id="mostCommonMoodWords" class="p-3"></canvas>
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

        let chart = new Chart();

        // Get user ID
        const userId = <?php echo isset($data) ? $data[0]['user_id'] : null; ?>;

        
        // RENDER block
        function renderChart(newLabels, newData){
        const moodCountChart = document.getElementById('mostCommonMoodWords');

        chart = new Chart(moodCountChart, {
            type: 'bar',
            data: {
                labels: newLabels,
                datasets: [{
                    label: 'Top mood words chosen to describe your mood',
                    data: newData,
                    borderWidth: 1,
                    backgroundColor: [  'rgb(95, 158, 160)',  'rgb(228, 117, 125)',  'rgb(170, 122, 190)',  'rgb(207, 94, 98)',  'rgb(252, 215, 109)',  'rgb(119, 166, 214)',  'rgb(170, 204, 87)',  'rgb(225, 160, 87)',  'rgb(132, 179, 155)',  'rgb(255, 153, 102)'],
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
        }


        function updateChart(startTime, currentTime, userId) {
            chart.destroy();
            // Code to fetch mood cards from API
            fetch('http://localhost/PROJECT-APIGithub/api.php?cards&user_id=' + userId)
            .then(function (response) {
                return response.json();
            }).then(function (obj) {
                userMoodCards = obj;

                // Filter mood log data based on start timestamp
                const timefilteredMoodCards = userMoodCards.filter(card => {
                    const cardTimestamp = new Date(card.mood_log_timestamp).getTime();
                    return cardTimestamp >= startTime && cardTimestamp <= currentTime;
                });

                // Create an object to keep track of the count of each mood_name
                // If word exists already, it is incremented. If not it is set to 1
                const counts = {};
                timefilteredMoodCards.forEach(card => {
                    const moodWord = card.mood_name;
                    counts[moodWord] = (counts[moodWord] || 0) + 1;
                });
                
                // Sort the object by value in descending order to get the top 9 mood_names
                const sortedCounts = Object.entries(counts)
                    .sort(([, a], [, b]) => b - a) // Creates new array of arrays and sorts by the 2nd bit (count)
                    .slice(0, 9); // Takes first 9 elements in sorted array and returns them as a new array
            
                
                // Get the count of all remaining mood_names
                let remainingCount = 0;
                Object.values(counts).forEach(count => remainingCount += count); // Gives total number of of 'counts' or items we have
                sortedCounts.forEach(([, count]) => remainingCount -= count); // Subtracts the 'counts' in sortedcounts from the total, leaving remaining count

                // Add the count of the remaining mood_names as the count for the 10th value
                if (sortedCounts.length < 9) {
                    sortedCounts.push(...Object.entries(counts));
                } else {
                    sortedCounts.push(['Other', remainingCount]);
                }
                
                const uniqueLabels = new Set(sortedCounts.map(entry => entry[0]));
                // convert the Set back to an array
                const newLabels = [...uniqueLabels];

                // create the data array with the unique labels
                const newData = newLabels.map(label => {
                    const entry = sortedCounts.find(entry => entry[0] === label);
                    return entry[1];
                });

                
                renderChart(newLabels, newData);



            }).catch(function (error) {
            console.error("Something went wrong retrieving mood log data from db.");
            console.error(error);
            })
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