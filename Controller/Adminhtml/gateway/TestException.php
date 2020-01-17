<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Gateway;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;

class TestException extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_exceptionFactory;
    protected $_date;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ExceptionFactory $exceptionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_date = $date;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $exeptions = $this->_exceptionFactory->create()->getCollection()->getData();

        $date = $this->_date->gmtDate('Y-m-d H:i:s', strtotime('+86400 second'));

        $response->setData($date);

        return $response;
    }
}
