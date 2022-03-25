<?php

    require_once "./CommonInterface.php";
    require_once "./DatabaseInterface.php";

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "login":
                login(
                    $_POST["userName"],
                    $_POST["password"]
                );
                break;
            case "logout":
                    logout($_POST["userName"]);
                    break;
            case "register":
                register(
                    $_POST["userName"], 
                    $_POST["password"]
                );
                break;
            case "getAllCars":
                getAllCars();
                break;
            case "carAdd":
                carAdd(
                    $_POST["manufacturer"],
                    $_POST["model"],
                    $_POST["year"],
                    $_POST["monthlyPrice"]
                );
                break;
            case "updateProfile":
                updateProfile(
                    $_POST["userName"],
                    $_POST["fullName"],
                    $_POST["email"],
                    $_POST["address"],
                    $_POST["phoneNumber"]
                );
                break;
            case "getUserId":
                getUserId($_POST["userName"]);
                break;
            case "getProfile":
                getProfile($_POST["userName"]);
                break;
            case "rentCar":
                rentCar($_POST["carId"]);
                break;
            case "returnCar":
                returnCar($_POST["carId"]);
                break;
            default;
        }
    }

    function login($userName, $password) {
        if (isset($userName) && isset($password)) {
            $db = new DatabaseInterface();
            $retval = $db->login($userName, $password);

            if ($retval["success"] == true) {
                $_SESSION["userName"] = $userName;
                return_success($retval["data"]);
            } else {
                return_error($retval["data"]);
            }
        } else {
            die("Missing user name or password!");
        }
    }

    function logout($userName) {
        session_destroy();
        die(Header("Location: index.php"));
    }

    function register($userName, $password) {
        if (isset($userName) && isset($password)) {
            $db = new DatabaseInterface();
            $retval = $db->register($userName, $password);

            if ($retval["success"] == true) {
                $_SESSION["userName"] = $userName;
                return_success($retval);
            } else {
                return_error($retval);
            }
        } else {
            die("Missing user name or password!");
        }
    }

    function getAllCars() {
        $db = new DatabaseInterface();
        $retval = $db->getAllCars();
        if ($retval["success"] == true) {
            return_success($retval["data"]);
        } else {
            return_error($retval["data"]);
        }
    }

    function getUserId($userName) {
        if (isset($userName)) {
            $db = new DatabaseInterface();
            $retval = $db->getUserId($userName);
            if (isset($retval) && $retval != false) {
                $_SESSION["userId"] = $retval;
                return_success($retval);
            } else {
                return_error("Malformed request");
            }
        }
        return_error("Missing user name!");
    }

    function getProfile($userName) {
        if (isset($userName)) {
            $db = new DatabaseInterface();
            $data = $db->getProfile($userName);
            if ($data["success"] == true) {
                return_success($data["data"]);
            } else {
                return_error($data["data"]);
            }
        } else {
            return_error("Missing user name!");
        }
        return_error("Unexpected error!");
    }

    function getUsers($userId) {
        if (isset($userId)) {
            $db = new DatabaseInterface();
            $retval = $db->getUsers($userId);
            if ($retval["success"] == true) {
                return_success($retval["data"]);
            } else {
                return_error("Malformed request");
            }
        }
        return_error("Missing user id!");
    }

    function carAdd($manufacturer, $model, $year, $monthlyPrice) {
        if (isset($manufacturer) && isset($model) && isset($year) && isset($monthlyPrice)) {
            $db = new DatabaseInterface();
            $retval = $db->carAdd($manufacturer, $model, $year, $monthlyPrice);

            if ($retval["success"] == true) {
                return_success($retval["data"]);
            } else {
                return_error($retval["data"]);
            }
        }
        return false;
    }

    function updateProfile($userName, $fullName, $email, $address, $phoneNumber) {
        if (isset($userName)) {
            $db = new DatabaseInterface();
            $data = $db->editProfile($userName, $fullName, $email, $address, $phoneNumber);
            if ($data["success"] == true) {
                return_success($data["data"]);
            } else {
                return_error($data["data"]);
            }
        } else {
            return_error("Missing user name!");
        }
    }

    function rentCar($carId) {
        if (isset($carId)) {
            $db = new DatabaseInterface();
            $data = $db->rentCar($carId);
            if ($data["success"] == true) {
                return_success($data["data"]);
            } else {
                return_error($data["data"]);
            }
        }
        return false;
    }

    function returnCar($carId) {
        if (isset($carId)) {
            $db = new DatabaseInterface();
            $data = $db->returnCar($carId);
            if ($data["success"] == true) {
                return_success($data["data"]);
            } else {
                return_error($data["data"]);
            }
        }
        return false;
    }

?>