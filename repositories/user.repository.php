<?php 
    require_once (__DIR__.'/../configurations/config.php');
    require_once (__DIR__.'/../models/dao/order.dao.php');
    require_once (__DIR__.'/../models/dao/user.dao.php');
    require_once (__DIR__.'/../models/dao/tuple.php');
    require_once (__DIR__.'/../utilities.php');


    function saveUserDAO($userDAO) {
        global $pdo;
        $query = "INSERT INTO user(username, email, passwd) VALUES(:username, :email, :password)";
        $tuples = array();
        array_push($tuples, new Tuple('username', $userDAO->username));
        array_push($tuples, new Tuple('email', $userDAO->email));
        array_push($tuples, new Tuple('password', $userDAO->password));
        runStatement($query, $tuples, $pdo);
    }


    function saveShippingAddressDAO($shippingAddressDAO, $userId) {
        global $pdo;
        $query = "INSERT INTO shipping_address(userId, unitNo, street, city, province, country, zipCode) VALUES(:userId, :unitNo, :street, :city, :province, :country, :zipCode)";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $userId));
        array_push($tuples, new Tuple('unitNo', $shippingAddressDAO->unitNo));
        array_push($tuples, new Tuple('street', $shippingAddressDAO->street));
        array_push($tuples, new Tuple('city', $shippingAddressDAO->city));
        array_push($tuples, new Tuple('province', $shippingAddressDAO->province));
        array_push($tuples, new Tuple('country', $shippingAddressDAO->country));
        array_push($tuples, new Tuple('zipCode', $shippingAddressDAO->zipCode));
        runStatement($query, $tuples, $pdo);
    }

    function updateUserDAO($userDAO, $userId) {
        global $pdo;
        $query = "UPDATE user SET username = :username, email = :email, passwd = :password WHERE id = :userId";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $userId));
        array_push($tuples, new Tuple('username', $userDAO->username));
        array_push($tuples, new Tuple('email', $userDAO->email));
        array_push($tuples, new Tuple('password', $userDAO->password));
        runStatement($query, $tuples, $pdo);
    }

    function updateShippingAddressDAO($shippingAddressDAO, $userId) {
        global $pdo;
        $query = "UPDATE shipping_address SET unitNo = :unitNo, street = :street, city = :city, province = :province, country = :country, zipCode = :zipCode WHERE userId = :userId";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $userId));
        array_push($tuples, new Tuple('unitNo', $shippingAddressDAO->unitNo));
        array_push($tuples, new Tuple('street', $shippingAddressDAO->street));
        array_push($tuples, new Tuple('city', $shippingAddressDAO->city));
        array_push($tuples, new Tuple('province', $shippingAddressDAO->province));
        array_push($tuples, new Tuple('country', $shippingAddressDAO->country));
        array_push($tuples, new Tuple('zipCode', $shippingAddressDAO->zipCode));
        runStatement($query, $tuples, $pdo);
    }

    function readUserDAOByID($userId) {
        global $pdo;
        $result = readFromDB("SELECT * FROM user WHERE id = '$userId'", $pdo);
        if (empty($result)) {
            return null;
        }
        $row = $result[0];
        return new UserDAO($userId, $row['username'], $row['email'], $row['passwd']);
    }

    function readUserDAOByUsername($username) {
        global $pdo;
        $result = readFromDB("SELECT * FROM user WHERE username = '$username'", $pdo);
        if (empty($result)) {
            return null;
        }
        $row = $result[0];
        return new UserDAO($row['id'], $row['username'], $row['email'], $row['passwd']);
    }

    function readAllUserDAOs() {
        global $pdo;
        $result = readFromDB("SELECT * FROM user", $pdo);
        if (empty($result)) {
            return array();
        }
        $allUsers = array();
        foreach($result as $row) {
            $userDAO = readUserDAOByID($row['id']);
            if ($userDAO != null) {
                array_push($allUsers, $userDAO);
            }
        }
        return $allUsers;
    }

    function readShippingAddressDAOByUserID($userId) {
        global $pdo;
        $result = readFromDB("SELECT * FROM shipping_address WHERE userId = '$userId'", $pdo);
        if (empty($result)) {
            return null;
        }
        $row = $result[0];
        $id = $row['id'];
        $unitNo = $row['unitNo'];
        $street = $row['street'];
        $city = $row['city'];
        $province = $row['province'];
        $country = $row['country'];
        $zipCode = $row['zipCode'];
        return new ShippingAddressDAO($id, $userId, $unitNo, $street, $city, $province, $country, $zipCode);
    }

    function deleteShippingAddressDAOByUserID($userId) {
        global $pdo;
        $query = "DELETE FROM shipping_address WHERE userId = :userId";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $userId));
        runStatement($query, $tuples, $pdo);
    }

    function deleteUserDAOByUserID($userId) {
        global $pdo;
        $query = "DELETE FROM user WHERE id = :userId";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $userId));
        runStatement($query, $tuples, $pdo);
    }
?>