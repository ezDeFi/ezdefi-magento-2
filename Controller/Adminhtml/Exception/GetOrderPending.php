<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Exception;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;
use Magento\Framework\UrlInterface;

class GetOrderPending extends \Magento\Backend\App\Action
{
    protected $_pageFactory;

    protected $_exceptionFactory;
    protected $_urlBuilder;
    protected $_orderCollectionFactory;


    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ExceptionFactory $exceptionFactory,
        UrlInterface $urlBuilder,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->_orderCollectionFactory = $orderCollectionFactory;

        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $orders = $this->_orderCollectionFactory->create()->addAttributeToSelect('customer_email')
            ->addFieldToSelect(['id'=>'entity_id'])
            ->addAttributeToSelect('customer_firstname')
            ->addAttributeToSelect('customer_lastname')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('total_due')
            ->addAttributeToSelect('order_currency_code')
            ->addFieldToFilter('status', 'pending')->getData();

        $response->setData(['data' => $orders, 'status' => 'success']);

        return $response;
    }
}
