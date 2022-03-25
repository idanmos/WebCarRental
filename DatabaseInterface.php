<?php

    require_once './CommonInterface.php';

    class DatabaseInterface {
        const debug = true;

        public function __construct() {
            try {
                $this->MySQLdb = new PDO("mysql:host=localhost;dbname=car_rent", "root", "");
                $this->MySQLdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Connection Error: ".$exception;
            }
        }

        public function GetMySQLdb() {
            return $this->MySQLdb;
        }

        /*
        * CheckErrors - if debug mode is set we will output the error in the response, if the debug is off we will be redirected to 404.php
        */
        public function CheckErrors($e,$pass = false) {
            if ($pass == true) return true;

            if (self::debug){
                die($e->getMessage());
            }
            else {
                // return error if there is something strange in the database
                return_error(":)");
            }
        }

        public function login($userName, $password) {
            try {
                $cursor = $this->MySQLdb->prepare("SELECT * FROM users WHERE userName='".$userName."' AND password='".$password."'");
                $cursor->execute();
            } catch(PDOException $e) { //SQL injection
                $this->CheckErrors($e);
            }
            
            if($cursor->rowCount() && $cursor->rowCount() > 0) {
                $cursor->setFetchMode(PDO::FETCH_ASSOC);
                return array("success"=>true,"data"=>$cursor->fetch());
            } else {
                return array("success"=>false,"data"=>"Wrong Username/Password!<br>");
            }
        }

        public function register($userName, $password) {
            try {
                # Check if the username is taken
                $cursor = $this->MySQLdb->prepare("SELECT `userName` FROM `users` WHERE `userName`=:userName");
                $cursor->execute( array(":userName"=>$userName) );
            } catch(PDOException $e) {
                $this->CheckErrors($e);
            }

            /* New User */
            if(!($cursor->rowCount())){
                try {
                    $cursor = $this->MySQLdb->prepare("INSERT INTO `users` (`userName`, `password`) VALUES (:userName, :password)");

                    $cursor->execute(array(
                        ":password"=>$password,
                        ":userName"=>$userName
                    ));
                    return array("success"=>true,"data"=>"You have successfully registered!");
                } catch(PDOException $e) {
                    $this->CheckErrors($e);
                }
            } else { /* Already exists */
                return array("success"=>false,"data"=>"username already exists in the system!");
            }
        }

        public function carAdd($manufacturer, $model, $year, $monthlyPrice) {
            $isAvailable = 1;
            $sql = "INSERT INTO `cars` (`manufacturer`, `model`, `year`, `monthlyPrice`, `isAvailable`) value ('$manufacturer', '$model', $year, $monthlyPrice, $isAvailable)";            
            
            try {
                $cursor = $this->MySQLdb->prepare($sql);
                $cursor->execute();
                return array("success"=>true,"data"=>"New record created successfully");
            } catch(PDOException $e) {
                $this->CheckErrors($e);
            }
            return false;
        }

        public function getAllCars() {
            try {
                $cursor = $this->MySQLdb->prepare("SELECT * FROM `cars`");
                $cursor->execute();
                $retval = "";

                foreach ($cursor->fetchAll() as $obj) {
                    $carId = $obj["carId"];
                    $manufacturer = $obj["manufacturer"];
                    $model = $obj["model"];
                    $year = $obj["year"];
                    $monthlyPrice = $obj["monthlyPrice"];
                    $imageURL = $obj["imageURL"];
                    $rentToUserName = $obj["rentToUserName"];

                    if (isset($imageURL)) {
                        $imageURL = base64_decode($obj["imageURL"]);
                    }

                    $retval.="<tr><td>$carId</td><td>$manufacturer</td><td>$model</td><td>$year</td><td>$monthlyPrice</td>";

                    $isAvailableToRent = $obj["isAvailable"] == 1 ? true : false;
                    $isRentToLoggedInUserName = $_SESSION["userName"] == $rentToUserName ? true : false;

                    if ($isAvailableToRent) {
                        // Car is already available to rent
                        $retval.="<td><button id='car_$carId' type='button' class='btn btn-default' onclick='javascript:rentCar($carId)'>Rent</button></td>";
                    } else {
                        if ($isRentToLoggedInUserName) {
                            // Car is rent to logged in user
                            $retval.="<td><button id='car_$carId' type='button' class='btn btn-default' onclick='javascript:returnCar($carId)'>Return</button></td>";
                        } else {
                            // Car is rent to different user
                            $retval.="<td><button id='car_$carId' type='button' class='btn btn-default' disabled>Already Rent</button></td>";
                        }
                    }

                    $retval.="<tr>";
                }

                return array("success"=>true,"data"=>$retval);
            }
            catch(PDOException $e) {
                $this->CheckErrors($e);
            }
            return false;
        }

        public function getUserId($userName) {
            try {
                $cursor = $this->MySQLdb->prepare("SELECT `userId` FROM `users` WHERE `userName` = '$userName'");
                $cursor->execute();
                $retval = $cursor->fetch();
                return $retval["userId"];
            }
            catch(PDOException $e) {
                $this->CheckErrors($e);
            }
            return false;
        }

        public function getUsers($userId) {
            try {
                $cursor = $this->MySQLdb->prepare("SELECT `userId`, `userName` FROM `users` WHERE NOT `userName` = $userId");
                $cursor->execute();
                $retval = "";
                $retval = "<select id='userNameSelection'>";

                foreach ($cursor->fetchAll() as $obj) {
                    $differentUserId = $obj["userId"];
                    $differentUserName = $obj["userName"];

                    $retval.="<option value='$differentUserName'>$differentUserId - $differentUserName</option>";
                }

                $retval.="</select>";
                return $retval;
            }
            catch(PDOException $e) {
                $this->CheckErrors($e);
            }
            return false;
        }

        public function getProfile($userName) {
            try {
                $cursor = $this->MySQLdb->prepare("SELECT `userName`, `fullName`, `email`, `phoneNumber`, `address` FROM `users` WHERE `userName` = '$userName'");
                $cursor->execute();
                $retval = $cursor->fetch();
                return array("success"=>true,"data"=>$retval);
            }
            catch(PDOException $e) {
                $this->CheckErrors($e);
            }
            return false;
        }

        public function editProfile($userName, $fullName, $email, $address, $phoneNumber) {
            try {
                $cursor = $this->MySQLdb->prepare("UPDATE `users` SET `fullName`='$fullName', `email`='$email', `phoneNumber`='$phoneNumber', `address`='$address' WHERE `userName`='$userName'");
                $data = $cursor->execute();
                return array("success"=>true,"data"=>$data);
            }
            catch(PDOException $e) {
                $this->CheckErrors($e);
            }
            return false;
        }

        public function rentCar($carId) {
            try {
                $cursor = $this->MySQLdb->prepare("UPDATE `cars` SET `isAvailable` = '0', `rentToUserName` = :userName WHERE `cars`.`carId` = :carId");
                $cursor->execute(array(":userName"=>$_SESSION["userName"], ":carId"=>$carId));
                return array("success"=>true, "data"=>"Car rent successfully!");
            } catch (PDOException $e) {
                $this->CheckErrors(($e));
            }
        }

        public function returnCar($carId) {
            try {
                $cursor = $this->MySQLdb->prepare("UPDATE `cars` SET `isAvailable` = '1', `rentToUserName` = '' WHERE `cars`.`carId` = :carId");
                $cursor->execute(array(":carId"=>$carId));
                return array("success"=>true, "data"=>"Car return successfully!");
            } catch (PDOException $e) {
                $this->CheckErrors(($e));
            }
        }

    }
?>