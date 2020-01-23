<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Gateway;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

class ListCoin extends \Magento\Backend\App\Action
{
    protected $_pageFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $pageFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $curl = new Curl();
        $curl->get("163.172.170.35:3000/api/token/list");
        $tokenList = $curl->getBody();

        $response->setData(json_decode($tokenList));

        return $response;
    }
}
