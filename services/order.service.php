<?php 

    require_once(__DIR__.'/../repositories/order.repository.php');
    require_once(__DIR__.'/../models/request/order.request.php');
    require_once(__DIR__.'/../models/response/order.response.php');
    require_once(__DIR__.'/../constants/enum.php');


    function toCreateRequest($data) {
        $userId = $data['userId'];
        $status = $data['status'];
        $oItems = $data['orderItems'];
        $orderItems = array();

        foreach($oItems as $oItem) {
            $productId = $oItem['productId'];
            $quantity = $oItem['quantity'];
            $totalCost = $oItem['totalCost'];
            array_push($orderItems, new OrderItemRequest($productId, $quantity, $totalCost));
        }
        return new PurchaseOrderRequest($userId, $status, $orderItems);
    }

    function toUpdateRequest($data) {
        return new PurchaseOrderUpdateRequest(
            getStringOrDefault($data, 'userId', ''),
            getValidEnumOrDefault($data, 'status', orderStatuses, null)
        );
    }


    function toPurchaseOrderDAO($orderCreationRequest) {
        $id = null;
        $userId = $orderCreationRequest->userId;
        $status = $orderCreationRequest->status;
        $createdOn = null;
        return new PurchaseOrderDAO($id, $userId, $status, $createdOn);
    }

    function toOrderItemDAO($orderItemRequest, $orderId) {
        $id = null;
        $productId = $orderItemRequest->productId;
        $quantity = $orderItemRequest->quantity;
        $totalCost = $orderItemRequest->totalCost;
        return new OrderItemDAO($id, $orderId, $productId, $quantity, $totalCost);
    }

    function toOrderItemResponse($orderItemDAO) {
        $id = $orderItemDAO->id;
        $productId = $orderItemDAO->productId;
        $quantity = $orderItemDAO->quantity;
        $totalCost = $orderItemDAO->totalCost;
        return new OrderItemResponse($id, $productId, $quantity, $totalCost);
    }

    function toOrderResponse($orderDAO, $orderItems) {
        $id = $orderDAO->id;
        $userId = $orderDAO->userId;
        $status = $orderDAO->status;
        $createdOn = $orderDAO->createdOn;
        return new PurchaseOrderResponse($id, $userId, $status, $createdOn, $orderItems);
    }


    


    function createOrder($orderCreationRequest) {
        $orderDAO = toPurchaseOrderDAO($orderCreationRequest);
        saveOrder($orderDAO);
        $orderDAO = readLastOrderByUserId($orderCreationRequest->userId);

        $orderItems = $orderCreationRequest->orderItems;
        foreach($orderItems as $orderItem) {
            $orderItemDAO = toOrderItemDAO($orderItem, $orderDAO->id);
            saveOrderItem($orderItemDAO);
        }
        $savedOrderItems = readAllOrderItemsByOrderID($orderDAO->id);
        return toOrderResponse($orderDAO, $savedOrderItems);
    }

    function updateOrder($orderUpdateRequest, $orderID) {

        $orderDAO = readSavedOrderByID($orderID);
        if ($orderDAO == null) {
            return null;
        }

        $oUserId = $orderDAO->userId;
        $oStatus = $orderDAO->status;

        $nUserId = getValidNumericValueOrDedault($orderUpdateRequest, 'userId', $oUserId);
        $nStatus = getValidNumericValueOrDedault($orderUpdateRequest, 'status', $oStatus);

        $orderDAO = new PurchaseOrderDAO($orderID, $nUserId, $nStatus, null);
        updateSavedOrder($orderDAO);
        $orderDAO = readSavedOrderByID($orderID);

        $orderItems = array();
        $savedOrderItems = readAllOrderItemsByOrderID($orderID);
        foreach($savedOrderItems as $oi) {
            array_push($orderItems, toOrderItemResponse($oi));
        }
        return toOrderResponse($orderDAO, $orderItems);
    }

    function getOrderByID($orderId) {
        $savedOrderDAO = readSavedOrderByID($orderId);
        if ($savedOrderDAO == null) {
            return null;
        }
        $orderItems = array();
        $savedOrderItems = readAllOrderItemsByOrderID($orderId);
        foreach($savedOrderItems as $oi) {
            array_push($orderItems, toOrderItemResponse($oi));
        }
        return toOrderResponse($savedOrderDAO, $orderItems);
    }

    function getOrders() {
        $orders = readAllSavedOrders();
        if (empty($orders)) {
            return array();
        }
        $allOrders = array();
        foreach($orders as $o) {
            array_push($allOrders, getOrderByID($o->id));
        }
        return $allOrders;
    }

    function getOrdersByUserId($userId) {
        $allOrderDAOs = readAllOrdersByUserID($userId);
        if (empty($allOrderDAOs)) {
            return array();
        }
        $allOrders = array();
        foreach($allOrderDAOs as $orderDAO) {
            $oId = $orderDAO->id;
            $orderItemDAOs = readAllOrderItemsByOrderID($oId);
            if (empty($orderItemDAOs)) {
                continue;
            }

            $allOrderItems = array();
            foreach($orderItemDAOs as $orderItemDAO) {
                $oItem = toOrderItemResponse($orderItemDAO);
                array_push($allOrderItems, $oItem);
            }
            $rOrder = toOrderResponse($orderDAO, $allOrderItems);
            array_push($allOrders, $rOrder);
        }
        return $allOrders;
    }

    function deleteOrderByID($orderId) {
        deleteAllOrderItemsByOrderID($orderId);
        deleteSavedOrder($orderId);
    }

    function deleteOrders() {
        $savedOrders = readAllSavedOrders();
        foreach($savedOrders as $o) {
            deleteOrderByID($o->id);
        }
    }
?>