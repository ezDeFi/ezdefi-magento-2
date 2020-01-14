<?php
namespace Ezdefi\Payment\Controller\Frontend;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Ezdefi\Payment\Helper\GatewayHelper;
use Magento\Sales\Model\Order;

class CallbackConfirmOrder extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_customerSession;

    protected $_cart;

    protected $_scopeConfig;

    protected $_request;

    protected $_gatewayHelper;

    public function __construct(
        Context $context,
        GatewayHelper $gatewayHelper,
        \Magento\Framework\Webapi\Rest\Request $request
    )
    {
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request = $request;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $paymentId = $this->_request->getParam('paymentid');

        if ($paymentId) {
            $payment = $this->_gatewayHelper->checkPaymentComplete($paymentId);


            if ($payment['status'] == 'DONE') {
                $uoid = $payment['uoid'];
                $orderId = explode('-', $uoid)[0];
                $hasAmountId = explode('-', $uoid)[1];

                if ($hasAmountId == 1) {
//                    $this->model_extension_payment_ezdefi->setPaidForException($payment['_id'], self::PAID_IN_TIME, $payment['explorer_url']);
                } else {
//                    $this->model_extension_payment_ezdefi->deleteExceptionByOrderId($order_id);
                }
                $response->setData(['order_success' => $this->setProcessingForOrder($orderId)]);
            }
            if ($payment['status'] == 'EXPIRED_DONE') {
//                $this->model_extension_payment_ezdefi->setPaidForException($payment['_id'], self::PAID_OUT_TIME, $payment['explorer_url']);
            }
        } else {
//            $transaction_id =  $this->request->get['id'];
//            $explorer_url = $this->request->get['explorerUrl'];
//
//            $this->model_extension_payment_ezdefi->checkTransaction($transaction_id, $explorer_url);
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
