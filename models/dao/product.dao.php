<?php 

    class ProductDAO {
        public $id;
        public $name;
        public $description;
        public $price;
        public $shippingCost;
        public $currency;

        public function __construct($id, $name, $description, $price, $shippingCost, $currency) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->shippingCost = $shippingCost;
            $this->currency = $currency;
        }
    }

    class ProductResourceDAO {
        public $id;
        public $productId;
        public $type;
        public $uri;

        public function __construct($id, $productId, $type, $uri) {
            $this->id = $id;
            $this->productId = $productId;
            $this->type = $type;
            $this->uri = $uri;
        }
    }
?>