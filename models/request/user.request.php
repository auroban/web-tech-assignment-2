<?php 

    class UserCreateRequest {
        public $username;
        public $email;
        public $password;
        public $shippingAddress;

        // Constructor
        public function __construct($username, $email, $password, $shippingAddress) {
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->shippingAddress = $shippingAddress;
        }
    }

    class ShippingAddressCreateRequest {
        public $unitNo;
        public $street;
        public $city;
        public $province;
        public $country;
        public $zipCode;
    
        // Constructor
        public function __construct($unitNo, $street, $city, $province, $country, $zipCode) {
            $this->unitNo = $unitNo;
            $this->street = $street;
            $this->city = $city;
            $this->province = $province;
            $this->country = $country;
            $this->zipCode = $zipCode;
        }
    }

    class UserUpdateRequest {
        public $username;
        public $email;
        public $password;
        public $shippingAddress;

        // Constructor
        public function __construct($username, $email, $password, $shippingAddress) {
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->shippingAddress = $shippingAddress;
        }
    }

    class ShippingAddressUpdateRequest {
        public $unitNo;
        public $street;
        public $city;
        public $province;
        public $country;
        public $zipCode;
    
        // Constructor
        public function __construct($unitNo, $street, $city, $province, $country, $zipCode) {
            $this->unitNo = $unitNo;
            $this->street = $street;
            $this->city = $city;
            $this->province = $province;
            $this->country = $country;
            $this->zipCode = $zipCode;
        }
    }


?>