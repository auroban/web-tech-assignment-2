<?php 

    require_once (__DIR__.'/../utilities.php');
    require_once (__DIR__.'/../constants/enum.php');

    function validateCreateRequest($data) {

        if (!isset($data['name']) || isEmpty($data['name'])) {
            sendBadRequest('ERROR: Invalid \'name\'');
        }

        if (!isset($data['price']) || !isNonNegative($data['price'])) {
            sendBadRequest('ERROR: Invalid \'price\'');
        }

        if (!isset($data['shipping_cost']) || !isNonNegative($data['shipping_cost'])) {
            sendBadRequest('ERROR: Invalid \'shipping_cost\'');
        }

        if (!isset($data['currency']) || !isAValidEnum($data['currency'], currencies)) {
            sendBadRequest('ERROR: Invalid \'currency\'');
        }

        if(isset($data["resources"])) {

            $resources = $data["resources"];

            if (!empty($resources)) {

                foreach($resources as $r) {

                    if (!isset($r['type']) || !isAValidEnum($r['type'], productResourceTypes)) {
                        sendBadRequest('ERROR: Invalid \'resource->type\'');
                    }

                    if (!isset($r['uri']) || isEmpty($r['uri'])) {
                        sendBadRequest('ERROR: Invalid \'resource->uri\'');
                    }
                }
            }
        }
    }


    function validateUpdateRequest($productID, $data) {

        if(!is_numeric($productID)) {
            sendBadRequest('ERROR: Invalid \'productId\'');
        }

        if (isset($data['name']) && isEmpty($data['name'])) {
            sendBadRequest('ERROR: Invalid \'name\'');
        }

        if (isset($data['price']) && !isNonNegative($data['price'])) {
            sendBadRequest('ERROR: Invalid \'price\'');
        }

        if (isset($data['shipping_cost']) && !isNonNegative($data['shipping_cost'])) {
            sendBadRequest('ERROR: Invalid \'shipping_cost\'');
        }

        if (isset($data['currency']) && !isAValidEnum($data['currency'], currencies)) {
            sendBadRequest('ERROR: Invalid \'currency\'');
        }
    }

    function validateReadRequest($productID) {
        if (!isAValidID($productID)) {
            sendBadRequest("ERROR: Invalid \'productId\'");
        }
    }

    function validateDeleteRequest($productID) {
        if (!isAValidID($productID)) {
            sendBadRequest("ERROR: Invalid \'productId\'");
        }
    }
?>