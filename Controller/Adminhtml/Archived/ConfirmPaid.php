<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Archived;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class ConfirmPaid extends \Magento\Backend\App\Action
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
        $exceptionId = (int) $this->getRequest()->getParam('id');
        $exception = $this->_exceptionFactory->create()->load($exceptionId);
        $exception->setData('order_assigned',$exception['order_id']);
        $exception->save();

        $orderId = $exception->getData()['order_id'];
        $this->setStatusForOrder($orderId, Order::STATE_PROCESSING, Order::STATE_PROCESSING);

        $exceptionsToUpdate = $this->_exceptionFactory->create()->getCollection()
            ->addFieldToFilter('order_id', $orderId);
        foreach ($exceptionsToUpdate as $exceptionToUpdate) {
            $exceptionToUpdate->setData('confirmed', 1);
            $exceptionToUpdate->save();
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
