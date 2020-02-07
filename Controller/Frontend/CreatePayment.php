<?php
namespace Ezdefi\Payment\Controller\Frontend;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Ezdefi\Payment\Model\AmountFactory;
use \Ezdefi\Payment\Model\CurrencyFactory;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Framework\Webapi\Rest\Request;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\UrlInterface;
use \Ezdefi\Payment\Model\ExceptionFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;

class CreatePayment extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_cart;
    protected $_scopeConfig;
    protected $_gatewayHelper;
    protected $_request;
    protected $_amountFactory;
    protected $_currencyFactory;
    protected $_orderRepo;
    protected $_urlInterface;
    protected $originValue;
    protected $_exceptionFactory;
    protected $_date;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        AmountFactory $amountFactory,
        CurrencyFactory $currencyFactory,
        OrderRepositoryInterface $orderRepo,
        Session $cart,
        Request $request,
        ScopeConfigInterface $scopeConfig,
        GatewayHelper $gatewayHelper,
        UrlInterface $urlInterface,
        ExceptionFactory $exceptionFactory,
        DateTime $date
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_cart = $cart;
        $this->_scopeConfig = $scopeConfig;
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request = $request;
        $this->_orderRepo = $orderRepo;
        $this->_amountFactory = $amountFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_urlInterface = $urlInterface;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_date = $date;
        return parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->_cart->getLastOrderId();
        $currencyId = json_decode($this->_request->getContent())->currency_id;
        $cryptoCurrency    = $this->_currencyFactory->create()->getCollection()->addFieldToFilter('currency_id', $currencyId)->getData()[0];
        $order             = $this->_orderRepo->get($orderId);

        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));
        $paymentType = json_decode($this->_request->getContent())->type;

        if($paymentType === 'simple') {
            $payment = $this->createPaymentSimple($order, $cryptoCurrency);
            $block = $resultPage->getLayout()
                ->createBlock('Ezdefi\Payment\Block\Frontend\SimpleMethod', 'render simple method block', [
                        'data' => [
                            'payment'        => $payment,
                            'originValue'    => $order->getTotalDue() * (100 - $cryptoCurrency['discount']) / 100,
                            'originCurrency' => $order->getOrderCurrencyCode()]])
                ->setTemplate('Ezdefi_Payment::simpleMethod.phtml')
                ->toHtml();
        } else if ($paymentType === 'ezdefi') {
            $payment = $this->createPaymentEzdefi($order, $cryptoCurrency);

            $block = $resultPage->getLayout()
                ->createBlock('Ezdefi\Payment\Block\Frontend\EzdefiMethod', 'render simple method block', ['data' => ['payment' => $payment, 'originValue' => $order->getTotalDue()]])
                ->setTemplate('Ezdefi_Payment::ezdefiMethod.phtml')
                ->toHtml();
        }

        $this->getResponse()->setBody($block);
    }

    private function createPaymentSimple($order, $cryptoCurrency) {
        $amountCollection  = $this->_amountFactory->create();
        $originCurrency    = $order->getStoreCurrencyCode();
        $originValue = $order->getTotalDue();
        $amount            = round($this->_gatewayHelper->getExchange($originCurrency, $cryptoCurrency['symbol']) * $originValue * (100 - $cryptoCurrency['discount'])/100, $cryptoCurrency['decimal']);

        $amountId = (float)$amountCollection->getCollection()->createAmountId(
            $cryptoCurrency['symbol'], (float)$amount,
            $cryptoCurrency['payment_lifetime'],
            $cryptoCurrency['decimal'],
            $this->_scopeConfig->getValue('payment/ezdefi_payment/variation'));

        if(!$amountId) {
            return false;
        }

        $payment = $this->_gatewayHelper->createPayment([
            'uoid'     => $order->getId().'-1',
            'amountId' => true,
            'value'    => $amountId,
            'to'       => $cryptoCurrency['wallet_address'],
            'currency' => $cryptoCurrency['symbol'].':'.$cryptoCurrency['symbol'],
            'safedist' => $cryptoCurrency['block_confirmation'],
            'duration' => $cryptoCurrency['payment_lifetime'],
            'callback' => $this->_urlInterface->getUrl('ezdefi/frontend/callbackconfirmorder')
        ]);
        $this->addException($order, $cryptoCurrency, $payment->_id, $amountId, 1);
        return $payment;
    }

    private function createPaymentEzdefi($order, $cryptoCurrency) {
        $payment = $this->_gatewayHelper->createPayment([
            'uoid'     => $order->getId().'-0',
            'value'    => $order->getTotalDue() * (100 - $cryptoCurrency['discount'])/100,
            'to'       => $cryptoCurrency['wallet_address'],
            'currency' => $order->getStoreCurrencyCode().':'.$cryptoCurrency['symbol'],
            'safedist' => $cryptoCurrency['block_confirmation'],
            'duration' => $cryptoCurrency['payment_lifetime'],
            'callback' => $this->_urlInterface->getUrl('ezdefi/frontend/callbackconfirmorder')
        ]);

        $exchangeRate = $this->_gatewayHelper->getExchange($order->getStoreCurrencyCode(), $cryptoCurrency['symbol']);
        $this->addException($order, $cryptoCurrency, $payment->_id, $exchangeRate * $order->getTotalDue(), 0);
        return $payment;
    }

    private function addException($order, $cryptoCurrency, $paymentId, $amountId, $hasAmount) {
        $expiration = $this->_date->gmtDate('Y-m-d H:i:s', strtotime('+'.$cryptoCurrency['payment_lifetime'].' second'));
        $exceptionModel = $this->_exceptionFactory->create();
        $exceptionModel->addData([
            'payment_id' => $paymentId,
            'order_id' => $order->getId(),
            'currency' => $cryptoCurrency['symbol'],
            'amount_id' => $amountId,
            'expiration' => $expiration,
            'paid' => 0,
            'has_amount' => $hasAmount,
        ]);
        $exceptionModel->save();
    }

}
