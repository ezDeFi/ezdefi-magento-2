<?php
namespace Ezdefi\Payment\Block\Frontend;

use Ezdefi\Payment\Helper\GatewayHelper;
use \Ezdefi\Payment\Model\CurrencyFactory;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\UrlInterface;

class EzdefiMethod extends \Magento\Framework\View\Element\Template
{
    protected $_cart;
    protected $_gatewayHelper;
    protected $_payment;
    protected $_currencyFactory;
    protected $_orderRepo;
    protected $_urlInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $cart,
        OrderRepositoryInterface $orderRepo,
        CurrencyFactory $currencyFactory,
        UrlInterface $urlInterface,
        GatewayHelper $gatewayHelper
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_cart = $cart;
        $this->_orderRepo = $orderRepo;
        $this->_currencyFactory = $currencyFactory;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context);
    }

    public function _construct() {
        $orderId = $this->_cart->getLastOrderId();

        $currencyId = json_decode($this->_request->getContent())->currency_id;

        $cryptoCurrency    = $this->_currencyFactory->create()->getCollection()->addFieldToFilter('currency_id', $currencyId)->getData()[0];
        $order             = $this->_orderRepo->get($orderId);

        $this->_payment = $this->_gatewayHelper->createPayment([
            'uoid'     => $orderId.'-0',
//            'value'    => $order->getTotalDue(),
            'value'    => 0.1,
            'to'       => $cryptoCurrency['wallet_address'],
            'currency' => $order->getStoreCurrencyCode().':'.$cryptoCurrency['symbol'],
            'safedist' => $cryptoCurrency['block_confirmation'],
            'callback' => $this->_urlInterface->getUrl()
        ]);
    }

    public function getOriginCurrency()
    {
        return __($this->_payment->originCurrency);
    }

    public function getOriginValue()
    {
        return __($this->_payment->originValue);
    }

    public function getCryptoCurrency()
    {
        return __($this->_payment->currency);
    }

    public function getCryptoValue()
    {
        return __($this->_payment->value);
    }

    public function getQrCode() {
        return __($this->_payment->qr);
    }

    public function getExpiration() {
        return __($this->_payment->expiredTime);
    }
}