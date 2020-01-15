<?php
namespace Ezdefi\Payment\Block\Frontend;

use Ezdefi\Payment\Helper\GatewayHelper;
use \Ezdefi\Payment\Model\AmountFactory;
use \Ezdefi\Payment\Model\CurrencyFactory;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Webapi\Rest\Request;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\UrlInterface;

class SimpleMethod extends \Magento\Framework\View\Element\Template
{
    protected $_amountFactory;
    protected $_currencyFactory;
    protected $_cart;
    protected $_request;
    protected $_orderRepo;
    protected $_scopeConfig;
    protected $_gatewayHelper;
    protected $_urlInterface;
    protected $_payment;
    protected $originValue;

    public function __construct(
        AmountFactory $amountFactory,
        CurrencyFactory $currencyFactory,
        OrderRepositoryInterface $orderRepo,
        Session $cart,
        Request $request,
        ScopeConfigInterface $scopeConfig,
        GatewayHelper $gatewayHelper,
        UrlInterface $urlInterface,
        Context $context
    )
    {
        $this->_cart = $cart;
        $this->_request = $request;
        $this->_orderRepo = $orderRepo;
        $this->_amountFactory = $amountFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_gatewayHelper = $gatewayHelper;
        $this->_urlInterface = $urlInterface;

        parent::__construct($context);
    }

    public function _construct() {
        $orderId = $this->_cart->getLastOrderId();

        $currencyId = json_decode($this->_request->getContent())->currency_id;

        $amountCollection  = $this->_amountFactory->create();
        $cryptoCurrency    = $this->_currencyFactory->create()->getCollection()->addFieldToFilter('currency_id', $currencyId)->getData()[0];
        $order             = $this->_orderRepo->get($orderId);
        $originCurrency    = $order->getStoreCurrencyCode();
//        $this->originValue = $order->getTotalDue();
        $this->originValue = 0.001;
        $amount            = $this->_gatewayHelper->getExchange($originCurrency, $cryptoCurrency['symbol']) * $this->originValue;


        $amountId = (float)$amountCollection->getCollection()->createAmountId(
            $cryptoCurrency['symbol'], $amount,
            $cryptoCurrency['payment_lifetime'],
            $cryptoCurrency['decimal'],
            $this->_scopeConfig->getValue('payment/ezdefi_payment/variation'));

        $this->_payment = $this->_gatewayHelper->createPayment([
            'uoid'     => $orderId.'-1',
            'amountId' => true,
            'value'    => $amountId,
            'to'       => $cryptoCurrency['wallet_address'],
            'currency' => $cryptoCurrency['symbol'].':'.$cryptoCurrency['symbol'],
            'safedist' => $cryptoCurrency['block_confirmation'],
//            'callback' => $this->_urlInterface->getUrl()
            'callback' => 'http://ezdefi-magento2.lan/ezdefi/Frontend/CallbackConfirmOrder'
        ]);
    }

    public function getPaymentId(){
        return __($this->_payment->_id);
    }

    public function getOriginCurrency()
    {
        return __($this->_payment->originCurrency);
    }

    public function getOriginValue()
    {
        return __($this->originValue);
    }

    public function getCryptoCurrency()
    {
        return __($this->_payment->currency);
    }

    public function getCryptoValue()
    {
        return __($this->_payment->originValue);
    }

    public function getGatewayQrCode() {
        return __($this->_payment->qr);
    }

    public function getExpiration() {
        return __($this->_payment->expiredTime);
    }

    public function getWalletAddress() {
        return __($this->_payment->to);
    }
}