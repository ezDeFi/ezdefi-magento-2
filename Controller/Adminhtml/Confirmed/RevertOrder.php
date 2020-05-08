<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Confirmed;

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

        if($exception['order_id'] && $exception['order_assigned'] != $exception['order_id'] && $exception['paid'] == 1) {
            $this->setStatusForOrder($exception['order_id'], Order::STATE_PROCESSING, Order::STATE_PROCESSING);
        }
        $this->setStatusForOrder($exception['order_assigned'], 'new', 'pending');



        if(!$exception['explorer_url']) {
            $exceptionsToUpdate =  $this->_exceptionFactory->create()->getCollection()
                ->addFieldToFilter('order_id', $exception['order_id']);
            foreach ($exceptionsToUpdate as $exceptionToUpdate) {
                $exceptionToUpdate->setData('confirmed', 0);
                $exceptionToUpdate->save();
            }
        } else {
            $exceptionsToUpdate = $this->_exceptionFactory->create()->getCollection()
                ->addFieldToFilter(['id', 'order_id'], [$exception['id'],  $exception['order_assigned']]);
            foreach ($exceptionsToUpdate as $exceptionToUpdate) {
                $exceptionToUpdate->setData('confirmed', 0);
                $exceptionToUpdate->save();
            }
        }

        $exception->setData('order_assigned', NULL);
        $exception->save();

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
