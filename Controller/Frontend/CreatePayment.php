<?php
namespace Ezdefi\Payment\Controller\Frontend;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Ezdefi\Payment\Helper\GatewayHelper;

class CreatePayment extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_customerSession;

    protected $_cart;

    protected $_scopeConfig;

    protected $_gatewayHelper;

    protected $_request;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        GatewayHelper $gatewayHelper,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Checkout\Model\Session $cart
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $customerSession;
        $this->_cart = $cart;
        $this->_scopeConfig = $scopeConfig;
        $this->_gatewayHelper = $gatewayHelper;
        $this->_request = $request;
        return parent::__construct($context);
    }

    public function execute()
    {
//        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
//
//        $orderId = $this->_cart->getLastOrderId();
//
//        $payment = $this->_gatewayHelper->createPayment(['uoid' => $orderId, 'value' => '111', 'to' => '0x356215E788b06E5d14D182cad28d3ec05d2753D7', 'currency'=>'USD:ETH']);
//
//        $response->setData(json_decode($payment));

//        $response->setData(['test' => $this->_request->getParam("type")]);
//
//        return $response;
//        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));
        $paymentType = json_decode($this->_request->getContent())->type;

        if($paymentType === 'simple') {
            $block = $resultPage->getLayout()
                ->createBlock('Ezdefi\Payment\Block\Frontend\SimpleMethod')
                ->setTemplate('Ezdefi_Payment::simpleMethod.phtml')
                ->toHtml();
        } else if ($paymentType === 'ezdefi') {
            $block = $resultPage->getLayout()
                ->createBlock('Ezdefi\Payment\Block\Frontend\EzdefiMethod')
                ->setTemplate('Ezdefi_Payment::EzdefiMethod.phtml')
                ->toHtml();
        }

        $this->getResponse()->setBody($block);
    }
}
