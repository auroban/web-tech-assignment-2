<?php 
    class ProductResourceCreationRequest {
        public $type;
        public $uri;

        public function __construct($type, $uri) {
            $this->type = $type;
            $this->uri = $uri;
        }
    }


    class ProductCreationRequest {
        public $name;
        public $description;
        public $price;
        public $shippingCost;
        public $currency;
        public $resources;
    
        public function __construct($name, $description, $price, $shippingCost, $currency, $resources) {
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->shippingCost = $shippingCost;
            $this->currency = $currency;
            $this->resources = $resources;
        }
    }

    class ProductUpdateRequest {
        public $name;
        public $description;
        public $price;
        public $shippingCost;
        public $currency;

        public function __construct($name, $description, $price, $shippingCost, $currency) {
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->shippingCost = $shippingCost;
            $this->currency = $currency;
        }
    }
?>