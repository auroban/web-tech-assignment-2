<?php 
    class PurchaseOrderDAO {
        public $id;
        public $userId;
        public $status;
        public $createdOn;
    
        // Constructor
        public function __construct($id, $userId, $status, $createdOn) {
            $this->id = $id;
            $this->userId = $userId;
            $this->status = $status;
            $this->createdOn = $createdOn;
        }
    }

    class OrderItemDAO {
        public $id;
        public $orderId;
        public $productId;
        public $quantity;
        public $totalCost;
    
        // Constructor
        public function __construct($id, $orderId, $productId, $quantity, $totalCost) {
            $this->id = $id;
            $this->orderId = $orderId;
            $this->productId = $productId;
            $this->quantity = $quantity;
            $this->totalCost = $totalCost;
        }
    }
?>