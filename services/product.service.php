<?php 

    require_once(__DIR__.'/../repositories/product.repository.php');
    require_once(__DIR__.'/../models/request/product.request.php');
    require_once(__DIR__.'/../models/response/product.response.php');
    require_once(__DIR__.'/../constants/enum.php');


    function toCreateRequest($data) {
        $name = $data['name'];
        $description = isset($data['description']) ? $data['description'] : '';
        $price = $data['price'];
        $shippingCost = $data['shipping_cost'];
        $currency = $data['currency'];
        $productResources = array();

        if (isset($data['resources']) && !empty($data['resources'])) {
            foreach($data['resources'] as $r) {
                $type = $r['type'];
                $uri = $r['uri'];
                $pr = new ProductResourceCreationRequest($type, $uri);
                array_push($productResources, $pr);
            }
        }
        return new ProductCreationRequest($name, $description, $price, $shippingCost, $currency, $productResources);
    }

    function toUpdateRequest($data) {
        return new ProductUpdateRequest(
            getStringOrDefault($data,'name', ''),
            getStringOrDefault($data, 'description', ''),
            getValidNumericValueOrDedault($data, 'price', null),
            getValidNumericValueOrDedault($data, 'shipping_cost', null),
            getValidEnumOrDefault($data,'currency', currencies, null));
    }


    function toProductDAO($productCreationRequest) {
        $id = null;
        $name = $productCreationRequest->name;
        $description = $productCreationRequest->description;
        $price = $productCreationRequest->price;
        $shippingCost = $productCreationRequest->shippingCost;
        $currency = $productCreationRequest->currency;
        return new ProductDAO($id, $name, $description, $price, $shippingCost, $currency);
    }

    function toProductResourceDAO($productResourceCreationResource, $productId) {
        $id = null;
        $type = $productResourceCreationResource->type;
        $uri = $productResourceCreationResource->uri;
        return new ProductResourceDAO($id, $productId, $type, $uri);
    }

    function toProductResourceCreatedResponse($productResourceDAO) {
        $id = $productResourceDAO->id;
        $type = $productResourceDAO->type;
        $uri = $productResourceDAO->uri;
        return new ProductResourceCreatedResponse($id, $type, $uri);
    }

    function toProductCreatedResponse($productDAO, $productResources) {
        $id = $productDAO->id;
        $name = $productDAO->name;
        $description = $productDAO->description;
        $price = $productDAO->price;
        $shippingCost = $productDAO->shippingCost;
        $currency = $productDAO->currency;
        $resources = $productResources;
        return new ProductCreatedResponse($id, $name, $description, $price, $shippingCost, $currency, $resources);
    }
    


    function createProduct($productCreationRequest) {
        $productDAO = toProductDAO($productCreationRequest);
        saveProduct($productDAO);
        $productDAO = readSavedProductByName($productCreationRequest->name);


        $resources = $productCreationRequest->resources;
        if (!empty($resources)) {
            foreach($resources as $r) {
                $prDAO = toProductResourceDAO($r, $productDAO->id);
                saveProductResource($prDAO);
            }
        }

        $pID = $productDAO->id;
        $prDAOs = readAllSavedProductResourcesByProductID($pID);
        $prResponse = array();
        foreach($prDAOs as $p) {
            array_push($prResponse, toProductResourceCreatedResponse($p));
        }
        return toProductCreatedResponse($productDAO, $prResponse);
    }

    function updateProduct($productUpdateRequest, $productID) {

        $productDAO = readProductByID($productID);
        if ($productDAO != null) {
            $pID = $productDAO->id;
            $oName = $productDAO->name;
            $oPrice = $productDAO->price;
            $oShippingCost = $productDAO->shippingCost;
            $oCurrency = $productDAO->currency;

            
            $nName = getStringOrDefault($productUpdateRequest, 'name', $oName);
            $nDescription = $productUpdateRequest->description; 
            $nPrice = getValidNumericValueOrDedault($productUpdateRequest, 'price', $oPrice);
            $nShippingCost = getValidNumericValueOrDedault($productUpdateRequest, 'shippingCost', $oShippingCost);
            $nCurrency = getValidEnumOrDefault($productUpdateRequest, 'currency', currencies, $oCurrency);

            $updateProductDAO = new ProductDAO($pID, $nName, $nDescription, $nPrice, $nShippingCost, $nCurrency);
            updateSavedProduct($updateProductDAO);
            
            $updateProductDAO = readProductByID($pID);
            $resources = readAllSavedProductResourcesByProductID($pID);
            return toProductCreatedResponse($updateProductDAO, $resources);
        }

    }

    function getProducts() {
        $productDAOs = readAllProducts();
        if (empty($productDAOs)) {
            return array();
        }

        $allProducts = array();
        foreach($productDAOs as $productDAO) {
            $productResources = array();
            $productResourceDAOs = readAllSavedProductResourcesByProductID($productDAO->id);
            if (!empty($productResourceDAOs)) {
                foreach($productResourceDAOs as $productResourceDAO) {
                    array_push($productResources, toProductResourceCreatedResponse($productResourceDAO));
                }
            }
            array_push($allProducts, toProductCreatedResponse($productDAO, $productResources));
        }
        return $allProducts;
    }


    function getProductByID($productID) {
        $productDAO = readProductByID($productID);
        if ($productDAO == null) {
            return null;
        }
        $productResources = array();
        $productResourceDAOs = readAllSavedProductResourcesByProductID($productDAO->id);
        if (!empty($productResourceDAOs)) {
            foreach($productResourceDAOs as $productResourceDAO) {
                array_push($productResources, toProductResourceCreatedResponse($productResourceDAO));
            }
        }
        return toProductCreatedResponse($productDAO, $productResources);
    }

    function deleteAllProducts() {
        $productDAOs = readAllProducts();
        foreach($productDAOs as $productDAO) {
            deleteProductByID($productDAO->id);
        }
    }

    function deleteProductByID($productID) {
        $productDAO = readProductByID($productID);
        if ($productDAO != null) {
            deleteAllSavedProductResourcesByProductID($productID);
            deleteSavedProductDAOByID($productID);
        }

    }




?>