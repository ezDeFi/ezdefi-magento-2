<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Gateway;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ezdefi\Payment\Model\ExceptionFactory;
use \Ezdefi\Payment\Model\CurrencyFactory;

class TestException extends \Magento\Backend\App\Action
{
    protected $_pageFactory;

    protected $_exceptionFactory;
    protected $_date;
    protected $_currencyFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ExceptionFactory $exceptionFactory,
        CurrencyFactory $currencyFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_exceptionFactory = $exceptionFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_date = $date;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

//        $exeptions = $this->_exceptionFactory->create()->getCollection()->getData();
//
//        $date = $this->_date->gmtDate('Y-m-d H:i:s', strtotime('+86400 second'));

        $currencies = $this->_currencyFactory->create()->getOptions();
//        $ref =[];
//        foreach ($currencies as $currency) {
//            $ref[$currency['symbol']] = $currency['symbol'];
//        }

        $response->setData($currencies);

        return $response;
    }
}
