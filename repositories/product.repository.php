<?php 
    require_once (__DIR__.'/../configurations/config.php');
    require_once (__DIR__.'/../models/dao/product.dao.php');
    require_once (__DIR__.'/../models/dao/tuple.php');
    require_once (__DIR__.'/../utilities.php');

    function readAllProducts() {

        global $pdo;

        $result = readFromDB("SELECT * FROM product", $pdo);

        if (empty($result)) {
            return array();
        }

        $allProducts = array();

        foreach($result as $product) {
            $id = $product['id'];
            $name = $product['name'];
            $description = $product['description'];
            $price = $product['price'];
            $shippingCost = $product['shipping_cost'];
            $currency = $product['currency'];

            $p = new ProductDAO($id, $name, $description, $price, $shippingCost, $currency);
            array_push($allProducts, $p);
        }
        return $allProducts;
    }


    function readProductByID($productID) {

        global $pdo;

        $result = readFromDB("SELECT * FROM product WHERE id = '$productID'", $pdo);

        if (empty($result)) {
            return null;
        }
        $product = $result[0];
        
        $id = $product['id'];
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $shippingCost = $product['shipping_cost'];
        $currency = $product['currency'];
        return new ProductDAO($id, $name, $description, $price, $shippingCost, $currency);
        
    }

    function readSavedProductByName($name) {

        global $pdo;

        $result = readFromDB("SELECT * FROM product WHERE name = '$name'", $pdo);

        if (empty($result)) {
            return null;
        }
        $product = $result[0];
        
        $id = $product['id'];
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $shippingCost = $product['shipping_cost'];
        $currency = $product['currency'];
        return new ProductDAO($id, $name, $description, $price, $shippingCost, $currency);
        
    }

    function saveProduct($productDAO) {
        global $pdo;
        $query = "INSERT INTO product(name, description, price, shipping_cost, currency) VALUES(:name, :description, :price, :shippingCost, :currency)";
        $tuples = array();
        array_push($tuples, new Tuple('name', $productDAO->name));
        array_push($tuples, new Tuple('description', $productDAO->description));
        array_push($tuples, new Tuple('price', $productDAO->price));
        array_push($tuples, new Tuple('shippingCost', $productDAO->shippingCost));
        array_push($tuples, new Tuple('currency', $productDAO->currency));
        runStatement($query, $tuples, $pdo);
    }

    function updateSavedProduct($productDAO) {
        global $pdo;
        $query = "UPDATE product SET name = :name, description = :description, price = :price, shipping_cost = :shippingCost , currency = :currency WHERE id = :productID";
        $tuples = array();
        array_push($tuples, new Tuple('name', $productDAO->name));
        array_push($tuples, new Tuple('description', $productDAO->description));
        array_push($tuples, new Tuple('price', $productDAO->price));
        array_push($tuples, new Tuple('shippingCost', $productDAO->shippingCost));
        array_push($tuples, new Tuple('currency', $productDAO->currency));
        array_push($tuples, new Tuple('productID', $productDAO->id));
        runStatement($query, $tuples, $pdo);
    }

    function saveProductResource($productResourceDAO) {
        global $pdo;
        $query = "INSERT INTO product_resource(product_id, type, uri) VALUES(:productID, :type, :uri)";
        $tuples = array();
        array_push($tuples, new Tuple('productID', $productResourceDAO->productId));
        array_push($tuples, new Tuple('type', $productResourceDAO->type));
        array_push($tuples, new Tuple('uri', $productResourceDAO->uri));
        runStatement($query, $tuples, $pdo);
    }

    function updateSavedProductResource($productResourceDAO) {
        global $pdo;
        $query = "UPDATE product_resource SET product_id = :productID, type = :type, uri = :uri WHERE id = :productResourceID";
        $tuples = array();
        array_push($tuples, new Tuple('productID', $productResourceDAO->productId));
        array_push($tuples, new Tuple('type', $productResourceDAO->type));
        array_push($tuples, new Tuple('uri', $productResourceDAO->uri));
        array_push($tuples, new Tuple('productResourceID', $productResourceDAO->id));
        runStatement($query, $tuples, $pdo);
    }

    function readAllSavedProductResourcesByProductID($productID) {
        global $pdo;
        $result = readFromDB("SELECT * FROM product_resource WHERE product_id = '$productID'", $pdo);

        if (empty($result)) {
            return array();
        }
        $allProductResources = array();
        foreach($result as $productResource) {
            $id = $productResource['id'];
            $productId = $productResource['product_id'];
            $type = $productResource['type'];
            $uri = $productResource['uri'];
            array_push($allProductResources, new ProductResourceDAO($id, $productId, $type, $uri));
        }
        return $allProductResources;
    }

    function readSavedProductResourceByID($productResourceID) {
        global $pdo;
        $result = readFromDB("SELECT * FROM product_resource WHERE id = '$productResourceID'", $pdo);

        if (empty($result)) {
            return null;
        }
        $productResource = $result[0];
        $id = $productResource['id'];
        $productId = $productResource['product_id'];
        $type = $productResource['type'];
        $uri = $productResource['uri'];
        return new ProductResourceDAO($id, $productId, $$type, $uri);
    }

    function deleteAllSavedProductResourcesByProductID($productID) {
        global $pdo;
        $query = "DELETE FROM product_resource WHERE product_id = :productID";
        $tuples = array();
        array_push($tuples, new Tuple('productID', $productID));
        runStatement($query, $tuples, $pdo);
    }

    function deleteSavedProductDAOByID($productID) {
        global $pdo;
        $query = "DELETE FROM product WHERE id = :productID";
        $tuples = array();
        array_push($tuples, new Tuple('productID', $productID));
        runStatement($query, $tuples, $pdo);
    }
?>