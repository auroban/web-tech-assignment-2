<?php 

    class Currency {
        const USD = 'USD';
        const CAD = 'CAD';
    }

    class ProductResourceType {
        const IMAGE = 'IMAGE';
        const VIDEO = 'VIDEO';
        const AUDIO = 'AUDIO';
    }

    class OrderStatus {
        const CREATED = 'CREATED';
        const PROCESSING = 'PROCESSING';
        const COMPLETED = 'COMPLETED';
    }

    const orderStatuses = array(OrderStatus::CREATED, OrderStatus::PROCESSING, OrderStatus::COMPLETED);
    const currencies = array(Currency::CAD, Currency::USD);
    const productResourceTypes = array(ProductResourceType::AUDIO, ProductResourceType::VIDEO, ProductResourceType::IMAGE);
?>