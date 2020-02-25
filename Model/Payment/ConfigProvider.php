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
    private $_assetRepo;

    function __construct(
        GatewayHelper $gatewayHelper,
        CurrencyFactory $currencyFactory,
        OrderRepositoryInterface $orderRepo,
        \Magento\Checkout\Model\Session $cart,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo
    )
    {
        $this->_orderRepo       = $orderRepo;
        $this->_cart            = $cart;
        $this->_gatewayHelper   = $gatewayHelper;
        $this->_currencyFactory = $currencyFactory;
        $this->_scopeConfig     = $scopeConfig;
        $this->_assetRepo       = $assetRepo;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $totalPrice    = $this->_cart->getQuote()->getGrandTotal();
        $storeCurrency = $this->_cart->getQuote()->getStoreCurrencyCode();
        $websiteData   = $this->_gatewayHelper->getWebsiteData();

        $simpleMethod        = $websiteData->website->payAnyWallet;
        $ezdefiMethod        = $websiteData->website->payEzdefiWallet;
        $currencies          = $websiteData->coins;
        $currenciesWithPrice = $this->_gatewayHelper->getCurrenciesWithPrice($currencies, $totalPrice, $storeCurrency);


        return [
            '$websiteData' => $websiteData,
            'currencies'   => $currenciesWithPrice,
            'simpleMethod' => $simpleMethod,
            'ezdefiMethod' => $ezdefiMethod,
            'ezdefiLogo'   => $this->_assetRepo->getUrl("Ezdefi_Payment::image/ezdefi-logo.png")
        ];
    }
}