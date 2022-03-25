<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["userName"])) {
    session_destroy();
    die(Header("Location: index.php"));
}

$userName = (isset($_SESSION["userName"])) ? $_SESSION["userName"] : '';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
    <title>ITSafe Car Rental</title>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body class="text-center" style="background-color: #ecedf0; width: 100%;">
        <div>
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
                            <li><a href="main.php">Book a Car</a></li>
                            <li class="active"><a href="add_car.php">Add a Car</a></li>
                            <li><a href="profile.php">Profile</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <br><br><br>

            <!-- Add Car Form -->
            <div class="col-md-4 col-md-offset-4" style="background-color: white; border-radius: 10px;">
                <h3 style="text-align: center;">Add a Car</h3>                    
                <br />

                <!-- Error Message -->
                <div id ="alertDiv" class="alert alert-danger text-left" role="alert" hidden>
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    <span id="errorMessage"></span>
                </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                        <input id="manufacturer" name="manufacturer" type="text" class="form-control" placeholder="Manufacturer">
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-road"></i></span>
                        <input id="model" name="model" type="text" class="form-control" placeholder="Model">
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        <input id="year" name="year" type="text" class="form-control" placeholder="Year">
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                        <input id="monthlyPrice" name="monthlyPrice" type="text" class="form-control" placeholder="Monthly Price">
                        <div class="input-group-addon">.00</div>
                    </div>
                    <br/>
                    <button id="saveButton" type="button" class="btn btn-primary">Add</button><br/><br/>
                </div>
            </div>

        <!-- Event Handling -->
        <script>
            function logout() {
                $.post("api.php", {"action": "logout"}, function() {
                    location.href = "index.php";
                });
            }

            function showErrorMessage(errorMessage) {
                $("#errorMessage").text(errorMessage);
                $("#alertDiv").show();
            }

            function validate() {
                $("#alertDiv").hide();

                if (!$("#manufacturer").val()) {
                    showErrorMessage("Manufacturer cannot be empty!");
                    return false;
                }
                if (!$("#model").val()) {
                    showErrorMessage("Model cannot be empty!");
                    return false;
                }
                if (!$("#year").val()) {
                    showErrorMessage("Year cannot be empty!");
                    return false;
                }
                if (!$("#monthlyPrice").val()) {
                    showErrorMessage("Monthly price cannot be empty!");
                    return false;
                }

                return true;
            }

            $("#saveButton").click(function() {
                if (validate()) {
                    var $postData = {
                        "action": "carAdd", 
                        "manufacturer": $("#manufacturer").val(),
                        "model": $("#model").val(),
                        "year": $("#year").val(),
                        "monthlyPrice": $("#monthlyPrice").val()
                    };
                    
                    $.post("api.php", $postData, function(data) {
                        if (data["success"] == true) {
                            alert("Car added successfully!");
                            location.href = "main.php";
                        } else {
                            alert("Error adding car!");
                        }
                    });
                }
            });
        </script>
    </body>
</html>