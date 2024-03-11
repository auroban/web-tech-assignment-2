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

    class Product {
        public $id;
        public $name;
        public $description;
        public $image;
        public $price;
        public $shippingCost;
        public $currency;
    
        public function __construct($id = null, $name, $description, $price, $shippingCost, $currency, $image) {
            $this->id = $id ?? "";
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->shippingCost = $shippingCost;
            $this->currency = $currency;
            $this->image = $image;
        }
    
        public function displayProductDetails() {
            echo "Product ID: {$this->id}\n";
            echo "Name: {$this->name}\n";
            echo "Description: {$this->description}\n";
            echo "Image: {$this->image}";
            echo "Price: {$this->price} {$this->currency}\n";
            echo "Shipping Cost: {$this->shippingCost} {$this->currency}\n";
        }
    }

    switch($requestMethod) {
        case 'POST' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            validate($data);
            $name = $data['name'];
            $description = isset($data['description']) ? $data['description'] : '';
            $images = isset($data['images']) ? $data['images'] : '';
            $price = $data['price'];
            $shippingCost = $data['shipping_cost'];
            $currency = $data['currency'];
            $product = new Product(null, $name, $description, $price, $shippingCost, $currency, $images);
            createProduct($product, $pdo);
            break;
        }
        case 'PUT' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            $name = isset($data['name']) ? $data['description'] : '';
            $description = isset($data['description']) ? $data['description'] : '';
            $images = isset($data['images']) ? $data['images'] : '';
            $price = isset($data['price']) ? $data['price'] : '';
            $shippingCost = isset($data['shipping_cost']) ? $data['shipping_cost'] : '';
            $currency = isset($data['currency']) ? $data['currency'] : '';
            $product = new Product(null, $name, $description, $price, $shippingCost, $currency, $images);
            updateProduct($product, $pdo);
            break;
        }
        case 'GET' : {
            getProduct($pdo);
            break;
        }
        case 'DELETE' : {
            deleteProduct($pdo);
            break;
        }
        default : {
            echo "Method not defined";
            break;
        }
    }


    function createProduct($product, $pdo) {
        try {
            $saveProduct = $pdo->prepare('INSERT INTO product(name, description, price, shipping_cost, currency, image) VALUES (:name, :description, :price, :shippingCost, :currency, :image)');
            $saveProduct->bindParam(':name', $product->name);
            $saveProduct->bindParam(':description', $product->description);
            $saveProduct->bindParam(':price', $product->price);
            $saveProduct->bindParam(':shippingCost', $product->shippingCost);
            $saveProduct->bindParam(':currency', $product->currency);
            $saveProduct->bindParam(':image', $product->image);
            $saveProduct->execute();

            $retrieveProductQuery = "SELECT id, name, description, price, shipping_cost, currency, created_on, updated_on, image FROM product";
            $retrieveProduct = $pdo->query($retrieveProductQuery);

            $result = $retrieveProduct->fetchAll(PDO::FETCH_ASSOC);

            $row = $result[0];
            $pID = $row['id'];
            $pName = $row['name'];
            $pDescription = $row['description'];
            $pPrice = $row['price'];
            $pImage = $row['image'];
            $pShippingCost = $row['shipping_cost'];
            $pCurrency = $row['currency'];

            $p = new Product($pID, $pName, $pDescription, $pPrice, $pShippingCost, $pCurrency, $pImage);
            header('HTTP/1.0 200 OK');
            echo "\n\nCreated Product";
            $response = json_encode($p);
            echo "\n\n";
            echo $response;
            
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function updateProduct($product, $pdo) {
        try {
            $retrieveProductQuery = "SELECT * FROM product";
            $retrieveProduct = $pdo->query($retrieveProductQuery);

            $result = $retrieveProduct->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                header('HTTP/1.0 404 Resource not found');
                echo "No Data Found";
                die();
            }

            $row = $result[0];
            echo $row['id'];
            $pID = $row['id'];
            $pName = $row['name'];
            $pDescription = $row['description'];
            $pPrice = $row['price'];
            $pImage = $row['image'];
            $pShippingCost = $row['shipping_cost'];
            $pCurrency = $row['currency'];


            if (!empty($product->name) && !(strlen($product->name) == 0) && !(trim($product->name) == "") && $pName != $product->name) {
                $pName = $product->name;
            }

            $pDescription = $product->description;

            if (!($product->price < 0)) {
                $pPrice = $product->price;
            }

            if (!($product->shippingCost < 0)) {
                $pShippingCost = $product->shippingCost;
            }

            $pImage = $product->image;

            if ($product->currency == 'CAD' || $product->currency == 'USD') {
                $pCurrency = $product->currency;
            }


            $updateQuery = "UPDATE product SET name = :name, description = :description, price = :price, shipping_cost = :shippingCost, image = :image, currency = :currency WHERE id = :productID";
            $statement = $pdo->prepare($updateQuery);
            $statement->bindParam('productID', $pID);
            $statement->bindParam('name', $pName);
            $statement->bindParam('description', $pDescription);
            $statement->bindParam('price', $pPrice);
            $statement->bindParam('shippingCost', $pShippingCost);
            $statement->bindParam('image', $pImage);
            $statement->bindParam('currency', $pCurrency);
            $statement->execute();

            $retrieveProduct2 = $pdo->query($retrieveProductQuery);
            $result2 = $retrieveProduct2->fetchAll(PDO::FETCH_ASSOC);
            $row = $result2[0];
            $pID = $row['id'];
            $pName = $row['name'];
            $pDescription = $row['description'];
            $pPrice = $row['price'];
            $pImage = $row['image'];
            $pShippingCost = $row['shipping_cost'];
            $pCurrency = $row['currency'];

            $p = new Product($pID, $pName, $pDescription, $pPrice, $pShippingCost, $pCurrency, $pImage);
            echo "\n\nUpdated Product\n\n";
            $response = json_encode($p);
            header('HTTP/1.0 202 ACCEPTED');
            echo $response;
            
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function getProduct($pdo) {
        $retrieveProductQuery = "SELECT * FROM product";
        $retrieveProduct = $pdo->query($retrieveProductQuery);

        $result = $retrieveProduct->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            header('HTTP/1.0 404 Resource not found');
            echo "No Data Found";
            die();
        }

        $row = $result[0];
        $pID = $row['id'];
        $pName = $row['name'];
        $pDescription = $row['description'];
        $pPrice = $row['price'];
        $pImage = $row['image'];
        $pShippingCost = $row['shipping_cost'];
        $pCurrency = $row['currency'];
        $p = new Product($pID, $pName, $pDescription, $pPrice, $pShippingCost, $pCurrency, $pImage);

        echo "\n\nRetrieved Product\n\n";
        $response = json_encode($p);
        header('HTTP/1.0 200 OK');
        echo $response;
    } 

    function deleteProduct($pdo) {
       $pdo->exec("TRUNCATE TABLE product");
       header('HTTP/1.0 200 OK');
       echo "\n\nDeleted";
    }

    function validate($data) {

        if (!isset($data['name']) || empty($data['name']) || strlen($data['name']) == 0 || trim($data['name']) == "") {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Product Name cannot be empty";
            die();
        }

        if (!isset($data['price']) || $data['price'] < 0.0) {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Product Price cannot be negative";
            die();
        }

        if (!isset($data['shipping_cost']) || $data['shipping_cost'] < 0.0) {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Product Shipping Cost cannot be negative";
            die();
        }

        if (!isset($data['currency']) || !($data['currency'] == 'USD' || $data['currency'] == 'CAD')) {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Unsupported Product Currency";
            die();
        }
    } 

?>