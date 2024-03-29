<?php

    require_once './configurations/config.php';
    require_once 'utilities.php';
    require_once './repositories/product.repository.php';
    require_once (__DIR__.'/validators/product.validator.php');
    require_once (__DIR__.'/services/product.service.php');

    header('Content-Type: application/json');

    $requestMethod = $_SERVER['REQUEST_METHOD'];

    try {
        switch($requestMethod) {
            case 'POST' : {
                $requestBody = file_get_contents("php://input");
                $data = json_decode($requestBody, true);
                validateCreateRequest($data);
                $productCreationRequest = toCreateRequest($data);
                createProduct($productCreationRequest);
                sendSuccess();
                break;
            }
            case 'PUT' : {
                $requestBody = file_get_contents("php://input");
                $data = json_decode($requestBody, true);
                if (isset($_GET['productId'])) {
                    $productID = $_GET['productId'];
                    validateUpdateRequest($productID, $data);
                    $productUpdateRequest = toUpdateRequest($data);
                    $response = updateProduct($productUpdateRequest, $productID);
                    sendSuccessWithMessage(json_encode($response));
                } else {
                    sendBadRequest("ERROR: Invalid \'productId\'");
                }
                break;
            }
            case 'GET' : {
                $response;
                if (isset($_GET['productId'])) {
                    validateReadRequest($_GET['productId']);
                    $productID = $_GET['productId'];
                    $response = getProductByID($productID);
                    if (!$response) {
                        sendBadRequest("ERROR: No Product found by 'productId': $productID");
                    }
                } else {
                    $response = getProducts();
                    if (empty($response)) {
                        sendNotFound("ERROR: No Product found");
                    }
                }
                sendSuccessWithMessage(json_encode($response));
                break;
            }
            case 'DELETE' : {
                if (isset($_GET['productId']) && isAValidID($_GET['productId'])) {
                    deleteProductByID($_GET['productId'], $pdo);
                } else {
                    deleteAllProducts($pdo);
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