<?php 

    require_once (__DIR__.'/../utilities.php');
    require_once (__DIR__.'/../constants/enum.php');

    function validateCreateUserRequest($data) {

        if (!isset($data['username']) || isEmpty($data['username'])) {
            sendBadRequest('ERROR: Invalid \'username\'');
        }

        if (!isset($data['email']) || isEmpty($data['email'])) {
            sendBadRequest('ERROR: Invalid \'email\'');
        }

        if (!isset($data['password']) || isEmpty($data['password'])) {
            sendBadRequest('ERROR: Invalid \'password\'');
        }

        if (!isset($data['shippingAddress']) || empty($data['shippingAddress'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress\'');
        }

        $shippingAddress = $data['shippingAddress'];
        if (!isset($shippingAddress['street']) || isEmpty($shippingAddress['street'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress->street\'');
        }
        if (!isset($shippingAddress['city']) || isEmpty($shippingAddress['city'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress->city\'');
        }
        if (!isset($shippingAddress['province']) || isEmpty($shippingAddress['province'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress->province\'');
        }
        if (!isset($shippingAddress['country']) || isEmpty($shippingAddress['country'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress->street\'');
        }
        if (!isset($shippingAddress['zipCode']) || isEmpty($shippingAddress['zipCode'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress->zipCode\'');
        }
    }


    function validateUpdateUserRequest($data, $userId) {

        if(!isAValidID($userId)) {
            sendBadRequest('ERROR: Invalid \'userId\'');
        }

        if (isset($data['username']) && isEmpty($data['username'])) {
            sendBadRequest('ERROR: Invalid \'username\'');
        }

        if (isset($data['email']) && isEmpty($data['email'])) {
            sendBadRequest('ERROR: Invalid \'email\'');
        }

        if (isset($data['password']) && isEmpty($data['password'])) {
            sendBadRequest('ERROR: Invalid \'password\'');
        }

        if (isset($data['shippingAddress']) && empty($data['shippingAddress'])) {
            sendBadRequest('ERROR: Invalid \'shippingAddress\'');
        } 

        if (isset($data['shippingAddress'])) {
            $shippingAddress = $data['shippingAddress'];
            if (isset($shippingAddress['street']) && isEmpty($shippingAddress['street'])) {
                sendBadRequest('ERROR: Invalid \'shippingAddress->street\'');
            }
            if (isset($shippingAddress['city']) && isEmpty($shippingAddress['city'])) {
                sendBadRequest('ERROR: Invalid \'shippingAddress->city\'');
            }
            if (isset($shippingAddress['province']) && isEmpty($shippingAddress['province'])) {
                sendBadRequest('ERROR: Invalid \'shippingAddress->province\'');
            }
            if (isset($shippingAddress['country']) && isEmpty($shippingAddress['country'])) {
                sendBadRequest('ERROR: Invalid \'shippingAddress->street\'');
            }
            if (isset($shippingAddress['zipCode']) && isEmpty($shippingAddress['zipCode'])) {
                sendBadRequest('ERROR: Invalid \'shippingAddress->zipCode\'');
            }
        }
    }

    function validateReadUserRequest($userId) {
        if (!isAValidID($userId)) {
            sendBadRequest("ERROR: Invalid \'userId\'");
        }
    }

    function validateDeleteUserRequest($userId) {
        if (!isAValidID($userId)) {
            sendBadRequest("ERROR: Invalid \'userId\'");
        }
    }
?>