<?php
namespace Ezdefi\Payment\Controller\Adminhtml\Gateway;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Ezdefi\Payment\Helper\GatewayHelper;
use \Magento\Framework\Webapi\Rest\Request;

class CheckApiKey extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_getawayHelper;
    protected $_request;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        GatewayHelper $getawayHelper,
        Request $request
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_getawayHelper = $getawayHelper;
        $this->_request = $request;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $request = $this->_request->getParams();
        $apikey= $request['groups']['ezdefi_payment']['fields']['api_key']['value'];

        $response->setData($this->_getawayHelper->checkApiKey($apikey));
        return $response;
    }
}
