<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["userName"])) {
  Header("Location: index.php");
}

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      <link rel="shortcut icon" href="favicon.ico">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
      <link href="https://getbootstrap.com/docs/3.4/examples/dashboard/dashboard.css" rel="stylesheet">

      <script>
        function logout() {
          $.post("api.php", {"action": "logout"}, function() {
          location.href = "index.php";
        });
      }
      </script>
  </head>
  <body>
    <!-- Nav Bar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">ITSafe Car Rental</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
                <li><a id="logout" href="#" onclick="logout();"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
              </ul>
          <ul class="nav navbar-nav navbar-left">
            <li class="active"><a href="main.php">Book a Car</a></li>
            <li><a href="add_car.php">Add a Car</a></li>
            <li><a href="profile.php">Profile</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Cars List -->
    <div class="container">
      <div class="row">
        <div class="jumbotron">
          <h1 class="page-header">Dashboard</h1>
          <h2 class="sub-header">Cars</h2>
          
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>License Plate</th>
                  <th>Manufacturer</th>
                  <th>Model</th>
                  <th>Year</th>
                  <th>Monthly Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="cars_list">

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Handling -->
    <script>
      function logout() {
        $.post("api.php", {"action": "logout"}, function() {
          location.href = "index.php";
        });
      }

      $(document).ready(function() {
        $.post("api.php", {"action": "getAllCars"}, function(data) {
          if (data["success"] == true) {
              $("#cars_list").html(data["data"]);
          }
        });
      });

      function returnCar(carId) {
        $.post("api.php", {"action": "returnCar", "carId": carId}, function(data) {
          if (data["success"] == true) {
            alert("Car successfully return!");
          } else {
            alert("Unexpected Error. Car unsuccessfully return!");
          }
          location.reload();
        });
      }

      function rentCar(carId) {
        $.post("api.php", {"action": "rentCar", "carId": carId}, function(data) {
          if (data["success"] == true) {
            alert("Car successfully rent!");
          } else {
            alert("Unexpected Error. Car unsuccessfully rent!");
          }
          location.reload();
        });
      }
    </script>
  </body>
</html>
