<?php 

    class UserCreatedResponse {
        public $id;
        public $username;
        public $email;
        public $password;
        public $shippingAddress;
        public $orders;

        // Constructor
        public function __construct($id, $username, $email, $password, $shippingAddress, $orders) {
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->shippingAddress = $shippingAddress;
            $this->orders = $orders;
        }
    }

    class ShippingAddressResponse {
        public $id;
        public $unitNo;
        public $street;
        public $city;
        public $province;
        public $country;
        public $zipCode;
    
        // Constructor
        public function __construct($id, $unitNo, $street, $city, $province, $country, $zipCode) {
            $this->id = $id;
            $this->unitNo = $unitNo;
            $this->street = $street;
            $this->city = $city;
            $this->province = $province;
            $this->country = $country;
            $this->zipCode = $zipCode;
        }
    }
?>