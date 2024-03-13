<?php 
    class PurchaseOrderResponse {
        public $id;
        public $userId;
        public $status;
        public $createdOn;
        public $orderItems;
    
        // Constructor
        public function __construct($id, $userId, $status, $createdOn, $orderItems) {
            $this->id = $id;
            $this->userId = $userId;
            $this->status = $status;
            $this->createdOn = $createdOn;
            $this->orderItems = $orderItems;
        }
    }

    class OrderItemResponse {
        public $id;
        public $productId;
        public $quantity;
        public $totalCost;
    
        // Constructor
        public function __construct($id, $productId, $quantity, $totalCost) {
            $this->id = $id;
            $this->productId = $productId;
            $this->quantity = $quantity;
            $this->totalCost = $totalCost;
        }
    }
?>