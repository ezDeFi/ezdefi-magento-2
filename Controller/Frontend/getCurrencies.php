<?php

namespace Ezdefi\Payment\Controller\Frontend;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Sales\Api\OrderRepositoryInterface;

class getCurrencies extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_customerSession;
    protected $_cart;
    protected $_scopeConfig;
    protected $_gatewayHelper;
    protected $_orderRepo;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepo,
        GatewayHelper $gatewayHelper,
        \Magento\Checkout\Model\Session $cart
    )
    {
        $this->_pageFactory     = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_cart            = $cart;
        $this->_scopeConfig     = $scopeConfig;
        $this->_gatewayHelper   = $gatewayHelper;
        $this->_orderRepo       = $orderRepo;

        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $totalPrice          = $this->_cart->getQuote()->getGrandTotal();
        $storeCurrency       = $this->_cart->getQuote()->getStoreCurrencyCode();
        $websiteData         = $this->_gatewayHelper->getWebsiteData();
        $currencies          = $websiteData->coins;
        $currenciesWithPrice = $this->_gatewayHelper->getCurrenciesWithPrice($currencies, $totalPrice, $storeCurrency);

        $response->setData(['currencies' => $currenciesWithPrice]);

        return $response;
    }
}
