<?php 
    class PurchaseOrderRequest {
        public $userId;
        public $status;
        public $orderItems;
    
        // Constructor
        public function __construct($userId, $status, $orderItems) {
            $this->userId = $userId;
            $this->status = $status;
            $this->orderItems = $orderItems;
        }
    }

    class OrderItemRequest {
        public $productId;
        public $quantity;
        public $totalCost;
    
        // Constructor
        public function __construct($productId, $quantity, $totalCost) {
            $this->productId = $productId;
            $this->quantity = $quantity;
            $this->totalCost = $totalCost;
        }
    }

    class PurchaseOrderUpdateRequest {
        public $userId;
        public $status;
        // Constructor
        public function __construct($userId, $status) {
            $this->userId = $userId;
            $this->status = $status;
        }
    }
?>