<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Exception;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class AssignOrder extends \Magento\Backend\App\Action
{
    protected $_pageFactory;

    protected $_exceptionFactory;
    protected $_urlBuilder;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $pageFactory,
        ExceptionFactory $exceptionFactory,
        UrlInterface $urlBuilder
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_urlBuilder = $urlBuilder;
        return parent::__construct($context);
    }

    public function execute()
    {
        $orderIdToAssign = (int) $this->getRequest()->getParam('order_id');

        $exceptionId = (int) $this->getRequest()->getParam('id');
        $exception = $this->_exceptionFactory->create()->load($exceptionId);
        $exception->setData('order_assigned', $orderIdToAssign);
        $exception->setData('confirmed', 1);
        $exception->save();

        if($exception['order_id']  && $orderIdToAssign  != $exception['order_id']) {
            $this->setStatusForOrder($exception['order_id'], 'new', 'pending');
        }
        $this->setStatusForOrder($orderIdToAssign, Order::STATE_PROCESSING, Order::STATE_PROCESSING);

        $exceptionsToDelete = $this->_exceptionFactory->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $orderIdToAssign);
        foreach ($exceptionsToDelete as $exceptionToDelete) {
            $exceptionToDelete->setData('confirmed', 2);
            $exceptionToDelete->save();
        }


        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }


    private function setStatusForOrder($orderId, $state, $status) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $orderState = $state;
        $order->setState($orderState)->setStatus($status);
        $order->save();
    }
}
