<?php 

    class UserDAO {
        public $id;
        public $username;
        public $email;
        public $password;

        // Constructor
        public function __construct($id, $username, $email, $password) {
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
        }
    }

    class ShippingAddressDAO {
        public $id;
        public $userId;
        public $unitNo;
        public $street;
        public $city;
        public $province;
        public $country;
        public $zipCode;
    
        // Constructor
        public function __construct($id, $userId, $unitNo, $street, $city, $province, $country, $zipCode) {
            $this->id = $id;
            $this->userId = $userId;
            $this->unitNo = $unitNo;
            $this->street = $street;
            $this->city = $city;
            $this->province = $province;
            $this->country = $country;
            $this->zipCode = $zipCode;
        }
    }
?>