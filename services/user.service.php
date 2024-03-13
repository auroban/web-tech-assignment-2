<?php 

    require_once(__DIR__.'/../repositories/user.repository.php');
    require_once(__DIR__.'/../models/request/user.request.php');
    require_once(__DIR__.'/../models/response/user.response.php');
    require_once(__DIR__.'/../models/response/order.response.php');
    require_once(__DIR__.'/../services/order.service.php');
    require_once(__DIR__.'/../constants/enum.php');


    function toCreateUserRequest($data) {
        $username = $data['username'];
        $email = $data['email'];
        $password = hashPW($data['password']);
        $shippingAddress = $data['shippingAddress'];

        $unitNo = getStringOrDefault($shippingAddress, 'unitNo', '');
        $street = $shippingAddress['street'];
        $city = $shippingAddress['city'];
        $province = $shippingAddress['province'];
        $country = $shippingAddress['country'];
        $zipCode = $shippingAddress['zipCode'];

        $shAdd = new ShippingAddressCreateRequest($unitNo, $street, $city, $province, $country, $zipCode);
        return new UserCreateRequest($username, $email, $password, $shAdd);
    }

    function toUpdateUserRequest($data) {
        
        $username = getStringOrDefault($data, 'username', '');
        $email = getStringOrDefault($data, 'email', '');
        $password = hashPW(getStringOrDefault($data, 'password', ''));

        $userUpdateRequest = new UserUpdateRequest($username, $email, $password, null);
        if (isset($data['shippingAddress'])) {
            $shippingAddress = $data['shippingAddress'];
            $unitNo = getStringOrDefault($shippingAddress, 'unitNo', '');
            $street = getStringOrDefault($shippingAddress, 'street', '');
            $city = getStringOrDefault($shippingAddress, 'city', '');
            $province = getStringOrDefault($shippingAddress, 'province', '');
            $country = getStringOrDefault($shippingAddress, 'country', '');
            $zipCode = getStringOrDefault($shippingAddress, 'zipCode', '');

            $userUpdateRequest->shippingAddress = new ShippingAddressUpdateRequest($unitNo, $street, $city, $province, $country, $zipCode);
        }
        return $userUpdateRequest;
    }


    function toShippingAddressDAOFromCreateRequest($shippingAddressCreateRequest, $userId) {
        $id = null;
        $unitNo = $shippingAddressCreateRequest->unitNo;
        $street = $shippingAddressCreateRequest->street;
        $city = $shippingAddressCreateRequest->city;
        $province = $shippingAddressCreateRequest->province;
        $country = $shippingAddressCreateRequest->country;
        $zipCode = $shippingAddressCreateRequest->zipCode;
        return new ShippingAddressDAO($id, $userId, $unitNo, $street, $city, $province, $country, $zipCode);
    }

    function toShippingAddressDAOFromUpdateRequest($shippingAddressUpdateRequest, $userId) {
        $id = null;
        $unitNo = $shippingAddressUpdateRequest->unitNo;
        $street = $shippingAddressUpdateRequest->street;
        $city = $shippingAddressUpdateRequest->city;
        $province = $shippingAddressUpdateRequest->province;
        $country = $shippingAddressUpdateRequest->country;
        $zipCode = $shippingAddressUpdateRequest->zipCode;
        return new ShippingAddressDAO($id, $userId, $unitNo, $street, $city, $province, $country, $zipCode);
    }

    function toUserDAOFromCreateRequest($userCreateRequest) {
        $id = null;
        $username = $userCreateRequest->username;
        $email = $userCreateRequest->email;
        $password = $userCreateRequest->password;
        return new UserDAO($id, $username, $email, $password);
    }

    function toUserDAOFromUpdateRequest($userUpdateRequest, $userId) {
        $username = $userUpdateRequest->username;
        $email = $userUpdateRequest->email;
        $password = $userUpdateRequest->password;
        return new UserDAO($userId, $username, $email, $password);
    }

    function toShippingAddressResponse($shippingAddressDAO) {
        $id = $shippingAddressDAO->id;
        $unitNo = $shippingAddressDAO->unitNo;
        $street = $shippingAddressDAO->street;
        $city = $shippingAddressDAO->city;
        $province = $shippingAddressDAO->province;
        $zipCode = $shippingAddressDAO->zipCode;
        $country = $shippingAddressDAO->country;
        return new ShippingAddressResponse($id, $unitNo, $street, $city, $province, $country, $zipCode);
    }

    function toUserResponse($userDAO, $shippingAddressResponse, $orders) {
        return new UserCreatedResponse(
            $userDAO->id,
            $userDAO->username,
            $userDAO->email,
            $userDAO->password,
            $shippingAddressResponse,
            $orders);
    }

    function createUser($userCreateRequest) {
        $userDAO = toUserDAOFromCreateRequest($userCreateRequest);
        saveUserDAO($userDAO);
        $userDAO = readUserDAOByUsername($userCreateRequest->username);

        $shippingAddressDAO = toShippingAddressDAOFromCreateRequest($userCreateRequest->shippingAddress, $userDAO->id);
        saveShippingAddressDAO($shippingAddressDAO, $userDAO->id);
        $shippingAddressDAO = readShippingAddressDAOByUserID($userDAO->id);

        $shippingAddressResponse = toShippingAddressResponse($shippingAddressDAO);
        $userCreatedResponse = toUserResponse($userDAO, $shippingAddressResponse, array());

        return $userCreatedResponse;
    }

    function updateUser($updateUserRequest, $userId) {

        $oldUserDAO = readUserDAOByID($userId);
        if ($oldUserDAO == null) {
            return null;
        }
        $oUsername = $oldUserDAO->username;
        $oEmail = $oldUserDAO->email;
        $oPassword = $oldUserDAO->password;

        $nUsername = getStringOrDefault($updateUserRequest, 'username', $oUsername);
        $nEmail = getStringOrDefault($updateUserRequest, 'email', $oEmail);
        $nPassword = getStringOrDefault($updateUserRequest, 'password', $oPassword);

        $newUserDAO = toUserDAOFromUpdateRequest(new UserUpdateRequest($nUsername, $nEmail, $nPassword, null), $userId);
        updateUserDAO($newUserDAO, $userId);
        $newUserDAO = readUserDAOByID($userId);

        $oldShippingAddressDAO = readShippingAddressDAOByUserID($userId);
        $oUnitNo = $oldShippingAddressDAO->unitNo;
        $oStreet = $oldShippingAddressDAO->street;
        $oCity = $oldShippingAddressDAO->city;
        $oProvince = $oldShippingAddressDAO->province;
        $oZipCode = $oldShippingAddressDAO->zipCode;
        $oCountry = $oldShippingAddressDAO->country;

        $nUnitNo = getStringOrDefault($updateUserRequest->shippingAddress, 'unitNo', $oUnitNo);
        $nStreet = getStringOrDefault($updateUserRequest->shippingAddress, 'street', $oStreet);
        $nCity = getStringOrDefault($updateUserRequest->shippingAddress, 'city', $oCity);
        $nProvince = getStringOrDefault($updateUserRequest->shippingAddress, 'province', $oProvince);
        $nZipCode = getStringOrDefault($updateUserRequest->shippingAddress, 'zipCode', $oZipCode);
        $nCountry = getStringOrDefault($updateUserRequest->shippingAddress, 'country', $oCountry);

        $newShippingAddressDAO = toShippingAddressDAOFromUpdateRequest(
            new ShippingAddressUpdateRequest(
                $nUnitNo,
                $nStreet,
                $nCity,
                $nProvince,
                $nCountry,
                $nZipCode), 
            $userId);
        updateShippingAddressDAO($newShippingAddressDAO, $userId);
        $newShippingAddressDAO = readShippingAddressDAOByUserID($userId);
        $shippingAddressResponse = toShippingAddressResponse($newShippingAddressDAO);
        $allOrders = getOrdersByUserId($userId);
        return toUserResponse($newUserDAO, $shippingAddressResponse, $allOrders);
    }

   
    function getUserById($userId) {
        $userDAO = readUserDAOByID($userId);
        if ($userDAO == null) {
            return null;
        }

        $shippingAddressDAO = readShippingAddressDAOByUserID($userId);
        $shippingAddressResponse = toShippingAddressResponse($shippingAddressDAO);
        $allOrders = getOrdersByUserId($userId);

        return toUserResponse($userDAO, $shippingAddressResponse, $allOrders);
    }

    function getAllUsers() {
        $userDAOs = readAllUserDAOs();
        if (empty($userDAOs)) {
            return array();
        }
        $allUsers = array();
        foreach($userDAOs as $userDAO) {
            $userResponse = getUserById($userDAO->id);
            array_push($allUsers, $userResponse);
        }
        return $allUsers;
    }

    function deleteUserByID($userId) {
        $userDAO = readUserDAOByID($userId);
        if ($userDAO == null) {
            return null;
        }
        $orders = getOrdersByUserId($userId);
        foreach($orders as $o) {
            deleteOrderByID($o->id);
        }
        deleteShippingAddressDAOByUserID($userId);
        deleteUserDAOByUserID($userId);
    }

    function deleteAllUsers() {
        $userDAOs = readAllUserDAOs();
        if (!empty($userDAOs)) {
            foreach($userDAOs as $userDAO) {
                deleteUserByID($userDAO->id);
            }
        }
    }
?>