<?php
    session_start();

    if (!isset($_SESSION['login']) || !$_SESSION['login']) {
        header('LOCATION:login.php');
        die();
    }

    $package_raw = file_get_contents('../package.json');
    $package_json = json_decode($package_raw, true);
    $version = $package_json['version'];

    require_once '../ApiHelper.php';
    $apiHelper = new ApiHelper();

    $visitors = $apiHelper->adminGetVisitors();
    $logo = $apiHelper->adminGetLogoData();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">

    <title>Admin Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="../assets/css/styles.css?v<?php echo $version; ?>" rel="stylesheet">
    <link href="../assets/css/styles-admin.css?v<?php echo $version; ?>" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="../index.php">Logo Generator</a>
        <div class="w-100"></div>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="logout.php">Abmelden</a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin.php">
                                <span data-feather="home"></span>
                                Dashboard <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <span data-feather="file"></span>
                                Abmelden
                            </a>
                        </li>
                    </ul>

<!--                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">-->
<!--                        <span>Saved reports</span>-->
<!--                        <a class="d-flex align-items-center text-muted" href="#">-->
<!--                            <span data-feather="plus-circle"></span>-->
<!--                        </a>-->
<!--                    </h6>-->
<!--                    <ul class="nav flex-column mb-2">-->
<!--                        <li class="nav-item">-->
<!--                            <a class="nav-link" href="#">-->
<!--                                <span data-feather="file-text"></span>-->
<!--                                Current month-->
<!--                            </a>-->
<!--                        </li>-->
<!--                        <li class="nav-item">-->
<!--                            <a class="nav-link" href="#">-->
<!--                                <span data-feather="file-text"></span>-->
<!--                                Last quarter-->
<!--                            </a>-->
<!--                        </li>-->
<!--                        <li class="nav-item">-->
<!--                            <a class="nav-link" href="#">-->
<!--                                <span data-feather="file-text"></span>-->
<!--                                Social engagement-->
<!--                            </a>-->
<!--                        </li>-->
<!--                        <li class="nav-item">-->
<!--                            <a class="nav-link" href="#">-->
<!--                                <span data-feather="file-text"></span>-->
<!--                                Year-end sale-->
<!--                            </a>-->
<!--                        </li>-->
<!--                    </ul>-->
                </div>
            </nav>

            <main role="main" class="col-md-9 col-md-offset-3 ml-sm-auto col-lg-10 col-lg-offset-2 pt-3 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2">Anzahl Sessions</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="fs-1 text-700 lh-1 mb-1"><?php echo $visitors['session']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2">Logo Downloads</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="fs-1 text-700 lh-1 mb-1"><?php echo $visitors['logo']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2">Claim Downloads</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="fs-1 text-700 lh-1 mb-1"><?php echo $visitors['claim']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col"></div>
                    <div class="col">
                        <div class="card mt-4">
                            <canvas class="my-4" id="logo-chart" width="50" height="50"></canvas>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mt-4">
                            <canvas class="my-4" id="claim-chart" width="50" height="50"></canvas>
                        </div>
                    </div>
                </div>

<!--                <canvas class="my-4" id="myChart" width="900" height="380"></canvas>-->
<!---->
<!--                <h2>Section title</h2>-->
<!--                <div class="table-responsive">-->
<!--                    <table class="table table-striped table-sm">-->
<!--                        <thead>-->
<!--                        <tr>-->
<!--                            <th>#</th>-->
<!--                            <th>Header</th>-->
<!--                            <th>Header</th>-->
<!--                            <th>Header</th>-->
<!--                            <th>Header</th>-->
<!--                        </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        <tr>-->
<!--                            <td>1,001</td>-->
<!--                            <td>Lorem</td>-->
<!--                            <td>ipsum</td>-->
<!--                            <td>dolor</td>-->
<!--                            <td>sit</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,006</td>-->
<!--                            <td>nibh</td>-->
<!--                            <td>elementum</td>-->
<!--                            <td>imperdiet</td>-->
<!--                            <td>Duis</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,007</td>-->
<!--                            <td>sagittis</td>-->
<!--                            <td>ipsum</td>-->
<!--                            <td>Praesent</td>-->
<!--                            <td>mauris</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,008</td>-->
<!--                            <td>Fusce</td>-->
<!--                            <td>nec</td>-->
<!--                            <td>tellus</td>-->
<!--                            <td>sed</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,009</td>-->
<!--                            <td>augue</td>-->
<!--                            <td>semper</td>-->
<!--                            <td>porta</td>-->
<!--                            <td>Mauris</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,010</td>-->
<!--                            <td>massa</td>-->
<!--                            <td>Vestibulum</td>-->
<!--                            <td>lacinia</td>-->
<!--                            <td>arcu</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,011</td>-->
<!--                            <td>eget</td>-->
<!--                            <td>nulla</td>-->
<!--                            <td>Class</td>-->
<!--                            <td>aptent</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,012</td>-->
<!--                            <td>taciti</td>-->
<!--                            <td>sociosqu</td>-->
<!--                            <td>ad</td>-->
<!--                            <td>litora</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,013</td>-->
<!--                            <td>torquent</td>-->
<!--                            <td>per</td>-->
<!--                            <td>conubia</td>-->
<!--                            <td>nostra</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,014</td>-->
<!--                            <td>per</td>-->
<!--                            <td>inceptos</td>-->
<!--                            <td>himenaeos</td>-->
<!--                            <td>Curabitur</td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>1,015</td>-->
<!--                            <td>sodales</td>-->
<!--                            <td>ligula</td>-->
<!--                            <td>in</td>-->
<!--                            <td>libero</td>-->
<!--                        </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
            </main>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace()
    </script>

    <!-- Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
        // var ctx = document.getElementById("myChart");
        // var myChart = new Chart(ctx, {
        //     type: 'line',
        //     data: {
        //         labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        //         datasets: [{
        //             data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
        //             lineTension: 0,
        //             backgroundColor: 'transparent',
        //             borderColor: '#007bff',
        //             borderWidth: 2,
        //             pointBackgroundColor: '#007bff'
        //         }]
        //     },
        //     options: {
        //         scales: {
        //             yAxes: [{
        //                 ticks: {
        //                     beginAtZero: false
        //                 }
        //             }]
        //         },
        //         legend: {
        //             display: false,
        //         }
        //     }
        // });

        const ctxLogo = document.getElementById("logo-chart");
        const logoChart = new Chart(ctxLogo, {
            type: 'pie',
            data: {
                labels: ["SVG", "PNG", "JPG"],
                datasets: [{
                    data: [
                        <?php echo $logo['image_type_logo']['count_svg']; ?>,
                        <?php echo $logo['image_type_logo']['count_png']; ?>,
                        <?php echo $logo['image_type_logo']['count_jpg']; ?>
                    ],
                    lineTension: 0,
                    backgroundColor: ['#20639b', '#3caea3', '#ed553b'],
                    borderColor: ['#3073ab', '#4cbeb3', '#fd654b'],
                    borderWidth: 2,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                legend: {
                    display: false,
                }
            }
        });

        const ctxClaim = document.getElementById("claim-chart");
        const claimChart = new Chart(ctxClaim, {
            type: 'pie',
            data: {
                labels: ["SVG", "PNG", "JPG"],
                datasets: [{
                    data: [
                        <?php echo $logo['image_type_claim']['count_svg']; ?>,
                        <?php echo $logo['image_type_claim']['count_png']; ?>,
                        <?php echo $logo['image_type_claim']['count_jpg']; ?>
                    ],
                    lineTension: 0,
                    backgroundColor: ['#20639b', '#3caea3', '#ed553b'],
                    borderColor: ['#3073ab', '#4cbeb3', '#fd654b'],
                    borderWidth: 2,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                legend: {
                    display: false,
                }
            }
        });
    </script>
</body>
</html>
