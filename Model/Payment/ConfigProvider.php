<?php

namespace Ezdefi\Payment\Model\Payment;

use Magento\Checkout\Model\ConfigProviderInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Ezdefi\Payment\Model\CurrencyFactory;
use Ezdefi\Payment\Helper\GatewayHelper;

/**
 * Class SampleConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    private $_currencyFactory;

    private $_gatewayHelper;

    private $_cart;

    function __construct(
        GatewayHelper $gatewayHelper,
        CurrencyFactory $currencyFactory,
        OrderRepositoryInterface $orderRepo,
        \Magento\Checkout\Model\Session $cart
    )
    {
        $this->_orderRepo = $orderRepo;
        $this->_cart = $cart;
        $this->_gatewayHelper = $gatewayHelper;
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
//        $orderId = $this->_cart->getLastOrderId();
        $order = $this->_orderRepo->get(29);

        $currencies = $this->_currencyFactory->create()->getCollection()->getData();
        $currenciesWithPrice = $this->_gatewayHelper->getCurrenciesWithPrice($currencies, $order->getTotalDue(), $order->getStoreCurrencyCode());

        return [
            'currencies' => $currenciesWithPrice,
        ];
    }
}