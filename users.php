<?php

    require_once './configurations/config.php';
    require_once 'utilities.php';
    require_once './repositories/user.repository.php';
    require_once (__DIR__.'/validators/user.validator.php');
    require_once (__DIR__.'/services/user.service.php');

    header('Content-Type: application/json');
    $requestMethod = $_SERVER['REQUEST_METHOD'];


    switch($requestMethod) {
        case 'POST' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            validateCreateUserRequest($data);
            createUser(toCreateUserRequest($data));
            sendSuccess();
            break;
        }
        case 'PUT' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            $userID = isset($_GET['userId']) ? $_GET['userId'] : '';
            validateUpdateUserRequest($data, $userID);
            updateUser(toUpdateUserRequest($data), $userID);
            sendSuccess();
            break;
        }
        case 'GET' : {
            $userID = getStringOrDefault($_GET,'userId', null);
            if ($userID) {
                sendSuccessWithMessage(json_encode(getUserByID($userID)));
            } else {
                sendSuccessWithMessage(json_encode(getAllUsers()));
            }
            break;
        }
        case 'DELETE' : {
            $userID = getStringOrDefault($_GET,'userId', null);
            if ($userID) {
                deleteUserByID($userID);
            } else {
                deleteAllUsers();
            }
            break;
        }
        default : {
            echo "Method not defined";
            break;
        }
    }
?>