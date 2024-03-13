<?php 

    require_once (__DIR__.'/../utilities.php');
    require_once (__DIR__.'/../constants/enum.php');

    function validateCreateRequest($data) {

        if (!isset($data['userId']) || !isAValidID($data['userId'])) {
            sendBadRequest('ERROR: Invalid \'userId\'');
        }

        if (!isset($data['status']) || !isAValidEnum($data['status'], orderStatuses)) {
            sendBadRequest('ERROR: Invalid \'status\'');
        }

        if (!isset($data['orderItems']) || empty($data['orderItems'])) {
            sendBadRequest('ERROR: Invalid \'orderItems\'');
        }

        $orderItems = $data['orderItems'];
        foreach ($orderItems as $oi) {
            if (!isset($oi['productId']) || isEmpty($oi['productId'])) {
                sendBadRequest('ERROR: Invalid \'orderItems->productId\'');
            }
            if (!isset($oi['quantity']) || !isNonNegative($oi['quantity'])) {
                sendBadRequest('ERROR: Invalid \'orderItems->quantity\'');
            }
            if (!isset($oi['totalCost']) || !isNonNegative($oi['totalCost'])) {
                sendBadRequest('ERROR: Invalid \'orderItems->totalCost\'');
            }
        }
    }


    function validateUpdateRequest($orderID, $data) {

        if (!isAValidID($orderID)) {
            sendBadRequest('ERROR: Invalid \'orderId\'');
        }

        if (isset($data['userId']) && !isAValidID($data['userId'])) {
            sendBadRequest('ERROR: Invalid \'userId\'');
        }

        if (isset($data['status']) && !isAValidEnum($data['status'], orderStatuses)) {
            sendBadRequest('ERROR: Invalid \'status\'');
        }
    }

    function validateReadRequest($orderID) {
        if (!isAValidID($orderID)) {
            sendBadRequest("ERROR: Invalid \'orderId\'");
        }
    }

    function validateDeleteRequest($orderID) {
        if (!isAValidID($orderID)) {
            sendBadRequest("ERROR: Invalid \'orderId\'");
        }
    }
?>