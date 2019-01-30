<?php
    include 'functions.php';
    $itemsPerPage = 50;
    $page = 1;
    $interval = "daily";
    $startDate = (new DateTime("now", new DateTimeZone("Africa/Nairobi")))->sub(new DateInterval("P1M"))->format('Y-m-d');
    $endDate = (new DateTime("now", new DateTimeZone("Africa/Nairobi")))->add(new DateInterval("P1D"))->format('Y-m-d');
    $chartType = "bar";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $interval = cleanFormInput($_POST["interval"]);
        $startDate = cleanFormInput($_POST["start_date"], "DATE");
        $endDate = cleanFormInput($_POST["end_date"], "DATE");
        $chartType = cleanFormInput($_POST["chart_type"]);
    }

    $messages = getMessages($itemsPerPage, $page);
    $messageSummary = getMessageSummary($interval, $startDate, $endDate);
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css" >
    <link rel="stylesheet" href="css/style.css" >

    <title>KNBTS SMS Application!</title>
</head>
<body>

    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">KNBTS SMS</a>
        <input class="form-control form-control-dark w-100 d-none" type="text" placeholder="Search" aria-label="Search">
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap d-none">
                <a class="nav-link" href="#">Sign out</a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <main role="main" class="col-md-11 px-1">
                <div class="pt-4 pb-2 border-bottom">
                    <h1 class="h2 d-none">SMS</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="col">
                            <div class="form-row">
                                <div class="col">
                                    <label for="chart_type">Chart Type:</label>
                                </div>
                                <div class="col">
                                    <select class="form-control form-control-sm" name="chart_type" id="chart_type">
                                        <option value="bar" <?php echo strcmp($chartType, 'bar')==0?"selected":""; ?>>Bar</option>
                                        <option value="line" <?php echo strcmp($chartType, 'line')==0?"selected":""; ?>>Line</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="start_date">Start Date:</label>
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control form-control-sm" name="start_date" value="<?php echo $startDate; ?>">
                                </div>
                                <div class="col">
                                    <label for="end_date">End Date:</label>
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control form-control-sm" name="end_date" value="<?php echo $endDate; ?>">
                                </div>
                                <div class="col-2">
                                    <select class="form-control form-control-sm" name="interval" id="interval">
                                        <option value="daily" <?php echo strcmp($interval, 'daily')==0?"selected":""; ?>>Daily</option>
                                        <option value="monthly" <?php echo strcmp($interval, 'monthly')==0?"selected":""; ?>>Monthly</option>
                                        <option value="yearly" <?php echo strcmp($interval, 'yearly')==0?"selected":""; ?>>Yearly</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <strong>Go</strong>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Interval</th>
                                        <th>Sent</th>
                                        <th>Failed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if(strlen($messageSummary['labels']) > 0){
                                            $labelString = str_replace("'", "", $messageSummary['labels']);
                                            $labels = explode(",", substr($labelString, 1, strlen($labelString) - 2));

                                            $totalString = str_replace("'", "", $messageSummary['totals']);
                                            $totals = explode(",", substr($totalString, 1, strlen($totalString) - 2));

                                            $deliverString = str_replace("'", "", $messageSummary['deliveries']);
                                            $deliveries = explode(",", substr($deliverString, 1, strlen($deliverString) - 2));

                                            for ($i = 0; $i < count($labels); $i++) {
                                    ?>
                                                <tr>
                                                    <td><?php echo $labels[$i]; ?></td>
                                                    <td><?php echo $totals[$i]; ?></td>
                                                    <td><?php echo $deliveries[$i]; ?></td>
                                                </tr>
                                    <?php
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h2>Messages</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Time Sent</th>
                                <th>Number</th>
                                <th>Message</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if($messages){
                                    $status = ['0' => 'SENT', '1' => 'DELIVERED'];
                                    $counter = ($page - 1) * $itemsPerPage;
                                    while ($row = $messages->fetch_assoc()) {
                                        $counter++;
                            ?>
                                        <tr>
                                            <td><?php echo $counter; ?></td>
                                            <td><?php echo $row['sent_at']; ?></td>
                                            <td><?php echo $row['phone']; ?></td>
                                            <td><?php echo $row['message']; ?></td>
                                            <td><?php echo $status[$row['status']]; ?></td>
                                        </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <footer class="bg-dark flex-md-nowrap p-0 shadow">
        <center>
            <span style="margin:20px;display: inline-block;color: #fff;">Association of Public Health Laboratories &copy; 2019 </span>
        </center>
    </footer>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery/jquery-3.3.1.slim.min.js"></script>
    <script src="js/popper.js/popper.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js" ></script>
    <script src="js/feather-icons/feather.min.js" ></script>
    <script src="js/Chart.js/Chart.min.js" ></script>

    <script type="text/javaScript">
        /* globals Chart:false, feather:false */

(function () {
  'use strict'

  feather.replace()

  // Graphs
  var ctx = document.getElementById('myChart')
  // eslint-disable-next-line no-unused-vars
  var myChart = new Chart(ctx, {
    type: '<?php echo $chartType;?>',
    data: {
      labels: <?php echo $messageSummary['labels'];?>,
      datasets: [{
        label: 'Sent',
        data: <?php echo $messageSummary['totals'];?>,
        lineTension: 0,
        backgroundColor: '<?php echo strcmp($chartType, "bar")==0?"#007bff":"transparent";?>',
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff'
      },
      {
        label: 'Failed',
        data: <?php echo $messageSummary['deliveries'];?>,
        lineTension: 0,
        backgroundColor: '<?php echo strcmp($chartType, "bar")==0?"#ff7b7b":"transparent";?>',
        borderColor: '#ff7b7b',
        borderWidth: 4,
        pointBackgroundColor: '#ff7b7b'
      }]
    },
    options: {
      title: {
        display: true,
        text: 'Messages over time'},
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          },
          scaleLabel: {
            display: true,
            labelString: 'Message count'
          }
        }],
        xAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'Interval'
          }
        }]

      },
      legend: {
        display: true,
        position: 'right'
      }
    }
  })
}())
    </script>
</body>
</html>