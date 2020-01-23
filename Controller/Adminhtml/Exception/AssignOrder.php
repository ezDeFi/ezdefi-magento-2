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
        $exceptionOrderId = $exception->getData()['order_id'];

        $this->setProcessingForOrder($orderIdToAssign);
        $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('order_id', $exceptionOrderId)->walk('delete');
        $this->_exceptionFactory->create()->getCollection()->addFieldToFilter('id', $exceptionId)->walk('delete');

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    private function setProcessingForOrder($orderId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $orderState = Order::STATE_PROCESSING;
        $order->setState($orderState)->setStatus(Order::STATE_PROCESSING);
        $order->save();
    }
}
