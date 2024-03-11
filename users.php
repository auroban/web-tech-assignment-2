<?php

    header('Content-Type: application/json');

    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    echo "$requestMethod";
    echo "$requestUriPath";

    $host = 'localhost';
    $port = 3306;
    $username = "root";
    $password = "root";
    $dbName = "assignment2";

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Failed while connecting to database: ".$e->getMessage());
    }

    class User {
        public $id;
        public $username;
        public $email;
        public $password;
        public $purchaseHistory;
        public $shippingAddress;
    
        public function __construct($id = null, $username, $email, $password, $purchaseHistory = null, $shippingAddress) {
            $this->id = $id ?? "";
            $this->username = $username;
            $this->email = $email;
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            $this->purchaseHistory = $purchaseHistory ?? "";
            $this->shippingAddress = $shippingAddress;
        }
    
        public function displayUserdetails() {
            echo "User ID: {$this->id}\n";
            echo "Username: {$this->username}\n";
            echo "Email: {$this->email}\n";
            echo "Password: {$this->password}";
            echo "Purchase History: {$this->purchaseHistory}";
            echo "Shipping Address: {$this->shippingAddress}";
        }
    }

    switch($requestMethod) {
        case 'POST' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            validate($data);
            $username = $data['username'];
            $email = $data['email'];
            $password = $data['password'];
            $purchaseHistory = $data['purchase_history'];
            $shippingAddress = $data['shipping_address'];
            $user = new User(null, $username, $email, $password, $purchaseHistory, $shippingAddress);
            createUser($user, $pdo);
            break;
        }
        case 'PUT' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            $username = isset($data['username']) ? $data['username'] : '';
            $email = isset($data['email']) ? $data['email'] : '';;
            $password = isset($data['password']) ? $data['password'] : '';
            $purchaseHistory = isset($data['purchase_history']) ? $data['purchase_history'] : '';
            $shippingAddress = isset($data['shipping_address']) ? $data['shipping_address'] : '';
            $user = new User(null, $username, $email, $password, $purchaseHistory, $shippingAddress);
            updateUser($user, $pdo);
            break;
        }
        case 'GET' : {
            getUser($pdo);
            break;
        }
        case 'DELETE' : {
            deleteUser($pdo);
            break;
        }
        default : {
            echo "Method not defined";
            break;
        }
    }


    function createUser($user, $pdo) {
        try {
            $saveUser = $pdo->prepare('INSERT INTO user(username, email, passwd, purchase_history, shipping_address) VALUES ( :username, :email, :password, :purchaseHistory, :shippingAddress)');
            $saveUser->bindParam(':username', $user->username);
            $saveUser->bindParam(':email', $user->email);
            $saveUser->bindParam(':password', $user->password);
            $saveUser->bindParam(':purchaseHistory', $user->purchaseHistory);
            $saveUser->bindParam(':shippingAddress', $user->shippingAddress);
            $saveUser->execute();

            $retrieveUserQuery = "SELECT * FROM user";
            $retrieveUser = $pdo->query($retrieveUserQuery);

            $result = $retrieveUser->fetchAll(PDO::FETCH_ASSOC);

            $row = $result[0];
            $uID = $row['id'];
            $pUsername = $row['username'];
            $pEmail = $row['email'];
            $pPassword = $row['passwd'];
            $pPurchaseHistory = $row['purchase_history'];
            $pShippingAddress = $row['shipping_address'];
            

            $u = new User($uID, $pUsername, $pEmail, $pPassword, $pPurchaseHistory, $pShippingAddress);
            header('HTTP/1.0 200 OK');
            echo "\n\nCreated User";
            $response = json_encode($u);
            echo "\n\n";
            echo $response;
            
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function updateUser($user, $pdo) {
        try {
            $retrieveUserQuery = "SELECT * FROM user";
            $retrieveUser = $pdo->query($retrieveUserQuery);

            $result = $retrieveUser->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                header('HTTP/1.0 404 Resource not found');
                echo "No Data Found";
                die();
            }

            $row = $result[0];
            $uID = $row['id'];
            $pUsername = $row['username'];
            $pEmail = $row['email'];
            $pPassword = $row['passwd'];
            $pPurchaseHistory = $row['purchase_history'];
            $pShippingAddress = $row['shipping_address'];


            if (!empty($user->username) && !(strlen($user->username) == 0) && !(trim($user->username) == "") && $pUsername != $user->username) {
                $pUsername = $user->username;
            }

            if (!empty($user->email) && !(strlen($user->email) == 0) && !(trim($user->email) == "") && $pEmail != $user->email) {
                $pEmail = $user->email;
            }

            if (!empty($user->password) && !(strlen($user->password) == 0) && !(trim($user->password) == "")) {
                $pPassword = password_hash($user->password, PASSWORD_DEFAULT);
            }

            $pPurchaseHistory = $user->purchaseHistory;

            if (!empty($user->shippingAddress) && !(strlen($user->shippingAddress) == 0) && !(trim($user->shippingAddress) == "")) {
                $pShippingAddress = $user->shippingAddress;
            }
            
            $updateQuery = "UPDATE user SET username = :username, email = :email, passwd = :password, purchase_history = :purchaseHistory, shipping_address = :shippingAddress WHERE id = :userID";
            $statement = $pdo->prepare($updateQuery);
            $statement->bindParam('userID', $uID);
            $statement->bindParam('username', $pUsername);
            $statement->bindParam('email', $pEmail);
            $statement->bindParam('password', $pPassword);
            $statement->bindParam('purchaseHistory', $pPurchaseHistory);
            $statement->bindParam('shippingAddress', $pShippingAddress);
            $statement->execute();

            $retrieveUser2 = $pdo->query($retrieveUserQuery);
            $result2 = $retrieveUser2->fetchAll(PDO::FETCH_ASSOC);
            $row = $result2[0];
            $uID = $row['id'];
            $pUsername = $row['username'];
            $pEmail = $row['email'];
            $pPassword = $row['passwd'];
            $pPurchaseHistory = $row['purchase_history'];
            $pShippingAddress = $row['shipping_address'];

            $p = new User($uID, $pUsername, $pEmail, $pPassword, $pPurchaseHistory, $pShippingAddress);
            echo "\n\nUpdated User\n\n";
            $response = json_encode($p);
            header('HTTP/1.0 202 ACCEPTED');
            echo $response;
            
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function getUser($pdo) {
        $retrieveUserQuery = "SELECT * FROM user";
        $retrieveUser = $pdo->query($retrieveUserQuery);

        $result = $retrieveUser->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            header('HTTP/1.0 404 Resource not found');
            echo "No Data Found";
            die();
        }

        $row = $result[0];
        $uID = $row['id'];
        $pUsername = $row['username'];
        $pEmail = $row['email'];
        $pPassword = $row['passwd'];
        $pPurchaseHistory = $row['purchase_history'];
        $pShippingAddress = $row['shipping_address'];
        $p = new User($uID, $pUsername, $pEmail, $pPassword, $pPurchaseHistory, $pShippingAddress);

        echo "\n\nRetrieved User\n\n";
        $response = json_encode($p);
        header('HTTP/1.0 200 OK');
        echo $response;
    } 

    function deleteUser($pdo) {
       $pdo->exec("TRUNCATE TABLE user");
       header('HTTP/1.0 200 OK');
       echo "\n\nDeleted";
    }

    function validate($data) {

        if (!isset($data['username']) || empty($data['username']) || strlen($data['username']) == 0 || trim($data['username']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Username cannot be empty";
            die();
        }

        if (!isset($data['email']) || empty($data['email']) || strlen($data['email']) == 0 || trim($data['email']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Email cannot be empty";
            die();
        }

        if (!isset($data['password']) || empty($data['password']) || strlen($data['password']) == 0 || trim($data['password']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Password cannot be empty";
            die();
        }

        if (!isset($data['shipping_address']) || empty($data['shipping_address']) || strlen($data['shipping_address']) == 0 || trim($data['shipping_address']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Shipping Address cannot be empty";
            die();
        }
    } 

?>