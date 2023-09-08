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
                    <h3>Your Mood Triggers</h3>
                    
                    <div class="text-end">
                        <select onchange="handleOnChangeDates(this.value)">
                            <option value="last7Days" selected>Last 7 days</option>
                            <option value="last30Days">Last 30 days</option>
                            <option value="lastYear">Last 12 months</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
                <p class="p-3 m-0 text-secondary"><em>Knowing which activities improve your mood and which don't can help you make decisions that support a happier and healthier lifestyle. </em></p>
                <p class="p-3 m-0 text-secondary"><em>Terrible/Bad moods have been categorised as negative, while Good/Excellent moods have been categorised as positive.</em></p>
                <hr>
                <canvas id="moodTriggersChart" class="p-3"></canvas>
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

            

            function renderChart(triggerNames, badValues, goodValues){
                const moodTriggerChart = document.getElementById('moodTriggersChart');

                // Negative datapoint converter
                const badPositiveVals = badValues;
                const negativeBadValues = [];
                badPositiveVals.forEach(element => negativeBadValues.push(element * -1));

                // block tooltip
                const tooltip = {
                    yAlign: 'bottom',
                    titleAlign: 'center',
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label} ${Math.abs(context.raw)}`;
                        }
                    }
                };

                chart = new Chart(moodTriggerChart, {
                    type: 'bar',
                    data: {
                    labels: triggerNames,
                    datasets: [{
                        label: 'Negative mood',
                        backgroundColor: 'rgb(208,79,63)',
                        data: negativeBadValues,
                        borderWidth: 1
                    },{
                        label: 'Positive mood',
                        backgroundColor: 'rgb(172,197,99)',
                        data: goodValues,
                        borderWidth: 1
                    }]
                    },
                    options: {
                        indexAxis: 'y',
                        scales: {
                            x: {
                                min: -Math.max(...goodValues, ...negativeBadValues),
                                max: Math.max(...goodValues, ...negativeBadValues),
                                stacked: true,
                                beginAtZero: false,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value, index, values) {
                                        return Math.abs(value);
                                    }
                                    
                                }
                            },
                            y: {
                                beginAtZero: true,
                                stacked: true
                            }
                        },
                        plugins: {
                            legend: {
                                align: 'centre',
                                justify: false,
                                labels: {
                                    boxWidth: 40,
                                },
                            },
                            tooltip
                        }
                    }
                });
            }


            function updateChart(startTime, currentTime, userId) {
                chart.destroy();
                // Code to fetch data from API
                fetch('http://localhost/PROJECT-APIGithub/api.php?triggers-chart&user_id=' + userId)
                .then(function (response) {
                    return response.json();
                }).then(function (obj) {
                    apiData = obj;

                    // Filter mood log data based on start timestamp
                    const timeFilteredData = apiData.filter(card => {
                    const cardTimestamp = new Date(card.mood_log_timestamp).getTime();
                    return cardTimestamp >= startTime && cardTimestamp <= currentTime;
                    });

                    const counts = {};

                    for (const item of timeFilteredData) {
                        const trigger_name = item.trigger_name;
                        const mood_rating_id = parseInt(item.mood_rating_id);
                        
                        if (!counts[trigger_name]) {
                            counts[trigger_name] = [0, 0]; // initialize count to 0 for each trigger name
                        }
                        
                        if (mood_rating_id >= 1 && mood_rating_id <= 2) {
                            counts[trigger_name][0]++;
                        } else if (mood_rating_id >= 4 && mood_rating_id <= 5) {
                            counts[trigger_name][1]++;
                        }
                    }

                    const triggerNames = Object.keys(counts);
                    const badValues = triggerNames.map(name => counts[name][0]);
                    const goodValues = triggerNames.map(name => counts[name][1]);

                    // print results in desired format
                    console.log('trigger_name\tcount for 1 or 2\tcount for 4 or 5');
                    for (const trigger_name in counts) {
                    console.log(`${trigger_name}\t${counts[trigger_name][0]}\t${counts[trigger_name][1]}`);
                    }


                    renderChart(triggerNames, badValues, goodValues);

                }).catch(function (error) {
                console.error("Something went wrong retrieving mood log data from db.");
                console.error(error);
                });

                
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