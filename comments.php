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

    class Comment {
        public $id;
        public $username;
        public $productName;
        public $rating;
        public $images;
        public $review;
    
        public function __construct($id = null, $username, $productName, $rating, $iamges = null, $review = null) {
            $this->id = $id ?? "";
            $this->username = $username;
            $this->productName = $productName;
            $this->rating = $rating;
            $this->images = $iamges ?? "";
            $this->review = $review ?? "";
        }
    
        public function displayCommentDetails() {
            echo "Comment ID: {$this->id}\n";
            echo "Username: {$this->username}\n";
            echo "Product Name: {$this->productName}\n";
            echo "Rating: {$this->rating}\n";
            echo "Images: {$this->images}";
            echo "Review: {$this->review}";
        }
    }

    switch($requestMethod) {
        case 'POST' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            validate($data);
            $username = $data['username'];
            $productName = $data['product_name'];
            $rating = $data['rating'];
            $images = isset($data['images']) ? $data['images'] : null;
            $review = isset($data['review']) ? $data['review'] : null;
            $comment = new Comment(null, $username, $productName, $rating, $images, $review);
            createComment($comment, $pdo);
            break;
        }
        case 'PUT' : {
            $requestBody = file_get_contents("php://input");
            $data = json_decode($requestBody, true);
            $username = isset($data['username']) ? $data['username'] : '';
            $productName = isset($data['product_name']) ? $data['product_name'] : '';
            $rating = isset($data['rating']) ? $data['rating'] : '';
            $images = isset($data['images']) ? $data['images'] : '';
            $review = isset($data['review']) ? $data['review'] : '';
            $comment = new Comment(null, $username, $productName, $rating, $images, $review);
            updateComment($comment, $pdo);
            break;
        }
        case 'GET' : {
            getComment($pdo);
            break;
        }
        case 'DELETE' : {
            deleteComment($pdo);
            break;
        }
        default : {
            echo "Method not defined";
            break;
        }
    }


    function createComment($comment, $pdo) {
        try {

            $getUserQuery = "SELECT id FROM user WHERE username = '{$comment->username}'";
            $getUser = $pdo->query($getUserQuery);
            $resultUser = $getUser->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultUser)) {
                header('HTTP/1.0 400 Bad Request');
                echo "\n\nNo User Found by username: {$comment->username}";
                die();
            }

            $uID = ($resultUser[0])['id'];

            $getProductQuery = "SELECT id FROM product WHERE name = '{$comment->productName}'";
            $getProduct = $pdo->query($getProductQuery);
            $resultProduct = $getProduct->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultProduct)) {
                header('HTTP/1.0 400 Bad Request');
                echo "\n\nNo Product Found by product name: {$comment->productName}";
                die();
            }

            $pID = ($resultProduct[0])['id'];

            $saveComment = $pdo->prepare('INSERT INTO comment(user_id, product_id, rating, images, review) VALUES (:userID, :productID, :rating, :images, :review)');
            $saveComment->bindParam(':userID', $uID);
            $saveComment->bindParam(':productID', $pID);
            $saveComment->bindParam(':rating', $comment->rating);
            $saveComment->bindParam(':images', $comment->images);
            $saveComment->bindParam(':review', $comment->review);
            $saveComment->execute();

            $retrieveCommentQuery = "SELECT * FROM comment";
            $retrieveComment = $pdo->query($retrieveCommentQuery);

            $result = $retrieveComment->fetchAll(PDO::FETCH_ASSOC);

            $row = $result[0];
            $cID = $row['id'];
            $cUsername = $comment->username;
            $cProductName = $comment->productName;
            $cRating = $row['rating'];
            $cImages = $row['images'];
            $cReview = $row['review'];
            
            $c = new Comment($cID, $cUsername, $cProductName, $cRating, $cImages, $cReview);
            header('HTTP/1.0 200 OK');
            echo "\n\nCreated Comment";
            $response = json_encode($c);
            echo "\n\n";
            echo $response;
            
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function updateComment($comment, $pdo) {
        try {

            $getUserQuery = "SELECT * FROM user WHERE username = '{$comment->username}'";
            $getUser = $pdo->query($getUserQuery);
            $resultUser = $getUser->fetchAll(PDO::FETCH_ASSOC);

            $uID = null;
            $uUsername = null;
            if (!empty($resultUser)) {
                $row = $resultUser[0];
                $uID = $row['id'];
                $uUsername = $row['username'];
            }

            $getProductQuery = "SELECT id FROM product WHERE name = '{$comment->productName}'";
            $getProduct = $pdo->query($getProductQuery);
            $resultProduct = $getProduct->fetchAll(PDO::FETCH_ASSOC);

            $pID = null;
            $pProductName = null;
            if (!empty($resultProduct)) {
                $row = $resultProduct[0];
                $pID = $row['id'];
                $pProductName = $row['name'];
            }

            $retrieveCommentQuery = "SELECT * FROM comment";
            $retrieveComment = $pdo->query($retrieveCommentQuery);

            $result = $retrieveComment->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                header('HTTP/1.0 404 Resource not found');
                echo "\n\nNo Comment Found";
                die();
            }

            $row = $result[0];
            $cID = $row['id'];
            $prevUserID = $row['user_id'];
            $prevProductID = $row['product_id'];
            $cRating = $row['rating'];


            if (!($comment->rating < 0)) {
                $cRating = $comment->rating;
            }

            $cUserID = $uID ?? $prevUserID;
            $cProductID = $pID ?? $prevProductID;

            $cImages = $comment->images;
            $cReview = $comment->review;
            
            $updateQuery = "UPDATE comment SET user_id = :userID, product_id = :productID, rating = :rating, images = :images, review = :review WHERE id = :commentID";
            $statement = $pdo->prepare($updateQuery);
            $statement->bindParam('commentID', $cID);
            $statement->bindParam('userID', $cUserID);
            $statement->bindParam('productID', $cProductID);
            $statement->bindParam('rating', $cRating);
            $statement->bindParam('images', $cImages);
            $statement->bindParam('review', $cReview);
            $statement->execute();

            $retrieveComment = $pdo->query($retrieveCommentQuery);
            $result = $retrieveComment->fetchAll(PDO::FETCH_ASSOC);
            $row = $result[0];
            $cID = $row['id'];
            $nUsername = $uUsername;
            $nProductName = $pProductName;
            $nRating = $row['rating'];
            $nImages = $row['images'];
            $nReview = $row['review'];

            $c = new Comment($cID, $nUsername, $nProductName, $nRating, $nImages, $nReview);
            echo "\n\nUpdated Comment\n\n";
            $response = json_encode($c);
            header('HTTP/1.0 202 ACCEPTED');
            echo $response;
            
        } catch (PDOException $e) {
            echo "ERROR: ".$e->getMessage();
        }
    }

    function getComment($pdo) {
        $retrieveCommentQuery = "SELECT * FROM comment";
        $retrieveComment = $pdo->query($retrieveCommentQuery);

        $result = $retrieveComment->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            header('HTTP/1.0 404 Resource not found');
            echo "\n\nNo Comment Found";
            die();
        }

        $row = $result[0];
        $cID = $row['id'];
        $cUserID = $row['user_id'];
        $cProductID = $row['product_id'];
        $cRating = $row['rating'];
        $cImages = $row['images'];
        $cReview = $row['review'];


        $getUserQuery = "SELECT * FROM user WHERE id = $cUserID";
        $getUser = $pdo->query($getUserQuery);
        $result = $getUser->fetchAll(PDO::FETCH_ASSOC);
        $row = $result[0];
        $cUsername = $row['username'];

        $getProductQuery = "SELECT * FROM product WHERE id = $cProductID";
        $getProduct = $pdo->query($getProductQuery);
        $result = $getProduct->fetchAll(PDO::FETCH_ASSOC);
        $row = $result[0];
        $cProductName = $row['name'];

        $c = new Comment($cID, $cUsername, $cProductName, $cRating, $cImages, $cReview);

        echo "\n\nRetrieved Comment\n\n";
        $response = json_encode($c);
        header('HTTP/1.0 200 OK');
        echo $response;
    } 

    function deleteComment($pdo) {
       $pdo->exec("TRUNCATE TABLE comment");
       header('HTTP/1.0 200 OK');
       echo "\n\nDeleted";
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

        if (!isset($data['rating']) || $data['rating'] < 0) {
            header('HTTP/1.0 400 Bad Request');
            echo "400 Bad Request: Rating cannot be negative";
            die();
        }
    } 

?>