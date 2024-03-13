<?php
    require_once 'utilities.php';
    require_once (__DIR__.'./repositories/order.repository.php');
    require_once (__DIR__.'/validators/order.validator.php');
    require_once (__DIR__.'/services/order.service.php');

    header('Content-Type: application/json');

    $requestMethod = $_SERVER['REQUEST_METHOD'];

    try {
        switch($requestMethod) {
            case 'POST' : {
                $requestBody = file_get_contents("php://input");
                $data = json_decode($requestBody, true);
                validateCreateRequest($data);
                $orderCreationRequest = toCreateRequest($data);
                createOrder($orderCreationRequest);
                sendSuccess();
                break;
            }
            case 'PUT' : {
                $requestBody = file_get_contents("php://input");
                $data = json_decode($requestBody, true);
                if (isset($_GET['orderId'])) {
                    $orderId = $_GET['orderId'];
                    validateUpdateRequest($orderId, $data);
                    $orderUpdateRequest = toUpdateRequest($data);
                    $response = updateOrder($orderUpdateRequest, $orderId);
                    sendSuccessWithMessage(json_encode($response));
                } else {
                    sendBadRequest("ERROR: Invalid \'productId\'");
                }
                break;
            }
            case 'GET' : {
                $response;
                if (isset($_GET['orderId'])) {
                    validateReadRequest($_GET['orderId']);
                    $orderId = $_GET['orderId'];
                    $response = getOrderByID($orderId);
                    if (!$response) {
                        sendBadRequest("ERROR: No Product found by 'productId': $productID");
                    }
                } else {
                    $response = getOrders();
                    if (empty($response)) {
                        sendNotFound("ERROR: No Product found");
                    }
                }
                sendSuccessWithMessage(json_encode($response));
                break;
            }
            case 'DELETE' : {
                if (isset($_GET['orderId']) && isAValidID($_GET['orderId'])) {
                    deleteOrderByID($_GET['orderId']);
                } else {
                    deleteOrders();
                }
                sendSuccessWithMessage('Deleted');
                break;
            }
            default : {
                echo "Method not defined";
                break;
            }
        }
    } catch (Exception $e) {
        sendInternalServerError("ERROR: ".$e->getMessage());
    } 
?>