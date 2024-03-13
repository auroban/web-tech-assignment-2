<?php 
    require_once (__DIR__.'/../configurations/config.php');
    require_once (__DIR__.'/../models/dao/order.dao.php');
    require_once (__DIR__.'/../models/dao/tuple.php');
    require_once (__DIR__.'/../utilities.php');

    function readAllSavedOrders() {

        global $pdo;
        $result = readFromDB("SELECT * FROM purchase_order", $pdo);

        if (empty($result)) {
            return array();
        }

        $allOrderDAOs = array();

        foreach($result as $orderDAO) {
            $id = $orderDAO['id'];
            $userId = $orderDAO['userId'];
            $status = $orderDAO['status'];
            $createdOn = $orderDAO['createdOn'];
            array_push($allOrderDAOs, new PurchaseOrderDAO($id, $userId, $status, $createdOn));
        }
        return $allOrderDAOs;
    }

    function readLastOrderByUserId($userId) {
        global $pdo;

        $result = readFromDB("SELECT * FROM purchase_order WHERE userId = '$userId' ORDER BY createdOn DESC LIMIT 1", $pdo);

        if (empty($result)) {
            return null;
        }
        $orderDAO = $result[0];
        
        $id = $orderDAO['id'];
        $userId = $orderDAO['userId'];
        $status = $orderDAO['status'];
        $createdOn = $orderDAO['createdOn'];
        return new PurchaseOrderDAO($id, $userId, $status, $createdOn);
    }

    function readAllOrdersByUserID($userId) {
        global $pdo;
        $result = readFromDB("SELECT * FROM purchase_order WHERE userId = '$userId'", $pdo);

        if (empty($result)) {
            return array();
        }

        $allOrders = array();
        foreach($result as $row) {
            $id = $row['id'];
            $userId = $row['userId'];
            $status = $row['status'];
            $createdOn = $row['createdOn'];
            array_push($allOrders, new PurchaseOrderDAO($id, $userId, $status, $createdOn));
        }
        return $allOrders;
    }

    function readSavedOrderByID($orderID) {

        global $pdo;

        $result = readFromDB("SELECT * FROM purchase_order WHERE id = '$orderID'", $pdo);

        if (empty($result)) {
            return null;
        }
        $orderDAO = $result[0];
        
        $id = $orderDAO['id'];
        $userId = $orderDAO['userId'];
        $status = $orderDAO['status'];
        $createdOn = $orderDAO['createdOn'];
        return new PurchaseOrderDAO($id, $userId, $status, $createdOn);
        
    }

    function saveOrder($orderDAO) {
        global $pdo;
        $query = "INSERT INTO purchase_order(userId, status, createdOn) VALUES(:userId, :status, :createdOn)";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $orderDAO->userId));
        array_push($tuples, new Tuple('status', $orderDAO->status));
        array_push($tuples, new Tuple('createdOn', $orderDAO->createdOn));
        runStatement($query, $tuples, $pdo);
    }

    function updateSavedOrder($orderDAO) {
        global $pdo;
        $query = "UPDATE purchase_order SET userId = :userId, status = :status WHERE id = :orderID";
        $tuples = array();
        array_push($tuples, new Tuple('userId', $orderDAO->userId));
        array_push($tuples, new Tuple('status', $orderDAO->status));
        array_push($tuples, new Tuple('orderID', $orderDAO->id));
        runStatement($query, $tuples, $pdo);
    }

    function saveOrderItem($orderItemDAO) {
        global $pdo;
        $query = "INSERT INTO order_item(orderId, productId, quantity, totalCost) VALUES(:orderId, :productId, :quantity, :totalCost)";
        $tuples = array();
        array_push($tuples, new Tuple('orderId', $orderItemDAO->orderId));
        array_push($tuples, new Tuple('productId', $orderItemDAO->productId));
        array_push($tuples, new Tuple('quantity', $orderItemDAO->quantity));
        array_push($tuples, new Tuple('totalCost', $orderItemDAO->totalCost));
        runStatement($query, $tuples, $pdo);
    }

    function deleteSavedOrder($orderID) {
        global $pdo;
        $query = "DELETE FROM purchase_order WHERE id = :orderID";
        $tuples = array();
        array_push($tuples, new Tuple('orderID', $orderID));
        runStatement($query, $tuples, $pdo);
    }

    function readAllOrderItemsByOrderID($orderID) {
        global $pdo;
        $result = readFromDB("SELECT * FROM order_item WHERE orderId = '$orderID'", $pdo);

        if (empty($result)) {
            return array();
        }

        $allOrderItems = array();
        foreach($result as $row) {
            $id = $row['id'];
            $orderId = $row['orderId'];
            $productId = $row['productId'];
            $quantity = $row['quantity'];
            $totalCost = $row['totalCost'];
            array_push($allOrderItems, new OrderItemDAO($id, $orderId, $productId, $quantity, $totalCost));
        }
        return $allOrderItems;
    }

    function deleteAllOrderItemsByOrderID($orderID) {
        global $pdo;
        $query = "DELETE FROM order_item WHERE orderId = :orderID";
        $tuples = array();
        array_push($tuples, new Tuple('orderID', $orderID));
        runStatement($query, $tuples, $pdo);
    }
?>