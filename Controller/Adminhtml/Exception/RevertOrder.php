<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Exception;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;
use Magento\Framework\UrlInterface;

class RevertOrder extends \Magento\Backend\App\Action
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

    protected function _isAllowed() {
        return true;
    }

    public function execute()
    {
        $exceptionId = (int) $this->getRequest()->getParam('id');

        $exception   = $this->_exceptionFactory->create()->load($exceptionId);

        // set order status to pendding
        $orderId = $exception->getOrderId();
        $this->setPendingForOrder($orderId);

        // set exception to unknown transaction
        $exception->setOrderId(null);
        $exception->setPaid(3);
        $exception->save();

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    private function setPendingForOrder($orderId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $orderState = 'new';
        $order->setState($orderState)->setStatus('pending');
        $order->save();
    }
}
