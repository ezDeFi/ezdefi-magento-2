<?php
namespace Ezdefi\PaymentMethod\Controller\Adminhtml\Gateway;

use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

class ListCoin extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $scope;


    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->scope = $scope;
        return parent::__construct($context);
    }

    public function execute()
    {

        $methodList     = $this->scope->getValue('payment');

        echo "<pre>";
        print_r($methodList);die;


        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $curl = new Curl();
        $curl->get("163.172.170.35:3000/api/token/list");
        $tokenList = $curl->getBody();

        $response->setData(json_decode($tokenList));

        return $response;
    }
}
