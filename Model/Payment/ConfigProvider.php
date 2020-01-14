<?php

namespace Ezdefi\Payment\Model\Payment;

use Magento\Checkout\Model\ConfigProviderInterface;
use \Ezdefi\Payment\Model\CurrencyFactory;

/**
 * Class SampleConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    private $_currencyFactory;

    private $_cart;

    function __construct(
        CurrencyFactory $currencyFactory,
        \Magento\Checkout\Model\Session $cart
    )
    {
        $this->_cart = $cart;
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'currencies' => $this->_currencyFactory->create()->getCollection()->getData(),
            'cart' => $this->_cart->getQuote()->getId()
        ];
    }
}