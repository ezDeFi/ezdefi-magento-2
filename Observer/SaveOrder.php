<?php

namespace Cloudways\EventsObservers\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Data implements ObserverInterface
{
    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $orderId = $order->getId();
//        $modifiedName = $originalName . ' - Modified by Magento 2 Events and Observers';
//        $product->setName($modifiedName);
    }
}