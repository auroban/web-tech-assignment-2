<?php 

    class ProductResourceCreatedResponse {
        public $id;
        public $type;
        public $uri;

        public function __construct($id, $type, $uri) {
            $this->id = $id;
            $this->type = $type;
            $this->uri = $uri;
        }
    }


    class ProductCreatedResponse {
        public $id;
        public $name;
        public $description;
        public $price;
        public $shippingCost;
        public $currency;
        public $resources;

        public function __construct($id, $name, $description, $price, $shippingCost, $currency, $resources) {
            $this->id = $id;
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->shippingCost = $shippingCost;
            $this->currency = $currency;
            $this->resources = $resources;
        }
    }

?>