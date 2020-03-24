<?php

namespace Ezdefi\Payment\Controller\Frontend;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Ezdefi\Payment\Helper\GatewayHelper;
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
    protected $_orderRepo;
    protected $_urlInterface;
    protected $_exceptionFactory;
    protected $_date;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
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
        $this->_pageFactory      = $pageFactory;
        $this->_cart             = $cart;
        $this->_scopeConfig      = $scopeConfig;
        $this->_gatewayHelper    = $gatewayHelper;
        $this->_request          = $request;
        $this->_orderRepo        = $orderRepo;
        $this->_urlInterface     = $urlInterface;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_date             = $date;
        return parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->_cart->getLastOrderId();
        $coinId  = json_decode($this->_request->getContent())->coin_id;
//        $cryptoCurrency    = $this->_currencyFactory->create()->getCollection()->addFieldToFilter('currency_id', $currencyId)->getData()[0];
        $cryptoCurrency = $this->_gatewayHelper->getCurrency($coinId);
        $discount = (float)number_format((100 - $cryptoCurrency['discount']) / 100, 6);

        $order = $this->_orderRepo->get($orderId);

        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));
        $paymentType = json_decode($this->_request->getContent())->type;

        if ($paymentType === 'simple') {
            $payment = $this->createPaymentSimple($order, $coinId, $cryptoCurrency);
            $block   = $resultPage->getLayout()
                ->createBlock('Ezdefi\Payment\Block\Frontend\SimpleMethod', 'render simple method block', [
                    'data' => [
                        'payment'        => $payment,
                        'originValue'    => $order->getTotalDue() * $discount,
                        'originCurrency' => $order->getOrderCurrencyCode()
                    ]
                ])
                ->setTemplate('Ezdefi_Payment::simpleMethod.phtml')
                ->toHtml();
        } else if ($paymentType === 'ezdefi') {
            $payment = $this->createPaymentEzdefi($order, $coinId, $cryptoCurrency);

            $block = $resultPage->getLayout()
                ->createBlock('Ezdefi\Payment\Block\Frontend\EzdefiMethod', 'render simple method block', ['data' => ['payment' => $payment, 'originValue' => $order->getTotalDue()]])
                ->setTemplate('Ezdefi_Payment::ezdefiMethod.phtml')
                ->toHtml();
        }

        $this->getResponse()->setBody($block);
    }

    private function createPaymentSimple($order, $coinId, $cryptoCurrency)
    {
        $originCurrency = $order->getStoreCurrencyCode();
        $originValue    = $order->getTotalDue();
        $amount         = round($this->_gatewayHelper->getExchange($originCurrency, $cryptoCurrency['token']['symbol']) * $originValue * (100 - $cryptoCurrency['discount']) / 100, $cryptoCurrency['decimal']);

        $payment = $this->_gatewayHelper->createPayment([
            'uoid'     => $order->getId() . '-1',
            'amountId' => true,
            'coinId'   => $coinId,
            'value'    => $amount,
            'to'       => $cryptoCurrency['walletAddress'],
            'currency' => $cryptoCurrency['token']['symbol'] . ':' . $cryptoCurrency['token']['symbol'],
            'safedist' => $cryptoCurrency['blockConfirmation'],
            'duration' => $cryptoCurrency['expiration'] * 60,
            'callback' => $this->_urlInterface->getUrl('ezdefi/frontend/callbackconfirmorder')
        ]);
        $this->addException($order, $cryptoCurrency, $payment->_id, $payment->value * pow(10, -$payment->decimal), 1);

        return $payment;
    }

    private function createPaymentEzdefi($order, $coinId, $cryptoCurrency)
    {
        $discount = (float)number_format((100 - $cryptoCurrency['discount']) / 100, 6);

        $payment = $this->_gatewayHelper->createPayment([
            'uoid'     => $order->getId() . '-0',
            'coinId'   => $coinId,
            'value'    => $order['grand_total'] * $discount,
            'to'       => $cryptoCurrency['walletAddress'],
            'currency' => $order['base_currency_code'] . ':' . $cryptoCurrency['token']['symbol'],
            'safedist' => $cryptoCurrency['blockConfirmation'],
            'duration' => $cryptoCurrency['expiration'] * 60,
            'callback' => $this->_urlInterface->getUrl('ezdefi/frontend/callbackconfirmorder')
        ]);

        $cryptoValue = $payment->value * pow(10, -$payment->decimal);
        $this->addException($order, $cryptoCurrency, $payment->_id, $cryptoValue, 0);
        return $payment;
    }

    private function addException($order, $cryptoCurrency, $paymentId, $amountId, $hasAmount)
    {
        $expiration     = $this->_date->gmtDate('Y-m-d H:i:s', strtotime('+' . ($cryptoCurrency['expiration'] * 60) . ' second'));
        $exceptionModel = $this->_exceptionFactory->create();
        $exceptionModel->addData([
            'payment_id' => $paymentId,
            'order_id'   => $order->getId(),
            'currency'   => $cryptoCurrency['token']['symbol'],
            'amount_id'  => $amountId,
            'expiration' => $expiration,
            'paid'       => 0,
            'has_amount' => $hasAmount,
        ]);
        $exceptionModel->save();
    }

}
