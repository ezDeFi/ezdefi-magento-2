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

    CONST PAY_ON_TIME = 1;
    CONST PAID_OUT_TIME = 3;

    public function __construct(
        Context $context,
        GatewayHelper $gatewayHelper,
        ExceptionFactory $exceptionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request = $request;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_date = $date;
        $this->_logger = $logger;
        return parent::__construct($context);
    }

    public function execute()
    {
        $this->_logger->debug('have callback');
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $paymentId = $this->_request->getParam('paymentid');

        if ($paymentId) {
            $payment = $this->_gatewayHelper->checkPaymentComplete($paymentId);
            if ($payment['status'] == 'DONE') {
                $uoid = $payment['uoid'];
                $orderId = explode('-', $uoid)[0];
                $hasAmountId = explode('-', $uoid)[1];

                if ($hasAmountId == 1) {
                    $exceptionCollection = $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                    $exception = $exceptionCollection->getFirstItem();
                    $exception->setData('paid', self::PAY_ON_TIME);
                    $exception->setData('explorer_url', $payment['explorer_url']);
                    $exception->save();
                } else {
                    $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('order_id', $orderId)->walk('delete');
                }
                $response->setData(['order_success' => $this->setProcessingForOrder($orderId)]);
            }
            if ($payment['status'] == 'EXPIRED_DONE') {
                $exceptionCollection = $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                $exception = $exceptionCollection->getFirstItem();
                $exception->setData('paid', self::PAID_OUT_TIME);
                $exception->setData('explorer_url', $payment['explorer_url']);
                $exception->save();
            }
        } else {
            $transactionId = $this->_request->getParam('id');
            $explorerUrl = $this->_request->getParam('explorerUrl');

            $transaction = $this->_gatewayHelper->getTransaction($transactionId, $explorerUrl);
            $valueResponse = $transaction->value * pow(10, -$transaction->decimal);

            if ($transaction->status === 'ACCEPTED') {
                $this->addException(null, $transaction->currency, $valueResponse, null, 1, 3, $transaction->explorerUrl);
                $exceptionModel = $this->_exceptionFactory->create();
                $exceptionModel->addData([
                    'payment_id' => null,
                    'order_id' => null,
                    'currency' => $transaction->currency,
                    'amount_id' => $valueResponse,
                    'expiration' => $this->_date->gmtDate(),
                    'paid' => 3,
                    'has_amount' => 1,
                    'explorer_url' => $transaction->explorerUrl
                ]);
                $exceptionModel->save();
            }

        }
        return $response;
    }

    private function setProcessingForOrder($orderId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $orderState = Order::STATE_PROCESSING;
        $order->setState($orderState)->setStatus(Order::STATE_PROCESSING);
        $order->save();
        return 'true';
    }
}
