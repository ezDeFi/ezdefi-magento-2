<?php

namespace Ezdefi\Payment\Controller\Frontend;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Ezdefi\Payment\Helper\GatewayHelper;
use Magento\Sales\Model\Order;
use \Ezdefi\Payment\Model\ExceptionFactory;

class CallbackConfirmOrder extends \Magento\Framework\App\Action\Action
{
    protected $_request;
    protected $_gatewayHelper;
    protected $_exceptionFactory;
    protected $_date;
    protected $_logger;

    CONST PAY_ON_TIME   = 1;
    CONST PAID_OUT_TIME = 2;

    public function __construct(
        Context $context,
        GatewayHelper $gatewayHelper,
        ExceptionFactory $exceptionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_gatewayHelper    = $gatewayHelper;
        $this->_request          = $request;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_date             = $date;
        $this->_logger           = $logger;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response  = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $paymentId = $this->_request->getParam('paymentid');

        if ($paymentId) {
            $this->_logger->debug('payment id:' . $this->_request->getParam('paymentid'));
            $payment = $this->_gatewayHelper->checkPaymentComplete($paymentId);
            $uoid        = $payment['uoid'];
            $orderId     = explode('-', $uoid)[0];
            $hasAmountId = explode('-', $uoid)[1];

            if ($payment['status'] == 'DONE') {
                $message = 'Payment ID: ' . $paymentId . '<br> 
                            Status: ' . $payment['status'] . '<br>
                            Use Ezdefi Wallet: ' . ($hasAmountId ? 'false' : 'true').'<br>
                            Tx: '.($payment['explorer_url'] ? $payment['explorer_url'] : 'none');
                if ($hasAmountId == 1) {
                    $exceptionCollection = $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                    $exception           = $exceptionCollection->getFirstItem();
                    $exception->setData('paid', self::PAY_ON_TIME);
                    $exception->setData('explorer_url', $payment['explorer_url']);
                    $exception->save();
                    $this->deleteExceptionByOrderId($orderId, $payment['_id']);
                } else {
                    $this->deleteExceptionByOrderId($orderId);
                }
                $response->setData(['order_success' => $this->setProcessingForOrder($orderId, $message)]);
            }
            if ($payment['status'] == 'EXPIRED_DONE') {
                $exceptionCollection = $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                $exception           = $exceptionCollection->getFirstItem();
                $exception->setData('paid', self::PAID_OUT_TIME);
                $exception->setData('explorer_url', $payment['explorer_url']);
                $exception->save();
                $response->setData(['order_success' => 'expired done']);

                $this->deleteExceptionByOrderId($orderId, $payment['_id']);
            }
        } else {
            $this->_logger->debug('transaction id:' . $this->_request->getParam('id'));
            $transactionId = $this->_request->getParam('id');
            $explorerUrl   = $this->_request->getParam('explorerUrl');

            $transaction   = $this->_gatewayHelper->getTransaction($transactionId, $explorerUrl);
            $valueResponse = $transaction->value * pow(10, -$transaction->decimal);

            if ($transaction->status === 'ACCEPTED') {
                $this->addException($transaction->currency, $valueResponse, $transaction->explorerUrl);
            }
            $response->setData(['order_success' => 'unknown transaction']);
        }
        return $response;
    }

    private function deleteExceptionByOrderId($orderId, $paymentId = null) {
        $collection =$this->_exceptionFactory->create()->getCollection()
            ->addFieldToFilter('order_id', $orderId);

        if($paymentId) {
            $collection->addFieldToFilter('payment_id', array('neq' => $paymentId));
        }
        $collection->walk('delete');
    }

    private function addException($cryptoCurrency, $valueResponse, $exploreUrl)
    {
        $exceptionModel = $this->_exceptionFactory->create();
        $exceptionModel->addData([
            'payment_id'   => null,
            'order_id'     => null,
            'currency'     => $cryptoCurrency,
            'amount_id'    => $valueResponse,
            'expiration'   => $this->_date->gmtDate(),
            'paid'         => 3,
            'has_amount'   => 1,
            'explorer_url' => $exploreUrl
        ]);
        $exceptionModel->save();
    }

    private function setProcessingForOrder($orderId, $message)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order         = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $orderState    = Order::STATE_PROCESSING;
        $order->setState($orderState)->setStatus(Order::STATE_PROCESSING);
        $history = $order->addStatusHistoryComment($message, false);
        $history->setIsCustomerNotified(true);
        $order->save();
        return 'true';
    }
}
