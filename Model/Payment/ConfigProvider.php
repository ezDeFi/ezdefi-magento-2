<?php

namespace Ezdefi\Payment\Model\Payment;

use Magento\Checkout\Model\ConfigProviderInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Ezdefi\Payment\Model\CurrencyFactory;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class SampleConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    private $_currencyFactory;
    private $_gatewayHelper;
    private $_cart;
    private $_scopeConfig;

    function __construct(
        GatewayHelper $gatewayHelper,
        CurrencyFactory $currencyFactory,
        OrderRepositoryInterface $orderRepo,
        \Magento\Checkout\Model\Session $cart,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_orderRepo = $orderRepo;
        $this->_cart = $cart;
        $this->_gatewayHelper = $gatewayHelper;
        $this->_currencyFactory = $currencyFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $totalPrice = $this->_cart->getQuote()->getGrandTotal();
        $storeCurrency= $this->_cart->getQuote()->getStoreCurrencyCode();

        $currencies = $this->_currencyFactory->create()->getCollection()->getData();
        $currenciesWithPrice = $this->_gatewayHelper->getCurrenciesWithPrice($currencies, $totalPrice , $storeCurrency);

        $paymentMethod = $this->_scopeConfig->getValue('payment/ezdefi_payment/payment_method');
        $simpleMethod = strpos($paymentMethod, 'simple') !== false ? 'enable' : false;
        $ezdefiMethod = strpos($paymentMethod, 'ezdefi') !== false ? 'enable' : false;

        return [
            'currencies'    => $currenciesWithPrice,
            'simpleMethod' => $simpleMethod,
            'ezdefiMethod' => $ezdefiMethod,

        ];
    }
}