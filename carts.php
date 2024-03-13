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

    class CartRequest {
        public $username;
        public $productName;
        public $quantity;

        public function __construct($username, $productName, $quantity) {
            $this->username = $username;
            $this->productName = $productName;
            $this->quantity = $quantity;
        }

        public function displayCartRequest() {
            echo "Username: {$this->username}\n";
            echo "Product Name: {$this->productName}\n";
            echo "Quantity: {$this->quantity}\n";
        }
        
    }

    class CartItem {
        public $productName;
        public $quantity;

        public function __construct($productName, $quantity) {
            $this->productName = $productName;
            $this->quantity = $quantity;
        }

        public function displayCartItemDetails() {
            echo "Product Name: {$this->productName}\n";
            echo "Quantity: {$this->quantity}\n";
        }
    }
    
    class Cart {
        public $id;
        public $username;
        public $cartItems;

        public function __construct($id = null, $username, $cartItems = null) {
            $this->id = $id ?? "";
            $this->username = $username;
            $this->cartItems = $cartItems;
        }

        public function displayCartDetails() {
            echo "Cart ID: {$this->id}\n";
            echo "Cart Username: {$this->username}\n";
            echo "Cart Items: {$this->cartItems}\n";
        }
    }


    switch($requestMethod) {
        case 'POST' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            validate($data);
            $username = $data['username'];
            $productName = $data['product_name'];
            $quantity = $data['quantity'];
            $cartRequest = new CartRequest($username, $productName, $quantity);
            createOrUpdateCartItem($cartRequest, $pdo);
            break;
        }
        case 'PUT' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            $username = isset($data['username']) ? $data['username'] : '';
            $productName = isset($data['product_name']) ? $data['product_name'] : '';
            $quantity = isset($data['quantity']) ? $data['quantity'] : '';
            $cartRequest = new CartRequest($username, $productName, $quantity);
            createOrUpdateCartItem($cartRequest, $pdo);
            break;
        }
        case 'GET' : {
            if (isset($_GET['cartId'])) {
                getCart($_GET['cartId'], $pdo);
            } else {
                getAllCarts($pdo);
            }
            break;
        }
        case 'DELETE' : {
            if (isset($_GET['cartId'])) {
                deleteCart($_GET['cartId'], $pdo);
            } else {
                echo "MASS CART DELETION NOT PERMITTED";
            }
            break;
        }
        default : {
            echo "Method not defined";
            break;
        }
    }

    function validate($data) {

        if (!isset($data['username']) || empty($data['username']) || strlen($data['username']) == 0 || trim($data['username']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Username cannot be empty";
            die();
        }

        if (!isset($data['product_name']) || empty($data['product_name']) || strlen($data['product_name']) == 0 || trim($data['product_name']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Product Name cannot be empty";
            die();
        }

        if (!isset($data['quantity']) || $data['quantity'] < 0) {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Quantity cannot be negative";
            die();
        }
    }

    function createOrUpdateCartItem($cartRequest, $pdo) {
        try {

            // Check if user with the given username exists
            $getUserQuery = "SELECT id FROM user WHERE username = '{$cartRequest->username}'";
            $getUser = $pdo->query($getUserQuery);
            $resultUser = $getUser->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultUser)) {
                header('HTTP/1.0 400 Bad Request');
                echo "\n\nNo User Found by username: {$cartRequest->username}";
                die();
            }

            $uID = ($resultUser[0])['id'];
            

            // Check if product wth the given product name exists
            $getProductQuery = "SELECT id FROM product WHERE name = '{$cartRequest->productName}'";
            $getProduct = $pdo->query($getProductQuery);
            $resultProduct = $getProduct->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultProduct)) {
                header('HTTP/1.0 400 Bad Request');
                echo "\n\nNo Product Found by product name: {$cartRequest->productName}";
                die();
            }

            $pID = ($resultProduct[0])['id'];


            // Check if a cart is already created for the user
            // If not, create a cart for the user
            $cartExistsQuery = "SELECT EXISTS(SELECT * FROM cart WHERE user_id = $uID)";
            $cartExists = $pdo->query($cartExistsQuery);
            $resultCartExists = $cartExists->fetchAll(PDO::FETCH_ASSOC);

            if (!($resultCartExists[0] > 0)) {
                $createCartStatement = $pdo->prepare('INSERT INTO cart(user_id) VALUES(:userID)');
                $createCartStatement->bindParam("userID", $uID);
                $createCartStatement->execute();
            }

            $readCartQuery = "SELECT * FROM cart WHERE user_id = $uID";
            $readCart = $pdo->query($readCartQuery);
            $resultCart = $readCart->fetchAll(PDO::FETCH_ASSOC);

            $cID = ($resultCart[0])['id'];
            echo "Cart ID: $cID";

            // Save Cart
            $saveCart = $pdo->prepare('INSERT INTO cart_item(cart_id, product_id, quantity) VALUES(:cartID, :productID, :quantity) ON DUPLICATE KEY UPDATE quantity = :quantity');
            $saveCart->bindParam(':cartID', $cID);
            $saveCart->bindParam(':productID', $pID);
            $saveCart->bindParam(':quantity', $cartRequest->quantity);
            $saveCart->execute();

            $retrieveCartItemsQuery = "SELECT * FROM cart_item";
            $retrieveCartItems = $pdo->query($retrieveCartItemsQuery);
            $resultCartItems = $retrieveCartItems->fetchAll(PDO::FETCH_ASSOC);

            $cCartItems = array();
            foreach($resultCartItems as $row) {
                $cItem = new CartItem($cartRequest->productName, $row['quantity']);
                array_push($cCartItems, $cItem);
            }

            $userCart = new Cart($cID, $cartRequest->username, $cCartItems);
            header('HTTP/1.0 200 OK');
            echo "\n\nAdded Item to Cart";
            $response = json_encode($userCart);
            echo "\n\n";
            echo $response;
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function getAllCarts($pdo) {
        $getCartQuery = "SELECT * FROM cart";
        $getAllCarts = $pdo->query($getCartQuery);
        $resultAllCarts = $getAllCarts->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultAllCarts)) {
            header('HTTP/1.0 404 Resource Not Found');
            echo "\n\nNo Carts Found\n\n";
            die();
        }

        $allCarts = array();

        foreach($resultAllCarts as $row) {

            $cartID = $row['id'];
            $cartUserID = $row['user_id'];


            // Get User with Cart User ID;
            $readUserQuery = "SELECT * FROM user WHERE id = $cartUserID";
            $readUser = $pdo->query($readUserQuery);
            $resultUser = $readUser->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultUser)) {
                header('HTTP/1.0 500 Internal Server Error');
                echo "Internal Server Error: No User Found for User ID: $cartUserID";
                die();
            }

            $cartUsername = ($resultUser[0])['username'];


            $cartItems = array();

            // Read all Cart Items
            $readCartItemsQuery = "SELECT * FROM cart_item WHERE cart_id = $cartID";
            echo "\n\n*****\n\n$readCartItemsQuery\n\n*****\n\n";
            $readCartItems = $pdo->query($readCartItemsQuery);
            $resultAllCartItems = $readCartItems->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($resultAllCartItems)) {

                foreach ($resultAllCartItems as $cartItemRow) {
                    $cartItemProductID = $cartItemRow['product_id'];
                    $cartItemQuantity = $cartItemRow['quantity'];

                    // Read Product with fetched Product ID:
                    $readProductQuery = "SELECT * FROM product WHERE id = $cartItemProductID";
                    $readProduct = $pdo->query($readProductQuery);
                    $resultProduct = $readProduct->fetchAll(PDO::FETCH_ASSOC);
             
                    if (empty($resultProduct)) {
                        header('HTTP/1.0 500 Internal Server Error');
                        echo "Internal Server Error: No Product Found for Product ID: $cartItemProductID";
                        die();
                    }

                    $cartItemProductName = ($resultProduct[0])['name'];

                    $ci = new CartItem($cartItemProductName, $cartItemQuantity);
                    array_push($cartItems, $ci);
                }

            }

            $c = new Cart($cartID, $cartUsername, $cartItems);
            array_push($allCarts, $c);
        }

        header('HTTP/1.0 200 OK');
        $response = json_encode($allCarts);
        echo $response;
    }

    function getCart($cartId, $pdo) {

        $getCartQuery = "SELECT * FROM cart WHERE id = $cartId";
        $getCart = $pdo->query($getCartQuery);
        $resultCart = $getCart->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultCart)) {
            header('HTTP/1.0 404 Resource Not Found');
            echo "\n\nNo Carts Found by Cart ID: $cartId\n\n";
            die();
        }

        $cartUserID = ($resultCart[0])['id'];
        
        // Get User with Cart User ID;
        $readUserQuery = "SELECT * FROM user WHERE id = $cartUserID";
        $readUser = $pdo->query($readUserQuery);
        $resultUser = $readUser->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultUser)) {
            header('HTTP/1.0 500 Internal Server Error');
            echo "Internal Server Error: No User Found for User ID: $cartUserID";
            die();
        }

        $cartUsername = ($resultUser[0])['username'];

        $cartItems = array();

        // Read all Cart Items
        $readCartItemsQuery = "SELECT * FROM cart_item WHERE cart_id = $cartId";
        $readCartItems = $pdo->query($readCartItemsQuery);
        $resultAllCartItems = $readCartItems->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($resultAllCartItems)) {
            foreach ($resultAllCartItems as $cartItemRow) {
                $cartItemProductID = $cartItemRow['product_id'];
                $cartItemQuantity = $cartItemRow['quantity'];

                // Read Product with fetched Product ID:
                $readProductQuery = "SELECT * FROM product WHERE id = $cartItemProductID";
                $readProduct = $pdo->query($readProductQuery);
                $resultProduct = $readProduct->fetchAll(PDO::FETCH_ASSOC);
         
                if (empty($resultProduct)) {
                    header('HTTP/1.0 500 Internal Server Error');
                    echo "Internal Server Error: No Product Found for Product ID: $cartItemProductID";
                    die();
                }

                $cartItemProductName = ($resultProduct[0])['name'];

                $ci = new CartItem($cartItemProductName, $cartItemQuantity);
                array_push($cartItems, $ci);
            }
        }

        $cart = new Cart($cartId, $cartUsername, $cartItems);
        header('HTTP/1.0 200 OK');
        $response = json_encode($cart);
        echo $response;
    }

    function deleteCart($cartID, $pdo) {
        try {
            $readCartQuery = "SELECT * FROM cart WHERE id = $cartID";
            $readCart = $pdo->query($readCartQuery);
            $resultCart = $readCart->fetchAll(PDO::FETCH_ASSOC);
    
            if(empty($resultCart)) {
                header('HTTP/1.0 404 No Resource Found');
                echo "No Cart Found By Cart ID: $cartID";
                die();
            }
    
            // Delete All Cart Items
            $deleteCartItemsStatement = $pdo->prepare("DELETE FROM cart_item WHERE cart_id = :cartID");
            $deleteCartItemsStatement->bindParam("cartID", $cartID);
            $deleteCartItemsStatement->execute();
    

            // Delete Cart
            $deleteCartStatement = $pdo->prepare("DELETE FROM cart WHERE id = :cartID");
            $deleteCartStatement->bindParam("cartID", $cartID);
            $deleteCartStatement->execute();

            header('HTTP/1.0 200 OK');
            echo "Deleted Cart Items and Cart associated with Cart ID: $cartID";

        } catch (PDOException $e) {
            header('HTTP/1.0 500 Internal Server Error');
            echo "ERROR: ".$e->getMessage();
        }
    }

    function deleteAllCarts($pdo) {
        try {
            $readAllCartsQuery = "SELECT * FROM cart";
            $readAllCarts = $pdo->query($readAllCartsQuery);
            $resultAllCarts = $readAllCarts->fetchAll(PDO::FETCH_ASSOC);
    
            if(empty($resultAllCarts)) {
                header('HTTP/1.0 404 No Resource Found');
                echo "No Cart(s) To Delete";
                die();
            }
    
            foreach($resultAllCarts as $rowCart) {

                $cartID = $rowCart['id'];

            }
            // Delete All Cart Items
            $deleteCartItemsStatement = $pdo->prepare("DELETE FROM cart_item WHERE cart_id = :cartID");
            $deleteCartItemsStatement->bindParam("cartID", $cartID);
            $deleteCartItemsStatement->execute();
    

            // Delete Cart
            $deleteCartStatement = $pdo->prepare("DELETE FROM cart WHERE id = :cartID");
            $deleteCartStatement->bindParam("cartID", $cartID);
            $deleteCartStatement->execute();

            header('HTTP/1.0 200 OK');
            echo "Deleted Cart Items and Cart associated with Cart ID: $cartID";

        } catch (PDOException $e) {
            header('HTTP/1.0 500 Internal Server Error');
            echo "ERROR: ".$e->getMessage();
        } 
    }

?>