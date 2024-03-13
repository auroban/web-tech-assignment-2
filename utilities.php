<?php
    function getStringOrDefault($data, $key, $default) {

        if (is_array($data)) {
            if (!isset($data[$key])) {
                return $default;
            }

            if (empty($data[$key]) || strlen($data[$key]) == 0 || trim($data[$key]) == "" ) {
                return $default;
            }
            return $data[$key];
        } else {
            if (empty($data->{$key}) || strlen($data->{$key}) == 0 || trim($data->{$key}) == "" ) {
                return $default;
            }
            return $data->{$key};
        }
    }

    function getValidNumericValueOrDedault($data, $key, $default) {

        if (is_array($data)) {
            if (!isset($data[$key])) {
                return $default;
            }
    
            if ($data[$key] < 0) {
                return $default;
            }
    
            return $data[$key];
        } else {
            if ($data->{$key} == null || $data->{$key} < 0) {
                return $default;
            }
            return $data->{$key};
        }
    }

    function getValidEnumOrDefault($data, $key, $acceptedValues, $default) {

        if (is_array($data)) {
            if (!isset($data[$key])) {
                return $default;
            }
            if (!in_array($data[$key], $acceptedValues)) {
                return $default;
            }
            return $data[$key];
        } else {
            if ($data->{$key} == null) {
                return $default;
            }
            if (!in_array($data->{$key}, $acceptedValues)) {
                return $default;
            }
            return $data->{$key};
        }
    }

    function isAValidID($id) {
        if (empty($id) || strlen($id) == 0 || trim($id) == "") {
            return false;
        }

        if (!is_numeric($id) || $id < 0) {
            return false;
        }
        return true;
    }

    function isNonNegative($num) {
        if (empty($num) || strlen($num) == 0 || trim($num) == "") {
            return false;
        }

        if (!is_numeric($num) || $num < 0) {
            return false;
        }
        return true;
    }

    function isEmpty($str) {
        return (empty($str) || strlen($str) == 0 || trim($str) == "");
    }

    function isAValidEnum($val, $arr) {
        return in_array($val, $arr);
    }

    function sendBadRequest($msg) {
        header('HTTP/1.0 400 Bad Request');
        echo $msg;
        die();
    }

    function sendInternalServerError($err) {
        header('HTTP/1.0 400 Bad Request');
        echo $err;
        die();
    }

    function sendSuccess() {
        header('HTTP/1.0 200 OK');
        echo 'Success';
        die();
    }

    function sendSuccessWithMessage($message) {
        header('HTTP/1.0 200 OK');
        echo $message;
        die();
    }

    function sendNotFound($message) {
        header('HTTP/1.0 404 OK');
        echo $message;
        die();
    }

    function hashPW($password) {
        if (isEmpty($password)) {
            return '';
        }
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function readFromDB($query, $pdo) {
        $read = $pdo->query($query);
        return $read->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function runStatement($query, $tuples, $pdo) {
        $statement = $pdo->prepare($query);
        foreach($tuples as $tuple) {
            $statement->bindParam($tuple->key, $tuple->value);
        }
        $statement->execute();
    }
?>